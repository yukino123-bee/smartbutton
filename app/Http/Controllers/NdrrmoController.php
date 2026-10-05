<?php

namespace App\Http\Controllers;

use App\Events\EmergencyReported;
use App\Models\Device;
use App\Models\Incident;
use App\Models\Notification;
use Illuminate\Http\Request;

class NdrrmoController extends Controller
{
    public function dashboard()
    {
        $activeIncidents = Incident::with(['device', 'notifications'])->active()->latest('reported_at')->get();
        $totalIncidents = Incident::count();
        $resolvedIncidents = Incident::resolved()->count();
        $devicesList = Device::all();
        $devicesCount = $devicesList->count();
        $onlineDevicesCount = $devicesList->filter->is_online->count();
        $recentLogs = Incident::with('device')->latest('reported_at')->take(5)->get();
        $todayIncidents = Incident::whereDate('reported_at', today());

        $stats = [
            'Critical' => (clone $todayIncidents)->where('emergency_type', Incident::TYPE_CRITICAL)->count(),
            'Medical' => (clone $todayIncidents)->where('emergency_type', Incident::TYPE_MEDICAL)->count(),
            'Public Safety' => (clone $todayIncidents)->where('emergency_type', Incident::TYPE_PUBLIC_SAFETY)->count(),
            'Pending' => (clone $todayIncidents)->where('status', 'Pending')->count(),
            'Acknowledged' => (clone $todayIncidents)->where('status', 'Acknowledged')->count(),
            'Responding' => (clone $todayIncidents)->where('status', 'Responding')->count(),
            'Resolved' => (clone $todayIncidents)->where('status', 'Resolved')->count(),
        ];

        return view('ndrrmo', compact('activeIncidents', 'totalIncidents', 'resolvedIncidents', 'devicesCount', 'onlineDevicesCount', 'devicesList', 'recentLogs', 'stats'));
    }

    public function alerts()
    {
        $alerts = Incident::with(['device', 'notifications'])
            ->active()
            ->latest('reported_at')
            ->get();

        return view('ndrrmo.alerts', compact('alerts'));
    }

    public function logs()
    {
        $logs = Incident::with('device')->latest('reported_at')->paginate(15);

        return view('ndrrmo.logs', compact('logs'));
    }

    public function map()
    {
        $devices = Device::all();
        $activeIncidents = Incident::with('device')
            ->active()
            ->get();

        return view('ndrrmo.map', compact('devices', 'activeIncidents'));
    }

    public function devices()
    {
        $devices = Device::latest()->get();

        return view('ndrrmo.devices', compact('devices'));
    }

    public function storeDevice(Request $request)
    {
        $validated = $request->validate([
            'device_code' => 'required|string|unique:devices,device_code',
            'building' => 'required|string',
            'floor' => 'required|string',
            'room' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'required|string|in:active,inactive,maintenance',
        ]);

        Device::create($validated);

        return redirect()->route('ndrrmo.devices')->with('success', 'Device registered successfully.');
    }

    public function updateDevice(Request $request, Device $device)
    {
        $validated = $request->validate([
            'device_code' => 'required|string|unique:devices,device_code,'.$device->id,
            'building' => 'required|string',
            'floor' => 'required|string',
            'room' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'required|string|in:active,inactive,maintenance',
        ]);

        $device->update($validated);

        return redirect()->route('ndrrmo.devices')->with('success', 'Device updated successfully.');
    }

    public function destroyDevice(Device $device)
    {
        $device->delete();

        return redirect()->route('ndrrmo.devices')->with('success', 'Device deleted successfully.');
    }

