<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClinicController extends Controller
{
    public function dashboard() { 
        $criticalIncidents = \App\Models\Incident::with('device')
            ->where('emergency_type', 'Critical Emergency')
            ->whereIn('status', ['pending', 'acknowledged'])
            ->get();
            
        $equipment = \App\Models\Equipment::all();
        
        return view('clinic', compact('criticalIncidents', 'equipment'));
    }
    public function alerts() { return view('clinic.alerts'); }
    public function incoming() { return view('clinic.incoming'); }
    public function logs() { return view('clinic.logs'); }
    public function patients() { return view('clinic.patients'); }
    public function equipment() { return view('clinic.equipment'); }
    public function reports() { return view('clinic.reports'); }
    public function users() { return view('clinic.users'); }
    public function settings() { return view('clinic.settings'); }
}
