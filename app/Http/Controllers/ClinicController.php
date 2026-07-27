<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use Carbon\Carbon;

class ClinicController extends Controller
{
    public function dashboard() { 
        $activeAlerts = Incident::where('emergency_type', 'Critical Emergency')
            ->whereIn('status', ['pending', 'acknowledged'])
            ->count();

        $incomingCount = Incident::whereDate('created_at', Carbon::today())
            ->whereIn('status', ['pending', 'acknowledged'])
            ->count();

        $treatedTodayCount = Incident::whereDate('updated_at', Carbon::today())
            ->where('status', 'resolved')
            ->count();

        $resolvedTodayCount = Incident::whereDate('updated_at', Carbon::today())
            ->where('status', 'resolved')
            ->count();

        $criticalIncidents = Incident::with('device')
            ->where('emergency_type', 'Critical Emergency')
            ->whereIn('status', ['pending', 'acknowledged'])
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

    public function resolveIncident(Incident $incident) {
        $incident->update(['status' => 'resolved']);
        return response()->json(['success' => true, 'message' => 'Incident resolved successfully']);
    }

    public function statsJson() {
        return response()->json([
            'active_alerts' => Incident::where('emergency_type', 'Critical Emergency')
                ->whereIn('status', ['pending', 'acknowledged'])->count(),
            'incoming' => Incident::whereDate('created_at', Carbon::today())
                ->whereIn('status', ['pending', 'acknowledged'])->count(),
            'treated_today' => Incident::whereDate('updated_at', Carbon::today())
                ->where('status', 'resolved')->count(),
            'resolved_today' => Incident::whereDate('updated_at', Carbon::today())
                ->where('status', 'resolved')->count(),
        ]);
    }

    public function alerts() { return view('clinic.alerts'); }
    public function incoming() { return view('clinic.incoming'); }
    public function logs() { return view('clinic.logs'); }
    public function patients() { return view('clinic.patients'); }
    public function reports() { return view('clinic.reports'); }
}
