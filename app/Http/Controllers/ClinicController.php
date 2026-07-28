<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\Notification;
use App\Events\EmergencyReported;
use Carbon\Carbon;

class ClinicController extends Controller
{
    public function dashboard() { 
        $activeAlerts = Incident::whereIn('status', ['pending', 'Pending', 'acknowledged', 'Acknowledged', 'responding', 'Responding'])
            ->count();

        $incomingCount = Incident::whereDate('created_at', Carbon::today())
            ->whereIn('status', ['pending', 'Pending', 'acknowledged', 'Acknowledged', 'responding', 'Responding'])
            ->count();

        $treatedTodayCount = Incident::whereDate('updated_at', Carbon::today())
            ->whereIn('status', ['resolved', 'Resolved'])
            ->count();

        $resolvedTodayCount = Incident::whereDate('updated_at', Carbon::today())
            ->whereIn('status', ['resolved', 'Resolved'])
            ->count();

        $criticalIncidents = Incident::with('device')
            ->whereIn('status', ['pending', 'Pending', 'acknowledged', 'Acknowledged', 'responding', 'Responding'])
            ->latest()
            ->get();

        $recentHistory = Incident::with('device')
            ->whereDate('created_at', Carbon::today())
            ->latest()
            ->take(10)
            ->get();

        $activeEmergency = $criticalIncidents->first();

        return view('clinic', compact(
            'activeAlerts',
            'incomingCount',
            'treatedTodayCount',
            'resolvedTodayCount',
            'criticalIncidents',
            'recentHistory',
            'activeEmergency'
        ));
    }

    public function alerts() {
        $alerts = Incident::with('device')
            ->whereIn('status', ['pending', 'Pending', 'acknowledged', 'Acknowledged', 'responding', 'Responding'])
            ->latest()
            ->get();

        return view('clinic.alerts', compact('alerts'));
    }

    public function bulkDeleteAlerts(Request $request) {
        $validated = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:incidents,id',
        ]);

        $ids = $validated['ids'];

        Notification::whereIn('incident_id', $ids)->delete();
        Incident::whereIn('id', $ids)->delete();

        return redirect()->route('clinic.alerts')
            ->with('success', count($ids) . ' alert(s) deleted successfully.');
    }

    public function destroyAlert($id) {
        $incident = Incident::findOrFail($id);
        Notification::where('incident_id', $incident->id)->delete();
        $incident->delete();

        return redirect()->route('clinic.alerts')
            ->with('success', 'Alert deleted successfully.');
    }

    public function incoming() {
        $incomingPatients = Incident::with('device')
            ->whereIn('status', ['pending', 'Pending', 'acknowledged', 'Acknowledged', 'responding', 'Responding'])
            ->latest()
            ->get();

        return view('clinic.incoming', compact('incomingPatients'));
    }

    public function logs() {
        $logs = Incident::with('device')
            ->latest()
            ->paginate(15);

        return view('clinic.logs', compact('logs'));
    }

    public function patients() {
        $patients = Incident::with('device')
            ->whereIn('status', ['resolved', 'Resolved'])
            ->latest()
            ->paginate(15);

        return view('clinic.patients', compact('patients'));
    }

    public function reports() {
        $stats = Incident::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $typeStats = Incident::selectRaw('emergency_type, count(*) as count')
            ->groupBy('emergency_type')
            ->pluck('count', 'emergency_type');

        $buildingStats = Incident::join('devices', 'incidents.device_id', '=', 'devices.id')
            ->selectRaw('devices.building, count(*) as count')
            ->groupBy('devices.building')
            ->pluck('count', 'devices.building');

        $totalIncidents = Incident::count();
        $totalTreated = Incident::whereIn('status', ['resolved', 'Resolved'])->count();

        return view('clinic.reports', compact('stats', 'typeStats', 'buildingStats', 'totalIncidents', 'totalTreated'));
    }

    public function acknowledgeIncident($id) {
        $incident = Incident::with('device')->findOrFail($id);

        Incident::where('device_id', $incident->device_id)
            ->whereIn('status', ['pending', 'Pending'])
            ->update(['status' => 'Acknowledged']);

        $incident->refresh();

        Notification::create([
            'incident_id' => $incident->id,
            'recipient'   => 'Clinic',
            'channel'     => 'Dashboard',
            'status'      => 'Acknowledged',
            'sent_at'     => now(),
        ]);

        Notification::create([
            'incident_id' => $incident->id,
            'recipient'   => 'DRRMO',
            'channel'     => 'Dashboard',
            'status'      => 'Acknowledged by Clinic',
            'sent_at'     => now(),
        ]);

        broadcast(new EmergencyReported($incident))->toOthers();

        if (request()->wantsJson()) {
            return response()->json([
                'status'   => 'success',
                'message'  => 'Medical alert acknowledged by Clinic.',
                'incident' => $incident
            ]);
        }

        return redirect()->back()->with('success', 'Medical alert acknowledged by Clinic staff.');
    }

    public function resolveIncident($id) {
        $incident = Incident::with('device')->findOrFail($id);
        $incident->update(['status' => 'Resolved']);

        Notification::create([
            'incident_id' => $incident->id,
            'recipient'   => 'Clinic',
            'channel'     => 'Dashboard',
            'status'      => 'Resolved',
            'sent_at'     => now(),
        ]);

        Notification::create([
            'incident_id' => $incident->id,
            'recipient'   => 'DRRMO',
            'channel'     => 'Dashboard',
            'status'      => 'Resolved by Clinic',
            'sent_at'     => now(),
        ]);

        broadcast(new EmergencyReported($incident))->toOthers();

        if (request()->wantsJson()) {
            return response()->json([
                'status'   => 'success',
                'message'  => 'Patient treated and incident marked resolved.',
                'incident' => $incident
            ]);
        }

        return redirect()->back()->with('success', 'Patient treated and incident resolved.');
    }

    public function exportExcel() {
        $incidents = Incident::with('device')->latest()->get();
        $filename = "clinic_patient_reports_" . date('Y-m-d_H-i-s') . ".xls";

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

    public function statsJson() {
        return response()->json([
            'active_alerts' => Incident::whereIn('status', ['pending', 'Pending', 'acknowledged', 'Acknowledged'])->count(),
            'incoming' => Incident::whereDate('created_at', Carbon::today())
                ->whereIn('status', ['pending', 'Pending', 'acknowledged', 'Acknowledged'])->count(),
            'treated_today' => Incident::whereDate('updated_at', Carbon::today())
                ->whereIn('status', ['resolved', 'Resolved'])->count(),
            'resolved_today' => Incident::whereDate('updated_at', Carbon::today())
                ->whereIn('status', ['resolved', 'Resolved'])->count(),
            'latest_pending' => Incident::with('device')->whereIn('status', ['pending', 'Pending'])->latest()->first(),
        ]);
    }
}
