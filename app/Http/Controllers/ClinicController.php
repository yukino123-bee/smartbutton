<?php

namespace App\Http\Controllers;

use App\Events\EmergencyReported;
use App\Models\Incident;
use App\Models\Notification;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
    public function dashboard()
    {
        $activeAlerts = Incident::clinicVisible()->clinicActive()->count();

        $incomingCount = Incident::clinicVisible()
            ->whereDate('reported_at', today())
            ->clinicActive()
            ->count();

        $treatedTodayCount = Incident::clinicVisible()
            ->whereDate('clinic_resolved_at', today())
            ->clinicResolved()
            ->count();

        $resolvedTodayCount = $treatedTodayCount;

        $criticalIncidents = Incident::with('device')
            ->clinicVisible()
            ->clinicActive()
            ->latest('reported_at')
            ->get();

        $recentHistory = Incident::with('device')
            ->clinicVisible()
            ->whereDate('reported_at', today())
            ->latest('reported_at')
            ->take(10)
            ->get();

        $criticalIncidents->each->clinicView();
        $recentHistory->each->clinicView();
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

    public function alerts()
    {
        $alerts = Incident::with(['device', 'notifications'])
            ->clinicVisible()
            ->clinicActive()
            ->latest('reported_at')
            ->get()
            ->each->clinicView();

        return view('clinic.alerts', compact('alerts'));
    }

    public function bulkDeleteAlerts(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:incidents,id',
        ]);

        $ids = Incident::clinicVisible()->whereIn('id', $validated['ids'])->pluck('id');

        // Only hides the alerts from the Clinic dashboard; DRRMO keeps the incident and its history.
        Incident::whereIn('id', $ids)->update(['clinic_hidden_at' => now()]);

        return redirect()->route('clinic.alerts')
            ->with('success', $ids->count().' alert(s) deleted successfully.');
    }

    public function destroyAlert($id)
    {
        // Only hides the alert from the Clinic dashboard; DRRMO keeps the incident and its history.
        $incident = Incident::clinicVisible()->findOrFail($id);
        $incident->update(['clinic_hidden_at' => now()]);

        return redirect()->route('clinic.alerts')
            ->with('success', 'Alert deleted successfully.');
    }

    public function incoming()
    {
        $incomingPatients = Incident::with('device')
            ->clinicVisible()
            ->clinicActive()
            ->latest('reported_at')
            ->get()
            ->each->clinicView();

        return view('clinic.incoming', compact('incomingPatients'));
    }

    public function logs()
    {
        $logs = Incident::with('device')
            ->clinicVisible()
            ->latest('reported_at')
            ->paginate(15);
        $logs->getCollection()->each->clinicView();

        return view('clinic.logs', compact('logs'));
    }

    public function patients()
    {
        $patients = Incident::with('device')
            ->clinicVisible()
            ->clinicResolved()
            ->latest('clinic_resolved_at')
            ->paginate(15);
        $patients->getCollection()->each->clinicView();

        return view('clinic.patients', compact('patients'));
    }

    public function reports()
    {
        $stats = Incident::clinicVisible()->selectRaw('clinic_status, count(*) as count')
            ->groupBy('clinic_status')
            ->pluck('count', 'clinic_status');

        $typeStats = Incident::clinicVisible()->selectRaw('emergency_type, count(*) as count')
            ->groupBy('emergency_type')
            ->pluck('count', 'emergency_type');

        $buildingStats = Incident::clinicVisible()->join('devices', 'incidents.device_id', '=', 'devices.id')
            ->selectRaw('devices.building, count(*) as count')
            ->groupBy('devices.building')
            ->pluck('count', 'devices.building');

        $totalIncidents = Incident::clinicVisible()->count();
        $totalTreated = Incident::clinicVisible()->clinicResolved()->count();

        return view('clinic.reports', compact('stats', 'typeStats', 'buildingStats', 'totalIncidents', 'totalTreated'));
    }

    /**
     * Clinic actions only touch the Clinic's own state (clinic_* columns) and write Clinic
     * notifications. They never change the shared status or notify DRRMO, so DRRMO's
     * alert keeps running until DRRMO itself handles it.
     */
    private function authorizeClinicIncident(Incident $incident): void
    {
        abort_unless($incident->involvesClinic() && ! $incident->clinic_hidden_at, 404);
    }

    private function clinicNotify(Incident $incident, string $status): void
    {
        Notification::create([
            'incident_id' => $incident->id,
            'recipient' => 'Clinic',
            'channel' => 'Dashboard',
            'status' => $status,
            'sent_at' => now(),
        ]);

        broadcast(new EmergencyReported($incident))->toOthers();
    }

    public function acknowledgeIncident(Incident $incident)
    {
        $this->authorizeClinicIncident($incident);

        if ($incident->clinic_status === 'Pending') {
            $incident->update([
                'clinic_status' => 'Acknowledged',
                'clinic_acknowledged_at' => $incident->clinic_acknowledged_at ?? now(),
            ]);
        }
        $incident->load('device');

        $this->clinicNotify($incident, 'Acknowledged');

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Medical alert acknowledged by Clinic.',
                'incident' => $incident->clinicView(),
            ]);
        }

        return redirect()->back()->with('success', 'Medical alert acknowledged by Clinic staff.');
    }

    public function acknowledgeAll(Request $request)
    {
        $pendingIncidents = Incident::with('device')
            ->clinicVisible()
            ->where('clinic_status', 'Pending')
            ->get();

        foreach ($pendingIncidents as $incident) {
            $incident->update([
                'clinic_status' => 'Acknowledged',
                'clinic_acknowledged_at' => $incident->clinic_acknowledged_at ?? now(),
            ]);

            $this->clinicNotify($incident, 'Acknowledged');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $pendingIncidents->count().' medical alert(s) acknowledged.',
                'count' => $pendingIncidents->count(),
            ]);
        }

        return redirect()->back()->with('success', $pendingIncidents->count().' medical alert(s) acknowledged.');
    }

    public function dispatchIncident(Request $request, Incident $incident)
    {
        $this->authorizeClinicIncident($incident);

        abort_unless(in_array($incident->clinic_status, ['Pending', 'Acknowledged', 'Responding'], true), 409, 'Incident cannot be dispatched from its current status.');

        $validated = $request->validate([
            'responder_name' => 'nullable|string|max:150',
            'responder_contact' => 'nullable|string|max:100',
            'eta_minutes' => 'nullable|integer|min:1|max:120',
            'dispatch_notes' => 'nullable|string|max:1000',
        ]);

        $responderName = $validated['responder_name'] ?? ($incident->clinic_responder_name ?: 'Clinic Medical Team');
        $etaMinutes = $validated['eta_minutes'] ?? ($incident->clinic_eta_minutes ?: 3);
        $responderContact = $validated['responder_contact'] ?? $incident->clinic_responder_contact;
        $dispatchNotes = $validated['dispatch_notes'] ?? $incident->clinic_dispatch_notes;

        $incident->update([
            'clinic_status' => 'Responding',
            'clinic_dispatched_at' => $incident->clinic_dispatched_at ?? now(),
            'clinic_acknowledged_at' => $incident->clinic_acknowledged_at ?? now(),
            'clinic_responder_name' => $responderName,
            'clinic_responder_contact' => $responderContact,
            'clinic_eta_minutes' => $etaMinutes,
            'clinic_dispatch_notes' => $dispatchNotes,
        ]);
        $incident->load('device');

        $this->clinicNotify($incident, 'Medical Team Dispatched');

        // Tell DRRMO the Clinic has responded and sent help (shows as "Clinic: Medical Dispatched"). DRRMO's own incident is untouched.
        Notification::create([
            'incident_id' => $incident->id,
            'recipient' => 'DRRMO',
            'channel' => 'Dashboard',
            'status' => 'Clinic Medical Team Dispatched',
            'sent_at' => now(),
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Clinic medical team dispatched: {$responderName} (ETA: {$etaMinutes} mins).",
                'incident' => $incident->clinicView(),
            ]);
        }

        return redirect()->back()->with('success', "Clinic medical team dispatched: {$responderName} (ETA: {$etaMinutes} mins).");
    }

    public function onSceneIncident(Incident $incident)
    {
        $this->authorizeClinicIncident($incident);
        abort_unless($incident->clinic_status === 'Responding', 409, 'Only dispatched incidents can be marked on scene.');

        $incident->update(['clinic_arrived_at' => now()]);
        $incident->load('device');

        $this->clinicNotify($incident, 'Medical Team Arrived On Scene');

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Clinic team confirmed on scene.',
                'incident' => $incident->clinicView(),
            ]);
        }

        return redirect()->back()->with('success', 'Clinic team confirmed on scene.');
    }

    public function resolveIncident(Request $request, Incident $incident)
    {
        $this->authorizeClinicIncident($incident);

        if ($incident->clinic_status === 'Resolved') {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Incident is already resolved.',
                    'incident' => $incident->load('device')->clinicView(),
                ]);
            }

            return redirect()->back()->with('info', 'Incident is already resolved.');
        }

        $validated = $request->validate([
            'resolution_type' => 'nullable|string|in:Resolved,Drill',
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
        $remarks = $validated['remarks'] ?? $validated['resolution_notes'] ?? $incident->clinic_remarks;
        if ($resType !== 'Resolved' && ! empty($validated['false_alarm_reason'])) {
            $remarks = ($remarks ? $remarks.' · ' : '')."[{$resType}] ".$validated['false_alarm_reason'];
        }

        $incident->update([
            'clinic_status' => 'Resolved',
            'clinic_resolved_at' => now(),
            'clinic_resolution_type' => $resType,
            'clinic_false_alarm_reason' => $validated['false_alarm_reason'] ?? null,
            'clinic_remarks' => $remarks,
            'clinic_patient_name' => $validated['patient_name'] ?? $incident->clinic_patient_name,
            'clinic_patient_id_number' => $validated['patient_id_number'] ?? $incident->clinic_patient_id_number,
            'clinic_triage_level' => $validated['triage_level'] ?? $incident->clinic_triage_level,
            'clinic_treatment_summary' => $validated['treatment_summary'] ?? $incident->clinic_treatment_summary,
            'clinic_disposition' => $validated['disposition'] ?? $incident->clinic_disposition,
        ]);
        $incident->load('device');

        $this->clinicNotify($incident, 'Resolved');

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Incident resolved and care logged.',
                'incident' => $incident->clinicView(),
            ]);
        }

        return redirect()->back()->with('success', 'Incident resolved and care logged.');
    }

    public function exportExcel()
    {
        $incidents = Incident::with('device')->clinicVisible()->latest('reported_at')->get();
        $filename = 'clinic_patient_reports_'.date('Y-m-d_H-i-s').'.xls';

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
                    strtoupper($inc->clinic_status),
                ], "\t");
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function statsJson()
    {
        $pendingIncidents = Incident::with('device')
            ->clinicVisible()
            ->clinicActive()
            ->whereNull('clinic_acknowledged_at')
            ->whereDoesntHave('notifications', function ($query) {
                $query->where('recipient', 'Clinic')
                    ->where('status', 'Acknowledged');
            })
            ->latest('reported_at')
            ->get()
            ->each->clinicView();

        return response()->json([
            'active_alerts' => Incident::clinicVisible()->clinicActive()->count(),
            'incoming' => Incident::clinicVisible()->whereDate('reported_at', today())->clinicActive()->count(),
            'treated_today' => Incident::clinicVisible()->whereDate('clinic_resolved_at', today())->clinicResolved()->count(),
            'resolved_today' => Incident::clinicVisible()->whereDate('clinic_resolved_at', today())->clinicResolved()->count(),
            'latest_pending' => $pendingIncidents->first(),
            'pending_incidents' => $pendingIncidents,
        ]);
    }
}
