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
        $onlineDevicesCount = \App\Models\Device::all()->filter(fn($d) => $d->is_online)->count();
        $devicesList = \App\Models\Device::all();
        $recentLogs = \App\Models\Incident::with('device')->latest()->take(5)->get();
        
        $stats = [
            'Critical' => \App\Models\Incident::where('emergency_type', 'like', '%Critical%')->count(),
            'Medical' => \App\Models\Incident::where('emergency_type', 'like', '%Medical%')->count(),
            'Public Safety' => \App\Models\Incident::where('emergency_type', 'like', '%Public Safety%')->count(),
            'Facility & Hazard' => \App\Models\Incident::where('emergency_type', 'like', '%Facility%')->orWhere('emergency_type', 'like', '%Hazard%')->count(),
        ];
        
        return view('ndrrmo', compact('activeIncidents', 'totalIncidents', 'resolvedIncidents', 'devicesCount', 'onlineDevicesCount', 'devicesList', 'recentLogs', 'stats'));
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

    public function bulkDeleteAlerts(Request $request) {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:incidents,id',
        ]);

        $ids = $validated['ids'];

        // Delete associated notifications first, then the incidents
        \App\Models\Notification::whereIn('incident_id', $ids)->delete();
        \App\Models\Incident::whereIn('id', $ids)->delete();

        return redirect()->route('ndrrmo.alerts')
            ->with('success', count($ids) . ' alert(s) deleted successfully.');
    }

    public function acknowledgeIncident($id) {
        $incident = \App\Models\Incident::with('device')->findOrFail($id);

        // Set status to Acknowledged for pending incidents of this device
        \App\Models\Incident::where('device_id', $incident->device_id)
            ->whereIn('status', ['pending', 'Pending'])
            ->update([
                'status' => 'Acknowledged',
            ]);

        \App\Models\Notification::create([
            'incident_id' => $incident->id,
            'recipient'   => 'NDRRMO',
            'channel'     => 'Dashboard',
            'status'      => 'Acknowledged',
            'sent_at'     => now(),
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'status'   => 'success',
                'message'  => 'Emergency alert acknowledged.',
                'incident' => $incident
            ]);
        }

        return redirect()->back()->with('success', 'Incident acknowledged.');
    }

    public function dispatchIncident($id) {
        $incident = \App\Models\Incident::findOrFail($id);
        $incident->update([
            'status' => 'Responding',
        ]);

        \App\Models\Notification::create([
            'incident_id' => $incident->id,
            'recipient'   => 'NDRRMO',
            'channel'     => 'Dashboard',
            'status'      => 'Dispatched',
            'sent_at'     => now(),
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'status'   => 'success',
                'message'  => 'Responders dispatched.',
                'incident' => $incident
            ]);
        }

        return redirect()->back()->with('success', 'Responders dispatched.');
    }

    public function resolveIncident($id) {
        $incident = \App\Models\Incident::findOrFail($id);
        $incident->update([
            'status' => 'Resolved',
        ]);

        \App\Models\Notification::create([
            'incident_id' => $incident->id,
            'recipient'   => 'NDRRMO',
            'channel'     => 'Dashboard',
            'status'      => 'Resolved',
            'sent_at'     => now(),
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'status'   => 'success',
                'message'  => 'Incident marked resolved and recorded.',
                'incident' => $incident
            ]);
        }

        return redirect()->back()->with('success', 'Incident marked resolved and recorded.');
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
        $devicesCount = \App\Models\Device::count();
        $onlineDevicesCount = \App\Models\Device::all()->filter(fn($d) => $d->is_online)->count();
        $latestPending = \App\Models\Incident::with('device')
            ->whereIn('status', ['pending', 'Pending'])
            ->latest()
            ->first();

        return response()->json([
            'active_alerts' => \App\Models\Incident::whereIn('status', ['pending', 'Pending', 'acknowledged', 'responding', 'Responding'])->count(),
            'total_incidents' => \App\Models\Incident::count(),
            'resolved_incidents' => \App\Models\Incident::whereIn('status', ['resolved', 'Resolved'])->count(),
            'devices_online' => $onlineDevicesCount,
            'total_devices' => $devicesCount,
            'pending' => \App\Models\Incident::whereIn('status', ['pending', 'Pending'])->count(),
            'responding' => \App\Models\Incident::whereIn('status', ['responding', 'Responding'])->count(),
            'resolved' => \App\Models\Incident::whereIn('status', ['resolved', 'Resolved'])->count(),
            'latest_pending' => $latestPending,
        ]);
    }

    public function exportExcel() {
        $incidents = \App\Models\Incident::with('device')->latest()->get();
        $filename = "incident_reports_" . date('Y-m-d_H-i-s') . ".xls";

        $headers = [
            "Content-Type" => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($incidents) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Incident ID', 'Time Reported', 'Emergency Category', 'Building Location', 'Floor / Room', 'Device Code', 'Status'], "\t");

            foreach ($incidents as $inc) {
                fputcsv($file, [
                    $inc->id,
                    $inc->created_at ? $inc->created_at->format('Y-m-d H:i:s') : '',
                    $inc->emergency_type,
                    $inc->device->building ?? 'N/A',
                    trim(($inc->device->floor ?? '') . ' ' . ($inc->device->room ?? '')),
                    $inc->device->device_code ?? 'N/A',
                    strtoupper($inc->status)
                ], "\t");
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
