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
    public function map() { return view('ndrrmo.map'); }
    public function devices() { return view('ndrrmo.devices'); }
    public function sms() { return view('ndrrmo.sms'); }
    public function reports() { return view('ndrrmo.reports'); }
    public function users() { return view('ndrrmo.users'); }
    public function settings() { return view('ndrrmo.settings'); }
}
