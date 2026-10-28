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
            'timestamp' => ['nullable', 'date', 'before_or_equal:now'],
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

        // A repeat press of the same button within seconds is the same alert, not a new incident.
        $duplicate = Incident::where('device_id', $device->id)
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
            'reported_at' => $validated['timestamp'] ?? now(),
            'status' => 'Pending',
            'remarks' => $remarks,
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
     * Acknowledge an incident via API.
     */
    public function acknowledge($incident)
    {
        $incidentModel = Incident::find($incident);
        if (! $incidentModel) {
            $incidentModel = Incident::findOrFail($incident);
        }

        abort_unless($incidentModel->status === 'Pending', 409, 'Only pending incidents can be acknowledged.');
        $incidentModel->update(['status' => 'Acknowledged']);
        $incidentModel->releaseClinicRequest();

        return response()->json([
            'status' => 'success',
            'message' => 'Emergency alert acknowledged.',
            'incident_id' => $incidentModel->id,
        ]);
    }

    public function dispatch($incident)
    {
        $incidentModel = Incident::findOrFail($incident);
        abort_unless(in_array($incidentModel->status, ['Pending', 'Acknowledged'], true), 409, 'Incident cannot be dispatched from its current status.');
        $incidentModel->update([
            'status' => 'Responding',
        ]);
        $incidentModel->releaseClinicRequest();

        return response()->json([
            'status' => 'success',
            'message' => 'Responders dispatched.',
            'incident_id' => $incidentModel->id,
        ]);
    }

    public function resolve($incident)
    {
        $incidentModel = Incident::findOrFail($incident);
        abort_if($incidentModel->status === 'Resolved', 409, 'Incident is already resolved.');
        $incidentModel->update([
            'status' => 'Resolved',
            'resolved_at' => now(),
        ]);
        $incidentModel->resolveClinicSide('Resolved', null, null);

        return response()->json([
            'status' => 'success',
            'message' => 'Incident marked resolved and recorded.',
            'incident_id' => $incidentModel->id,
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

        // Very basic parsing for demo
        if (str_contains($message, 'EMERGENCY:')) {
            // Extract Device ID and Category
            // Format: "EMERGENCY: DEVICE_ID - CATEGORY"
            $parts = explode(' - ', str_replace('EMERGENCY: ', '', $message));

            if (count($parts) === 2) {
                $deviceCode = trim($parts[0]);
                $emergencyType = trim($parts[1]);

                if (! in_array($emergencyType, Incident::EMERGENCY_TYPES, true)) {
                    return response()->json(['status' => 'ignored', 'message' => 'Unknown emergency category'], 200);
                }

                $device = Device::where('device_code', $deviceCode)->first();

                if ($device) {
                    $incident = Incident::create([
                        'device_id' => $device->id,
                        'emergency_type' => $emergencyType,
                        'status' => 'Pending',
                        'remarks' => 'Received via SMS Backup ('.$validated['sender'].')',
                    ]);

                    Notification::create([
                        'incident_id' => $incident->id,
                        'recipient' => 'DRRMO',
                        'channel' => 'SMS Backup',
                        'status' => 'Delivered',
                        'sent_at' => now(),
                    ]);

                    if ($emergencyType === Incident::TYPE_CRITICAL) {
                        Notification::create([
                            'incident_id' => $incident->id,
                            'recipient' => 'Clinic',
                            'channel' => 'SMS Backup',
                            'status' => 'Delivered',
                            'sent_at' => now(),
                        ]);
                    }

                    // Broadcast the event to WebSockets
                    broadcast(new EmergencyReported($incident))->toOthers();

                    return response()->json(['status' => 'success', 'message' => 'SMS emergency processed'], 201);
                }
            }
        }

        return response()->json(['status' => 'ignored', 'message' => 'Not a valid emergency format'], 200);
    }
}
