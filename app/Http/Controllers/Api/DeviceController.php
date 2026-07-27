<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Incident;
use App\Models\Notification;
use App\Events\EmergencyReported;
use Illuminate\Support\Facades\Log;

class DeviceController extends Controller
{
    /**
     * Handle incoming emergency alerts from ESP32 devices.
     */
    public function emergency(Request $request)
    {
        // Validate incoming payload
        $validated = $request->validate([
            'device_id' => 'required|string',
            'emergency_category' => 'required|string',
            'timestamp' => 'nullable|date',
        ]);

        $deviceCode = $validated['device_id'];
        $emergencyType = $validated['emergency_category'];

        // Find the device
        $device = Device::where('device_code', $deviceCode)->first();

        if (!$device) {
            return response()->json([
                'status' => 'error',
                'message' => 'Device not found.'
            ], 404);
        }

        // Update device last_seen status
        $device->update(['last_seen' => now()]);

        // Create incident record
        $incident = Incident::create([
            'device_id' => $device->id,
            'emergency_type' => $emergencyType,
            'status' => 'Pending',
        ]);

        // Create notifications log for both NDRRMO and Clinic
        Notification::create([
            'incident_id' => $incident->id,
            'recipient'   => 'NDRRMO',
            'channel'     => 'Dashboard',
            'status'      => 'Delivered',
            'sent_at'     => now(),
        ]);

        Notification::create([
            'incident_id' => $incident->id,
            'recipient'   => 'Clinic',
            'channel'     => 'Dashboard',
            'status'      => 'Delivered',
            'sent_at'     => now(),
        ]);

        // Broadcast the event to WebSockets
        broadcast(new EmergencyReported($incident))->toOthers();

        return response()->json([
            'status' => 'success',
            'message' => 'Emergency alert received and processed.',
            'incident_id' => $incident->id,
        ], 201);
    }

    /**
     * Return status of pending incidents for an ESP32 device.
     */
    public function status(Request $request)
    {
        $deviceCode = $request->query('device_id');
        if (!$deviceCode) {
            return response()->json(['status' => 'normal', 'has_pending' => false]);
        }

        $device = Device::where('device_code', $deviceCode)->first();
        if (!$device) {
            return response()->json(['status' => 'normal', 'has_pending' => false]);
        }

        $pendingIncident = Incident::where('device_id', $device->id)
            ->whereIn('status', ['pending', 'Pending'])
            ->first();

        return response()->json([
            'device_code' => $deviceCode,
            'has_pending' => (bool) $pendingIncident,
            'status'      => $pendingIncident ? 'pending' : 'normal',
            'incident_id' => $pendingIncident ? $pendingIncident->id : null,
        ]);
    }

    /**
     * Acknowledge an incident via API.
     */
    public function acknowledge($incident)
    {
        $incidentModel = Incident::find($incident);
        if (!$incidentModel) {
            $incidentModel = Incident::findOrFail($incident);
        }

        Incident::where('device_id', $incidentModel->device_id)
            ->whereIn('status', ['pending', 'Pending'])
            ->update([
                'status' => 'Acknowledged',
            ]);

        return response()->json([
            'status'      => 'success',
            'message'     => 'Emergency alert acknowledged.',
            'incident_id' => $incidentModel->id,
        ]);
    }

    public function dispatch($incident)
    {
        $incidentModel = Incident::findOrFail($incident);
        $incidentModel->update([
            'status' => 'Responding',
        ]);

        return response()->json([
            'status'      => 'success',
            'message'     => 'Responders dispatched.',
            'incident_id' => $incidentModel->id,
        ]);
    }

    public function resolve($incident)
    {
        $incidentModel = Incident::findOrFail($incident);
        $incidentModel->update([
            'status' => 'Resolved',
        ]);

        return response()->json([
            'status'      => 'success',
            'message'     => 'Incident marked resolved and recorded.',
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

                $device = Device::where('device_code', $deviceCode)->first();

                if ($device) {
                    $incident = Incident::create([
                        'device_id' => $device->id,
                        'emergency_type' => $emergencyType,
                        'status' => 'Pending',
                        'remarks' => 'Received via SMS Backup (' . $validated['sender'] . ')',
                    ]);

                    Notification::create([
                        'incident_id' => $incident->id,
                        'recipient' => 'NDRRMO',
                        'channel' => 'SMS Backup',
                        'status' => 'Delivered',
                        'sent_at' => now(),
                    ]);

                    if ($emergencyType === 'Critical Emergency') {
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
