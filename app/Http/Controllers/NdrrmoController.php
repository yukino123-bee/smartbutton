<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NdrrmoController extends Controller
{
    public function dashboard() { 
        $activeIncidents = \App\Models\Incident::with('device')->whereIn('status', ['pending', 'acknowledged'])->get();
        $totalIncidents = \App\Models\Incident::count();
        $resolvedIncidents = \App\Models\Incident::where('status', 'resolved')->count();
        $devicesCount = \App\Models\Device::count();
        $devicesList = \App\Models\Device::all();
        $recentLogs = \App\Models\Incident::with('device')->latest()->take(5)->get();
        
        $stats = [
            'Critical' => \App\Models\Incident::where('emergency_type', 'like', '%Critical%')->count(),
            'Medical' => \App\Models\Incident::where('emergency_type', 'like', '%Medical%')->count(),
            'Public Safety' => \App\Models\Incident::where('emergency_type', 'like', '%Public Safety%')->count(),
            'Facility & Hazard' => \App\Models\Incident::where('emergency_type', 'like', '%Facility%')->orWhere('emergency_type', 'like', '%Hazard%')->count(),
        ];
        
        return view('ndrrmo', compact('activeIncidents', 'totalIncidents', 'resolvedIncidents', 'devicesCount', 'devicesList', 'recentLogs', 'stats'));
    }
    public function alerts() { 
        $alerts = \App\Models\Incident::with('device')
            ->whereIn('status', ['pending', 'acknowledged', 'responding'])
            ->latest()
            ->get();
        return view('ndrrmo.alerts', compact('alerts')); 
    }
    public function logs() { 
        $logs = \App\Models\Incident::with('device')->latest()->paginate(15);
        return view('ndrrmo.logs', compact('logs')); 
    }
    public function map() { 
        $devices = \App\Models\Device::all();
        $activeIncidents = \App\Models\Incident::with('device')
            ->whereIn('status', ['pending', 'acknowledged', 'responding'])
            ->get();
            
        return view('ndrrmo.map', compact('devices', 'activeIncidents')); 
    }
    public function devices() { 
        $devices = \App\Models\Device::latest()->get();
        return view('ndrrmo.devices', compact('devices')); 
    }

    public function storeDevice(Request $request) {
        $validated = $request->validate([
            'device_code' => 'required|string|unique:devices,device_code',
            'building' => 'required|string',
            'floor' => 'required|string',
            'room' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'required|string|in:active,inactive,maintenance'
        ]);
        
        \App\Models\Device::create($validated);
        return redirect()->route('ndrrmo.devices')->with('success', 'Device registered successfully.');
    }

    public function updateDevice(Request $request, $id) {
        $device = \App\Models\Device::findOrFail($id);
        $validated = $request->validate([
            'device_code' => 'required|string|unique:devices,device_code,'.$id,
            'building' => 'required|string',
            'floor' => 'required|string',
            'room' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'required|string|in:active,inactive,maintenance'
        ]);

        $device->update($validated);
        return redirect()->route('ndrrmo.devices')->with('success', 'Device updated successfully.');
    }

    public function destroyDevice($id) {
        $device = \App\Models\Device::findOrFail($id);
        $device->delete();
        return redirect()->route('ndrrmo.devices')->with('success', 'Device deleted successfully.');
    }
    public function sms() { 
        $smsLogs = \App\Models\Notification::where('channel', 'SMS Backup')
            ->with('incident.device')
            ->latest()
            ->paginate(15);
        return view('ndrrmo.sms', compact('smsLogs')); 
    }
    public function reports() { 
        $stats = \App\Models\Incident::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
            
        $typeStats = \App\Models\Incident::selectRaw('emergency_type, count(*) as count')
            ->groupBy('emergency_type')
            ->pluck('count', 'emergency_type');
            
        $totalIncidents = \App\Models\Incident::count();
        
        return view('ndrrmo.reports', compact('stats', 'typeStats', 'totalIncidents')); 
    }

    public function statsJson() {
        return response()->json([
            'active_alerts' => \App\Models\Incident::whereIn('status', ['pending', 'acknowledged', 'responding'])->count(),
            'total_incidents' => \App\Models\Incident::count(),
            'resolved_incidents' => \App\Models\Incident::where('status', 'resolved')->count(),
            'devices_online' => \App\Models\Device::where('status', 'active')->count(),
            'total_devices' => \App\Models\Device::count(),
            'pending' => \App\Models\Incident::where('status', 'pending')->count(),
            'responding' => \App\Models\Incident::where('status', 'responding')->count(),
            'resolved' => \App\Models\Incident::where('status', 'resolved')->count(),
        ]);
    }
}
