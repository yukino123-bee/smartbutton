<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NdrrmoController extends Controller
{
    public function dashboard() { 
        $activeIncidents = \App\Models\Incident::with('device')->whereIn('status', ['pending', 'acknowledged'])->get();
        $totalIncidents = \App\Models\Incident::count();
        $resolvedIncidents = \App\Models\Incident::where('status', 'resolved')->count();
        $devices = \App\Models\Device::count();
        
        return view('ndrrmo', compact('activeIncidents', 'totalIncidents', 'resolvedIncidents', 'devices'));
    }
    public function alerts() { return view('ndrrmo.alerts'); }
    public function logs() { return view('ndrrmo.logs'); }
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
    public function sms() { return view('ndrrmo.sms'); }
    public function reports() { return view('ndrrmo.reports'); }
}