    public function bulkDeleteAlerts(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:incidents,id',
        ]);

        $ids = $validated['ids'];

        // Delete associated notifications first, then the incidents
        Notification::whereIn('incident_id', $ids)->delete();
        Incident::whereIn('id', $ids)->delete();

        return redirect()->route('ndrrmo.alerts')
            ->with('success', count($ids).' alert(s) deleted successfully.');
    }

    public function acknowledgeIncident(Incident $incident)
    {
        $incident->load('device');
        abort_unless($incident->status === 'Pending', 409, 'Only pending incidents can be acknowledged.');
        $incident->update([
            'status' => 'Acknowledged',
            'acknowledged_at' => $incident->acknowledged_at ?? now(),
        ]);

        Notification::create([
            'incident_id' => $incident->id,
            'recipient' => 'DRRMO',
            'channel' => 'Dashboard',
            'status' => 'Acknowledged',
            'sent_at' => now(),
        ]);

        if (in_array($incident->emergency_type, [Incident::TYPE_CRITICAL, Incident::TYPE_MEDICAL], true)
            || $incident->notifications()->where('recipient', 'Clinic')->exists()) {
            Notification::create([
                'incident_id' => $incident->id,
                'recipient' => 'Clinic',
                'channel' => 'Dashboard',
                'status' => 'Acknowledged by DRRMO',
                'sent_at' => now(),
            ]);
        }

        broadcast(new EmergencyReported($incident))->toOthers();

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Emergency alert acknowledged.',
                'incident' => $incident,
            ]);
        }

        return redirect()->back()->with('success', 'Incident acknowledged.');
    }

    public function acknowledgeAll(Request $request)
    {
        $pendingIncidents = Incident::with('device')->where('status', 'Pending')->get();

        foreach ($pendingIncidents as $incident) {
            $incident->update([
                'status' => 'Acknowledged',
                'acknowledged_at' => $incident->acknowledged_at ?? now(),
            ]);

            Notification::create([
                'incident_id' => $incident->id,
                'recipient' => 'DRRMO',
                'channel' => 'Dashboard',
                'status' => 'Acknowledged',
                'sent_at' => now(),
            ]);

            if (in_array($incident->emergency_type, [Incident::TYPE_CRITICAL, Incident::TYPE_MEDICAL], true)
                || $incident->notifications()->where('recipient', 'Clinic')->exists()) {
                Notification::create([
                    'incident_id' => $incident->id,
                    'recipient' => 'Clinic',
                    'channel' => 'Dashboard',
                    'status' => 'Acknowledged by DRRMO',
                    'sent_at' => now(),
                ]);
            }

            broadcast(new \App\Events\EmergencyReported($incident))->toOthers();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $pendingIncidents->count().' alert(s) acknowledged.',
                'count' => $pendingIncidents->count(),
            ]);
        }

        return redirect()->back()->with('success', $pendingIncidents->count().' alert(s) acknowledged.');
    }

    public function notifyClinic(Incident $incident)
    {
        abort_unless(in_array($incident->emergency_type, Incident::EMERGENCY_TYPES, true), 404);

        Notification::firstOrCreate([
            'incident_id' => $incident->id,
            'recipient' => 'Clinic',
        ], [
            'channel' => 'Dashboard',
            'status' => 'Delivered',
            'sent_at' => now(),
        ]);

        broadcast(new EmergencyReported($incident))->toOthers();

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Clinic notified successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Clinic notified successfully.');
    }

    public function dispatchIncident(Request $request, Incident $incident)
    {
        abort_unless(in_array($incident->status, ['Pending', 'Acknowledged', 'Responding'], true), 409, 'Incident cannot be dispatched from its current status.');

        $validated = $request->validate([
            'responder_name' => 'nullable|string|max:150',
            'responder_contact' => 'nullable|string|max:100',
            'eta_minutes' => 'nullable|integer|min:1|max:120',
            'dispatch_notes' => 'nullable|string|max:1000',
        ]);

        $responderName = $validated['responder_name'] ?? ($incident->responder_name ?: 'DRRMO Responders Team');
        $etaMinutes = $validated['eta_minutes'] ?? ($incident->eta_minutes ?: 3);
        $responderContact = $validated['responder_contact'] ?? $incident->responder_contact;
        $dispatchNotes = $validated['dispatch_notes'] ?? $incident->dispatch_notes;

        $incident->update([
            'status' => 'Responding',
            'dispatched_at' => $incident->dispatched_at ?? now(),
            'acknowledged_at' => $incident->acknowledged_at ?? now(),
            'responder_name' => $responderName,
            'responder_contact' => $responderContact,
            'eta_minutes' => $etaMinutes,
            'dispatch_notes' => $dispatchNotes,
        ]);
        $incident->load('device');

        Notification::create([
            'incident_id' => $incident->id,
            'recipient' => 'DRRMO',
            'channel' => 'Dashboard',
            'status' => 'Dispatched',
            'sent_at' => now(),
        ]);

        if (in_array($incident->emergency_type, [Incident::TYPE_CRITICAL, Incident::TYPE_MEDICAL], true)
            || $incident->notifications()->where('recipient', 'Clinic')->exists()) {
            Notification::create([
                'incident_id' => $incident->id,
                'recipient' => 'Clinic',
                'channel' => 'Dashboard',
                'status' => 'DRRMO Responders Dispatched',
                'sent_at' => now(),
            ]);
        }

        broadcast(new EmergencyReported($incident))->toOthers();

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Responders dispatched: {$responderName} (ETA: {$etaMinutes} mins).",
                'incident' => $incident,
            ]);
        }

        return redirect()->back()->with('success', "Responders dispatched: {$responderName} (ETA: {$etaMinutes} mins).");
    }

    public function onSceneIncident(Incident $incident)
    {
        $incident->update([
            'arrived_at' => now(),
            'status' => 'Responding',
        ]);
        $incident->load('device');

        Notification::create([
            'incident_id' => $incident->id,
            'recipient' => 'DRRMO',
            'channel' => 'Dashboard',
            'status' => 'Responders Arrived On Scene',
            'sent_at' => now(),
        ]);

        if (in_array($incident->emergency_type, [Incident::TYPE_CRITICAL, Incident::TYPE_MEDICAL], true)
            || $incident->notifications()->where('recipient', 'Clinic')->exists()) {
            Notification::create([
                'incident_id' => $incident->id,
                'recipient' => 'Clinic',
                'channel' => 'Dashboard',
                'status' => 'DRRMO On Scene',
                'sent_at' => now(),
            ]);
        }

        broadcast(new EmergencyReported($incident))->toOthers();

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Responders confirmed on scene.',
                'incident' => $incident,
            ]);
        }

        return redirect()->back()->with('success', 'Responders confirmed on scene.');
    }

    public function resolveIncident(Request $request, Incident $incident)
    {
        abort_if($incident->status === 'Resolved', 409, 'Incident is already resolved.');

        $validated = $request->validate([
            'resolution_type' => 'nullable|string|in:Resolved,False Alarm,Drill',
            'remarks' => 'nullable|string|max:1000',
            'resolution_notes' => 'nullable|string|max:1000',
            'false_alarm_reason' => 'nullable|string|max:255',
            'patient_name' => 'nullable|string|max:150',
            'patient_id_number' => 'nullable|string|max:50',
            'triage_level' => 'nullable|string|max:30',
            'treatment_summary' => 'nullable|string|max:1000',
            'disposition' => 'nullable|string|max:100',
        ]);

        $resType = $validated['resolution_type'] ?? 'Resolved';
        $remarks = $validated['remarks'] ?? $validated['resolution_notes'] ?? $incident->remarks;
        if ($resType !== 'Resolved' && !empty($validated['false_alarm_reason'])) {
            $remarks = ($remarks ? $remarks . ' · ' : '') . "[{$resType}] " . $validated['false_alarm_reason'];
        }

        $incident->update([
            'status' => 'Resolved',
            'resolved_at' => now(),
            'resolution_type' => $resType,
            'false_alarm_reason' => $validated['false_alarm_reason'] ?? null,
            'remarks' => $remarks,
            'patient_name' => $validated['patient_name'] ?? $incident->patient_name,
            'patient_id_number' => $validated['patient_id_number'] ?? $incident->patient_id_number,
            'triage_level' => $validated['triage_level'] ?? $incident->triage_level,
            'treatment_summary' => $validated['treatment_summary'] ?? $incident->treatment_summary,
            'disposition' => $validated['disposition'] ?? $incident->disposition,
        ]);
        $incident->load('device');

        Notification::create([
            'incident_id' => $incident->id,
            'recipient' => 'DRRMO',
            'channel' => 'Dashboard',
            'status' => $resType === 'Resolved' ? 'Resolved' : "Resolved ({$resType})",
            'sent_at' => now(),
        ]);

        if (in_array($incident->emergency_type, [Incident::TYPE_CRITICAL, Incident::TYPE_MEDICAL], true)
            || $incident->notifications()->where('recipient', 'Clinic')->exists()) {
            Notification::create([
                'incident_id' => $incident->id,
                'recipient' => 'Clinic',
                'channel' => 'Dashboard',
                'status' => $resType === 'Resolved' ? 'Resolved by DRRMO' : "Resolved by DRRMO ({$resType})",
                'sent_at' => now(),
            ]);
        }

        broadcast(new EmergencyReported($incident))->toOthers();

        $msg = $resType === 'Resolved' ? 'Incident marked resolved and recorded.' : "Incident logged as {$resType}.";

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'incident' => $incident,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    public function sms()
    {
        $smsLogs = Notification::where('channel', 'SMS Backup')
            ->with('incident.device')
            ->latest()
            ->paginate(15);

        return view('ndrrmo.sms', compact('smsLogs'));
    }

    public function reports()
    {
        $rawStats = Incident::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $stats = [
            'pending' => $rawStats['Pending'] ?? $rawStats['pending'] ?? 0,
            'acknowledged' => $rawStats['Acknowledged'] ?? $rawStats['acknowledged'] ?? 0,
            'responding' => $rawStats['Responding'] ?? $rawStats['responding'] ?? 0,
            'resolved' => $rawStats['Resolved'] ?? $rawStats['resolved'] ?? 0,
            'Pending' => $rawStats['Pending'] ?? $rawStats['pending'] ?? 0,
            'Acknowledged' => $rawStats['Acknowledged'] ?? $rawStats['acknowledged'] ?? 0,
            'Responding' => $rawStats['Responding'] ?? $rawStats['responding'] ?? 0,
            'Resolved' => $rawStats['Resolved'] ?? $rawStats['resolved'] ?? 0,
        ];

        $typeStats = Incident::selectRaw('emergency_type, count(*) as count')
            ->groupBy('emergency_type')
            ->pluck('count', 'emergency_type');

        $totalIncidents = Incident::count();
        $averageResolutionSeconds = Incident::resolved()
            ->whereNotNull('resolved_at')
            ->get(['reported_at', 'resolved_at'])
            ->avg(fn (Incident $incident) => $incident->reported_at->diffInSeconds($incident->resolved_at));

        return view('ndrrmo.reports', compact('stats', 'typeStats', 'totalIncidents', 'averageResolutionSeconds'));
    }

    public function statsJson()
    {
        $devices = Device::all();
        $pendingIncidents = Incident::with('device')
            ->where('status', 'Pending')
            ->latest('reported_at')
            ->get();

        return response()->json([
            'active_alerts' => Incident::active()->count(),
            'total_incidents' => Incident::count(),
            'resolved_incidents' => Incident::resolved()->count(),
            'devices_online' => $devices->filter->is_online->count(),
            'total_devices' => $devices->count(),
            'pending' => $pendingIncidents->count(),
            'responding' => Incident::where('status', 'Responding')->count(),
            'resolved' => Incident::resolved()->count(),
            'latest_pending' => $pendingIncidents->first(),
            'pending_incidents' => $pendingIncidents,
        ]);
    }

    public function exportExcel()
    {
        $incidents = Incident::with('device')->latest('reported_at')->get();
        $filename = 'incident_reports_'.date('Y-m-d_H-i-s').'.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($incidents) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Incident ID', 'Time Reported', 'Emergency Category', 'Building Location', 'Floor / Room', 'Device Code', 'Status'], "\t");

            foreach ($incidents as $inc) {
                fputcsv($file, [
                    $inc->id,
                    $inc->created_at ? $inc->created_at->format('Y-m-d H:i:s') : '',
                    $inc->emergency_type,
                    $inc->device->building ?? 'N/A',
                    trim(($inc->device->floor ?? '').' '.($inc->device->room ?? '')),
                    $inc->device->device_code ?? 'N/A',
                    strtoupper($inc->status),
                ], "\t");
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
