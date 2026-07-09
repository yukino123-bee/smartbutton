<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::all();
        return response()->json($devices);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_code' => 'required|string|unique:devices',
            'building' => 'required|string',
            'floor' => 'nullable|string',
            'room' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $device = Device::create($validated);
        return response()->json($device, 201);
    }

    public function update(Request $request, Device $device)
    {
        $validated = $request->validate([
            'building' => 'string',
            'floor' => 'nullable|string',
            'room' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'string',
        ]);

        $device->update($validated);
        return response()->json($device);
    }

    public function destroy(Device $device)
    {
        $device->delete();
        return response()->json(null, 204);
    }
}
