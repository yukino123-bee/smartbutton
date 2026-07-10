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
            
        return view('clinic', compact('criticalIncidents'));
    }
    public function alerts() { return view('clinic.alerts'); }
    public function incoming() { return view('clinic.incoming'); }
    public function logs() { return view('clinic.logs'); }
    public function patients() { return view('clinic.patients'); }
    public function reports() { return view('clinic.reports'); }
}
