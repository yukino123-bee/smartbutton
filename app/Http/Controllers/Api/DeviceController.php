<?php

namespace App\Http\Controllers\Api;

use App\Events\EmergencyReported;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Incident;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeviceController extends Controller
{
    // How long after a medical alert the clinic button can still add the Clinic to it.
    private const CLINIC_REQUEST_WINDOW_MINUTES = 60;

    // A different button pressed this soon after an unacknowledged alert replaces it (wrong button pressed first).

    /**
     * Handle incoming emergency alerts from ESP32 devices.
     */
    public function emergency(Request $request)
    {
        // Validate incoming payload
        $validated = $request->validate([
            'device_id' => 'required|string',
            'emergency_category' => ['required', 'string', Rule::in(Incident::EMERGENCY_TYPES)],
            'need_clinic' => ['nullable', 'boolean'],
            // The "no clinic needed" button (D33) on an open Medical alert.
            'clinic_declined' => ['nullable', 'boolean'],
            // One id per physical press; the device reuses it when it retries a send.
            'press_id' => ['nullable', 'string', 'max:40'],
            'timestamp' => ['nullable', 'date', 'before_or_equal:now'],
            // Buttons have no clock: an alert that went out by SMS only is re-sent with its age once the server is back.
            'age_seconds' => ['nullable', 'integer', 'between:0,86400'],
        ]);

        $deviceCode = $validated['device_id'];
        $emergencyType = $validated['emergency_category'];

        // Find the device
        $device = Device::where('device_code', $deviceCode)->first();

        if (! $device) {
            return response()->json([
                'status' => 'error',
                'message' => 'Device not found.',
            ], 404);
        }

        if ($device->status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Device is not active.',
            ], 409);
        }

        // Update device last_seen status
        $device->update(['last_seen' => now()]);

        // Medical alerts go to DRRMO only; the Clinic is added later by a clinic request (need_clinic = true).
        $defaultNeedClinic = $emergencyType === Incident::TYPE_CRITICAL;
        $needClinic = $request->has('need_clinic') ? $request->boolean('need_clinic') : $defaultNeedClinic;

        // D33: the witness says no clinic is needed on the open Medical alert. Nothing new is created.
        if ($emergencyType === Incident::TYPE_MEDICAL && $request->boolean('clinic_declined')) {
            $openMedical = Incident::where('device_id', $device->id)
                ->where('emergency_type', Incident::TYPE_MEDICAL)
                ->where('status', '!=', 'Resolved')
                ->where('created_at', '>=', now()->subMinutes(self::CLINIC_REQUEST_WINDOW_MINUTES))
                ->latest('id')
                ->first();

            if ($openMedical && ! $openMedical->need_clinic) {
                $openMedical->update(['clinic_declined_at' => $openMedical->clinic_declined_at ?? now(), 'clinic_requested_at' => null]);
            }

            return response()->json([
                'status' => $openMedical ? 'success' : 'ignored',
                'message' => $openMedical ? 'No clinic needed recorded.' : 'No open medical alert for this device.',
                'incident_id' => $openMedical?->id,
            ], 200);
        }

        // The clinic button after a medical alert adds the Clinic to that open alert instead of making a new one.
        if ($emergencyType === Incident::TYPE_MEDICAL && $needClinic) {
            $openMedical = Incident::where('device_id', $device->id)
                ->where('emergency_type', Incident::TYPE_MEDICAL)
                ->where('status', '!=', 'Resolved')
                ->where('created_at', '>=', now()->subMinutes(self::CLINIC_REQUEST_WINDOW_MINUTES))
                ->latest('id')
                ->first();

            if ($openMedical) {
                // Held until DRRMO acknowledges; alerts the Clinic at once if DRRMO already has.
                $alerted = $openMedical->requestClinic();

                return response()->json([
                    'status' => 'success',
                    'message' => $alerted
                        ? 'Clinic alerted on the open medical alert.'
                        : 'Clinic request recorded; the Clinic is alerted once DRRMO acknowledges.',
                    'incident_id' => $openMedical->id,
                    'need_clinic' => $alerted,
                    'clinic_request_pending' => ! $alerted,
                ], 200);
            }

            // The clinic button only follows a Medical alert. With none open, nothing is created or shown.
            return response()->json([
                'status' => 'ignored',
                'message' => 'No open medical alert for this device; press the Medical button first.',
                'ignored' => true,
            ], 200);
        }

        // A retry of the same press is not a new alert. Devices that send a press_id get every press as a new alert;
        // without one (older firmware, simulator) a same-button repeat within 30 s is treated as a retry.
        $pressId = $validated['press_id'] ?? null;
        $duplicate = $pressId
            ? Incident::where('device_id', $device->id)->where('press_id', $pressId)->first()
            : Incident::where('device_id', $device->id)
                ->where('emergency_type', $emergencyType)
                ->where('need_clinic', $needClinic)
                ->where('status', '!=', 'Resolved')
                ->where('created_at', '>=', now()->subSeconds(30))
                ->latest('id')
                ->first();

        if ($duplicate) {
            return response()->json([
                'status' => 'success',
                'message' => 'Duplicate alert ignored; existing incident is still active.',
                'incident_id' => $duplicate->id,
                'need_clinic' => (bool) $duplicate->need_clinic,
                'duplicate' => true,
            ], 200);
        }

        // A new alert from the device replaces its earlier alert that DRRMO has not acted on yet.
        Incident::where('device_id', $device->id)
            ->where('status', 'Pending')
            ->get()
            ->each(fn (Incident $old) => $old->supersedeAsFalseAlarm($emergencyType));

        $remarks = null;
        if ($emergencyType === Incident::TYPE_MEDICAL) {
            $remarks = $needClinic
                ? 'Medical Emergency (Witness requested Clinic Aid)'
                : 'Medical Emergency (Witness indicated No Clinic Aid needed)';
        }

        // Create incident record
        $incident = Incident::create([
            'device_id' => $device->id,
            'emergency_type' => $emergencyType,
            'need_clinic' => $needClinic,
            'press_id' => $pressId,
            'reported_at' => $validated['timestamp'] ?? now()->subSeconds($validated['age_seconds'] ?? 0),
            'status' => 'Pending',
            'remarks' => isset($validated['age_seconds']) ? trim($remarks.' [Sent by SMS while offline, synced later]') : $remarks,
        ]);

        // Create notifications log for DRRMO
        Notification::create([
            'incident_id' => $incident->id,
            'recipient' => 'DRRMO',
            'channel' => 'Dashboard',
            'status' => 'Delivered',
            'sent_at' => now(),
        ]);

        // Notify Clinic if Critical, or if Medical AND need_clinic is true
        if ($emergencyType === Incident::TYPE_CRITICAL || ($emergencyType === Incident::TYPE_MEDICAL && $needClinic)) {
            Notification::create([
                'incident_id' => $incident->id,
                'recipient' => 'Clinic',
                'channel' => 'Dashboard',
                'status' => 'Delivered',
                'sent_at' => now(),
            ]);
        }

        // Broadcast the event to WebSockets
        broadcast(new EmergencyReported($incident));

        return response()->json([
            'status' => 'success',
            'message' => 'Emergency alert received and processed.',
            'incident_id' => $incident->id,
            'need_clinic' => $needClinic,
        ], 201);
    }

    /**
     * Return status of pending incidents for an ESP32 device.
     */
    public function status(Request $request)
    {
        $validated = $request->validate(['device_id' => ['required', 'string']]);
        $deviceCode = $validated['device_id'];

        $device = Device::where('device_code', $deviceCode)->first();
        if (! $device) {
            return response()->json(['status' => 'error', 'message' => 'Device not found.'], 404);
        }

        $device->update(['last_seen' => now()]);

        $pendingIncident = Incident::where('device_id', $device->id)
            ->where('status', 'Pending')
            ->latest('reported_at')
            ->first();

        $latestIncident = Incident::where('device_id', $device->id)
            ->latest('reported_at')
            ->first();

        $drrmoResponded = false;
        if ($latestIncident && in_array($latestIncident->status, ['Acknowledged', 'Responding', 'Resolved'], true)) {
            $drrmoResponded = true;
        }

        return response()->json([
            'device_code' => $deviceCode,
            'has_pending' => (bool) $pendingIncident,
            'status' => $pendingIncident ? 'pending' : ($latestIncident ? strtolower($latestIncident->status) : 'normal'),
            'incident_id' => $pendingIncident ? $pendingIncident->id : ($latestIncident ? $latestIncident->id : null),
            'incident_status' => $latestIncident ? $latestIncident->status : null,
            'drrmo_responded' => $drrmoResponded,
        ]);
    }

    /**
     * Handle incoming SMS backup messages from GSM Module via Webhook.
     */
    public function smsWebhook(Request $request)
    {
        // Example parsed payload from GSM webhook:
        // { "sender": "+639123456789", "message": "EMERGENCY: GYM-001 - Critical Emergency" }

        $validated = $request->validate([
            'sender' => 'required|string',
            'message' => 'required|string',
        ]);

        $message = $validated['message'];

        // Two formats: "EMERGENCY: DEVICE_ID - CATEGORY", or the firmware's own text
        // "...ALERT...\nDevice: DEVICE_ID (place)\nCategory: CATEGORY\nClinic Aid: REQUIRED".
        $deviceCode = $emergencyType = null;
        $clinicAid = null;

        if (str_contains($message, 'EMERGENCY:')) {
            $parts = explode(' - ', str_replace('EMERGENCY: ', '', $message));
            if (count($parts) === 2) {
                [$deviceCode, $emergencyType] = array_map('trim', $parts);
            }
        } elseif (preg_match('/^\s*Device:\s*(\S+)/mi', $message, $d) && preg_match('/^\s*Category:\s*(.+?)\s*$/mi', $message, $c)) {
            $deviceCode = $d[1];
            $emergencyType = $c[1];
            if (preg_match('/^\s*Clinic Aid:\s*(REQUIRED|NOT REQUIRED)/mi', $message, $a)) {
                $clinicAid = strtoupper($a[1]) === 'REQUIRED';
            }
        }

        if (! $deviceCode || ! $emergencyType) {
            return response()->json(['status' => 'ignored', 'message' => 'Not a valid emergency format'], 200);
        }

        if (! in_array($emergencyType, Incident::EMERGENCY_TYPES, true)) {
            return response()->json(['status' => 'ignored', 'message' => 'Unknown emergency category'], 200);
        }

        $device = Device::where('device_code', $deviceCode)->first();

        if (! $device) {
            return response()->json(['status' => 'ignored', 'message' => 'Unknown device'], 200);
        }

        // Some buttons send the SMS even when the API call worked; that is the same alert.
        $duplicate = Incident::where('device_id', $device->id)
            ->where('emergency_type', $emergencyType)
            ->where('status', '!=', 'Resolved')
            ->where('created_at', '>=', now()->subSeconds(60))
            ->exists();

        if ($duplicate) {
            return response()->json(['status' => 'success', 'message' => 'Duplicate of an alert already received.', 'duplicate' => true], 200);
        }

        // Critical always includes the Clinic; Medical only when the witness asked (or the old format, which cannot say).
        $needClinic = $emergencyType === Incident::TYPE_CRITICAL
            || ($emergencyType === Incident::TYPE_MEDICAL && ($clinicAid ?? true));

        $incident = Incident::create([
            'device_id' => $device->id,
            'emergency_type' => $emergencyType,
            'need_clinic' => $needClinic,
            'reported_at' => now(),
            'status' => 'Pending',
            'remarks' => 'Received via SMS Backup ('.$validated['sender'].')',
        ]);

        foreach (array_filter(['DRRMO', $needClinic ? 'Clinic' : null]) as $recipient) {
            Notification::create([
                'incident_id' => $incident->id,
                'recipient' => $recipient,
                'channel' => 'SMS Backup',
                'status' => 'Delivered',
                'sent_at' => now(),
            ]);
        }

        // Webhook calls have no socket to exclude, so toOthers() is not needed.
        broadcast(new EmergencyReported($incident));

        return response()->json(['status' => 'success', 'message' => 'SMS emergency processed'], 201);
    }
}
