@extends('layouts.ndrrmo')

@section('content')
<div class="mb-6 flex justify-between items-center flex-wrap gap-4">
    <div>
        <h2 class="text-xl font-bold text-slate-900 mb-1">Incident Reports & Emergency Analytics</h2>
        <p class="text-xs text-slate-500">Comprehensive statistical overview of campus emergency alerts, response rates, and unit efficiency.</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="window.print()" class="bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-xl px-4 py-2 text-xs font-bold transition-all shadow-xs flex items-center gap-2 cursor-pointer">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print Official Report
        </button>
        <a href="{{ route('ndrrmo.reports.export-excel') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl px-4 py-2 text-xs font-bold transition-all shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export Excel Spreadsheet
        </a>
    </div>
</div>

<!-- 4 Key Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs relative overflow-hidden">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Total Emergencies</span>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
        </div>
        <div class="text-3xl font-black text-slate-900 mb-1">{{ $totalIncidents }}</div>
        <p class="text-[11px] text-slate-500 font-medium">Records currently stored in the system</p>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs relative overflow-hidden">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-extrabold uppercase tracking-wider text-red-600">Pending Alerts</span>
            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>
        <div class="text-3xl font-black text-slate-900 mb-1">{{ $stats['pending'] ?? 0 }}</div>
        <p class="text-[11px] text-slate-500 font-medium">Requires immediate response</p>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs relative overflow-hidden">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-extrabold uppercase tracking-wider text-amber-600">Active Responding</span>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
        </div>
        <div class="text-3xl font-black text-slate-900 mb-1">{{ $stats['responding'] ?? 0 }}</div>
        <p class="text-[11px] text-slate-500 font-medium">Responders dispatched</p>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs relative overflow-hidden">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-600">Resolved Incidents</span>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="text-3xl font-black text-slate-900 mb-1">{{ $stats['resolved'] ?? 0 }}</div>
        <p class="text-[11px] text-slate-500 font-medium">Successfully completed</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Category Breakdown Bar Chart -->
    <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-6 shadow-xs">
        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider mb-6 flex items-center justify-between">
            <span>Incidents by Emergency Category</span>
            <span class="text-xs text-slate-500 font-normal">Campus Analytics</span>
        </h3>
        
        <div class="space-y-6">
            @foreach($typeStats as $type => $count)
                @php
                    $percentage = $totalIncidents > 0 ? round(($count / $totalIncidents) * 100) : 0;
                    $barBg = 'bg-slate-600';
                    $badgeBg = 'bg-slate-100 text-slate-800';
                    if (str_contains(strtolower($type), 'medical')) {
                        $barBg = 'bg-orange-500';
                        $badgeBg = 'bg-orange-50 text-orange-700';
                    } elseif (str_contains(strtolower($type), 'critical') || str_contains(strtolower($type), 'general')) {
                        $barBg = 'bg-red-600';
                        $badgeBg = 'bg-red-50 text-red-700';
                    } elseif (str_contains(strtolower($type), 'public')) {
                        $barBg = 'bg-amber-500';
                        $badgeBg = 'bg-amber-50 text-amber-700';
                    }
                @endphp
                <div>
                    <div class="flex justify-between items-center mb-2 text-xs">
                        <span class="font-extrabold text-slate-800">{{ $type }}</span>
                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black {{ $badgeBg }}">{{ $count }} incident(s) • {{ $percentage }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden p-0.5 border border-slate-200">
                        <div class="{{ $barBg }} h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            @endforeach
            
            @if($typeStats->isEmpty())
                <div class="text-center py-12 text-slate-400 text-sm">No incident breakdown data available yet.</div>
            @endif
        </div>
    </div>
    
    <!-- Average Response Speed Efficiency -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col items-center justify-center text-center">
        <div class="w-24 h-24 rounded-full border-4 border-emerald-500 bg-emerald-50 flex items-center justify-center mb-4 relative shadow-inner">
            <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="text-xs text-slate-500 uppercase font-black tracking-wider mb-1">AVERAGE RESOLUTION TIME</div>
        <div class="text-4xl font-black text-slate-900 mb-2">
            @if($averageResolutionSeconds !== null)
                {{ intdiv((int) round($averageResolutionSeconds), 60) }}m {{ (int) round($averageResolutionSeconds) % 60 }}s
            @else
                —
            @endif
        </div>
        <p class="text-xs text-slate-500 leading-relaxed max-w-xs">
            {{ $averageResolutionSeconds !== null ? 'Calculated from report time to recorded resolution time.' : 'No resolved incident timing data is available yet.' }}
        </p>
    </div>
</div>
@endsection
