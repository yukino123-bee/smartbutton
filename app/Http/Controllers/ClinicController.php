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

        return view('clinic', compact(
            'activeAlerts',
            'incomingCount',
            'treatedTodayCount',
            'resolvedTodayCount',
            'criticalIncidents',
            'recentHistory'
        ));
    }

    public function alerts() { return view('clinic.alerts'); }
    public function incoming() { return view('clinic.incoming'); }
    public function logs() { return view('clinic.logs'); }
    public function patients() { return view('clinic.patients'); }
    public function reports() { return view('clinic.reports'); }
}
