@extends('layouts.clinic')

@section('content')

{{-- Header Row --}}
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-slate-300 shadow-sm">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <div class="w-8 h-8 rounded-xl bg-blue-100 border border-blue-300 flex items-center justify-center text-blue-600 font-black text-sm">
                📊
            </div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Clinic Reports & Analytics</h2>
        </div>
        <p class="text-xs font-bold text-slate-500">Medical emergency incident metrics, patient treatment summaries, and campus reports.</p>
    </div>

    <a href="{{ route('clinic.reports.export-excel') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-extrabold text-xs rounded-xl shadow-md shadow-emerald-200 transition-all flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        Download Excel Summary
    </a>
</div>

{{-- Top Metrics: same cards as the dashboard --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <x-kpi-card label="Total clinic incidents" tone="blue" :value="$totalIncidents" hint="Recorded system-wide"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></x-kpi-card>
    <x-kpi-card label="Patients treated" tone="green" :value="$totalTreated" hint="Resolved medical cases"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></x-kpi-card>
    <x-kpi-card label="Medical emergencies" tone="amber" :value="$typeStats['Medical Emergency'] ?? 0" hint="Health & first aid calls"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></x-kpi-card>
    <x-kpi-card label="Critical alerts" tone="red" :value="$typeStats['Critical Emergency'] ?? 0" hint="Severe emergency alerts"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></x-kpi-card>
</div>

@php
    $typeBar = ['Critical Emergency' => 'bg-red-600', 'Medical Emergency' => 'bg-orange-500', 'Public Safety Emergency' => 'bg-blue-600'];
    $triageBar = ['Green' => 'bg-emerald-500', 'Yellow' => 'bg-amber-500', 'Red' => 'bg-red-600', 'Black' => 'bg-slate-800'];
    $statusRows = ['Pending' => ['bg-red-500', 'Waiting for the Clinic'], 'Acknowledged' => ['bg-amber-500', 'Acknowledged'], 'Responding' => ['bg-blue-500', 'Team dispatched'], 'Resolved' => ['bg-emerald-500', 'Treated and closed']];
    $panel = 'bg-white border border-slate-300 rounded-2xl shadow-sm overflow-hidden';
    $head = 'px-6 py-4 bg-slate-100 border-b border-slate-300 text-xs font-black text-slate-900 uppercase tracking-wider';
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="{{ $panel }}">
        <h3 class="{{ $head }}">Emergency type</h3>
        <div class="p-6 space-y-5">
            @forelse($typeStats->sortDesc() as $type => $count)
            <div>
                <div class="flex justify-between items-baseline text-xs font-bold text-slate-800 mb-1.5">
                    <span>{{ $type }}</span>
                    <span class="font-black tabular-nums">{{ $count }} {{ Str::plural('case', $count) }} · {{ $totalIncidents > 0 ? round(($count / $totalIncidents) * 100) : 0 }}%</span>
                </div>
                <div class="w-full bg-slate-100 border border-slate-200 h-3 rounded-full overflow-hidden">
                    <div class="{{ $typeBar[$type] ?? 'bg-slate-500' }} h-full rounded-full" style="width: {{ $totalIncidents > 0 ? ($count / $totalIncidents) * 100 : 0 }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-xs text-slate-500 font-bold">No incident data available yet.</p>
            @endforelse
        </div>
    </div>

    <div class="{{ $panel }}">
        <h3 class="{{ $head }}">By building</h3>
        <div class="p-6 space-y-5">
            @forelse($buildingStats->sortDesc() as $building => $count)
            <div>
                <div class="flex justify-between items-baseline text-xs font-bold text-slate-800 mb-1.5">
                    <span>{{ $building }}</span>
                    <span class="font-black tabular-nums">{{ $count }} {{ Str::plural('call', $count) }}</span>
                </div>
                <div class="w-full bg-slate-100 border border-slate-200 h-3 rounded-full overflow-hidden">
                    <div class="bg-teal-500 h-full rounded-full" style="width: {{ $totalIncidents > 0 ? ($count / $totalIncidents) * 100 : 0 }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-xs text-slate-500 font-bold">No location records logged yet.</p>
            @endforelse
        </div>
    </div>

    <div class="{{ $panel }}">
        <h3 class="{{ $head }}">Response status</h3>
        <div class="divide-y divide-slate-200">
            @foreach($statusRows as $status => [$dot, $note])
            <div class="px-6 py-3 flex items-center justify-between text-xs">
                <div class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full {{ $dot }}"></span>
                    <div>
                        <div class="font-black text-slate-900">{{ $status }}</div>
                        <div class="text-[11px] text-slate-500 font-semibold">{{ $note }}</div>
                    </div>
                </div>
                <span class="text-lg font-black text-slate-900 tabular-nums">{{ $stats[$status] ?? 0 }}</span>
            </div>
            @endforeach
        </div>
        @if($triageStats->isNotEmpty())
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-300">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Treated by triage level</div>
            <div class="flex flex-wrap gap-2">
                @foreach($triageStats as $level => $count)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white border border-slate-300 text-[11px] font-black text-slate-800">
                    <span class="w-2 h-2 rounded-full {{ $triageBar[$level] ?? 'bg-slate-400' }}"></span>{{ $level }} · {{ $count }}
                </span>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
