<?php

namespace App\Http\Controllers;

use App\Events\EmergencyReported;
use App\Models\Device;
use App\Models\Incident;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SimulatorController extends Controller
{
    /**
     * Display the ESP32 Hardware & Location Simulator interface.
     */
    public function index()
    {
        $devices = Device::orderBy('building')->get();
        $emergencyTypes = Incident::EMERGENCY_TYPES;
        $activeIncidents = Incident::active()->with('device')->latest('reported_at')->take(10)->get();
        $totalActive = Incident::active()->count();

        return view('simulator', compact('devices', 'emergencyTypes', 'activeIncidents', 'totalActive'));
    }

    /**
     * Quickly register a new location / device from the simulator.
     */
    public function storeDevice(Request $request)
    {
        $validated = $request->validate([
            'device_code' => ['required', 'string', 'max:50', 'unique:devices,device_code'],
            'building' => ['required', 'string', 'max:100'],
            'floor' => ['nullable', 'string', 'max:50'],
            'room' => ['nullable', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $device = Device::create([
            'device_code' => strtoupper(trim($validated['device_code'])),
            'building' => trim($validated['building']),
            'floor' => $validated['floor'] ?? 'Ground Floor',
            'room' => $validated['room'] ?? 'Main Area',
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'status' => 'active',
            'last_seen' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Device [{$device->device_code}] created for {$device->building}.",
                'device' => $device,
            ], 201);
        }

        return redirect()->route('simulator')->with('success', "Device [{$device->device_code}] created successfully.");
    }

    /**
     * Trigger an alert across every registered location simultaneously.
     */
    public function triggerAll(Request $request)
    {
        $category = $request->input('emergency_category', Incident::TYPE_CRITICAL);
        $randomizeCategories = $request->boolean('randomize_categories', false);

        $activeDevices = Device::where('status', 'active')->get();

        if ($activeDevices->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active devices found to trigger.',
            ], 404);
        }

        $createdIncidents = [];
        $categoriesList = Incident::EMERGENCY_TYPES;

        foreach ($activeDevices as $index => $device) {
            $device->update(['last_seen' => now()]);

            $emergencyType = $randomizeCategories
                ? $categoriesList[$index % count($categoriesList)]
                : $category;

            $incident = Incident::create([
                'device_id' => $device->id,
                'emergency_type' => $emergencyType,
                'reported_at' => now(),
                'status' => 'Pending',
                'remarks' => 'Triggered via ESP32 Multi-Location Simulator',
            ]);

            // Create DRRMO notification
            Notification::create([
                'incident_id' => $incident->id,
                'recipient' => 'DRRMO',
                'channel' => 'Dashboard',
                'status' => 'Delivered',
                'sent_at' => now(),
            ]);

            // Create Clinic notification if Medical or Critical
            if (in_array($emergencyType, [Incident::TYPE_CRITICAL, Incident::TYPE_MEDICAL], true)) {
                Notification::create([
                    'incident_id' => $incident->id,
                    'recipient' => 'Clinic',
                    'channel' => 'Dashboard',
                    'status' => 'Delivered',
                    'sent_at' => now(),
                ]);
            }

            // Broadcast real-time emergency alert
            broadcast(new EmergencyReported($incident));

            $incident->load('device');
            $createdIncidents[] = $incident;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Triggered emergency alert across all ' . count($createdIncidents) . ' active locations!',
            'count' => count($createdIncidents),
            'incidents' => $createdIncidents,
        ], 201);
    }

    /**
     * Clean up or resolve active simulated alerts to re-arm the simulator.
     */
    public function resetAlerts(Request $request)
    {
        $activeIncidents = Incident::active()->get();
        $count = $activeIncidents->count();

        foreach ($activeIncidents as $incident) {
            $incident->update([
                'status' => 'Resolved',
                'resolved_at' => now(),
                'remarks' => 'Resolved via Simulator Re-arm',
            ]);

            Notification::create([
                'incident_id' => $incident->id,
                'recipient' => 'DRRMO',
                'channel' => 'Dashboard',
                'status' => 'Resolved',
                'sent_at' => now(),
            ]);

            if (in_array($incident->emergency_type, [Incident::TYPE_CRITICAL, Incident::TYPE_MEDICAL], true)) {
                Notification::create([
                    'incident_id' => $incident->id,
                    'recipient' => 'Clinic',
                    'channel' => 'Dashboard',
                    'status' => 'Resolved by DRRMO',
                    'sent_at' => now(),
                ]);
            }

            broadcast(new EmergencyReported($incident));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Successfully resolved and re-armed {$count} active alerts.",
                'resolved_count' => $count,
            ]);
        }

        return redirect()->route('simulator')->with('success', "Resolved and re-armed {$count} active alerts.");
    }
}
