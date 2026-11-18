@extends('layouts.clinic')

@section('content')

{{-- ===== STAT CARDS ROW ===== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <x-kpi-card label="Active alerts" tone="red" :value="$activeAlerts ?? 0" value-id="stat-active-alerts" hint="Needs attention" :urgent="($activeAlerts ?? 0) > 0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></x-kpi-card>
    <x-kpi-card label="Incoming" tone="blue" :value="$incomingCount ?? 0" value-id="stat-incoming" hint="Patients today"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg></x-kpi-card>
    <x-kpi-card label="Treated today" tone="green" :value="$treatedTodayCount ?? 0" value-id="stat-treated" hint="Total patients"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></x-kpi-card>
    <x-kpi-card label="Resolved today" tone="teal" :value="$resolvedTodayCount ?? 0" value-id="stat-resolved" hint="Incidents closed"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></x-kpi-card>
</div>

@include('partials.dashboard-map-alerts', ['activeIncidents' => $criticalIncidents, 'devicesList' => $devicesList, 'alertsRoute' => route('clinic.alerts'), 'statsUrl' => '/clinic/stats-json'])

{{-- ===== BOTTOM SECTION: TABLES ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-black">

    {{-- Alert History Table --}}
    <div class="lg:col-span-2 panel flex flex-col overflow-hidden">
        <div class="panel-head">
            <h2 class="panel-title">Alert History (Today)</h2>
            <a href="{{ route('clinic.logs') }}" class="text-[11px] text-brand-blue hover:underline font-extrabold">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="px-5 py-3.5">#</th>
                        <th class="px-5 py-3.5">Time</th>
                        <th class="px-5 py-3.5">Location</th>
                        <th class="px-5 py-3.5">Device</th>
                        <th class="px-5 py-3.5">Type</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="alert-history-tbody" class="text-xs divide-y divide-slate-100">
                    @forelse($recentHistory as $index => $incident)
                    <tr class="hover:bg-slate-50 transition-colors" id="history-row-{{ $incident->id }}">
                        <td class="px-5 py-4 text-slate-900 font-bold row-no">{{ $index + 1 }}</td>
                        <td class="px-5 py-4 font-black text-slate-900">{{ $incident->created_at->format('h:i A') }}</td>
                        <td class="px-5 py-4 font-bold text-slate-900">{{ $incident->device?->building ?? 'Location not recorded' }}</td>
                        <td class="px-5 py-4 text-slate-700 font-mono font-bold text-[11px]">{{ $incident->device?->device_code ?? 'Not recorded' }}</td>
                        <td class="px-5 py-4"><span class="inline-flex items-center bg-red-600 text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-xs">{{ $incident->emergency_type }}</span></td>
                        <td class="px-5 py-4 status-cell">
                            @if($incident->status === 'Resolved')
                                <span class="inline-flex items-center gap-1.5 text-brand-green font-black"><span class="w-2 h-2 rounded-full bg-brand-green"></span>Resolved</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-slate-700 font-bold"><span class="w-2 h-2 rounded-full bg-slate-500"></span>{{ ucfirst($incident->status) }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-xs font-bold text-slate-500">Recorded</span>
                        </td>
                    </tr>
                    @empty
                    <tr id="no-history-row">
                        <td colspan="7" class="px-5 py-8 text-center text-slate-500 font-semibold">
                            No alert history recorded today.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Response Status summary, same slot as the DRRMO Incident Summary --}}
    <div class="panel flex flex-col">
        <div class="panel-head">
            <h2 class="panel-title">Response Status (Today)</h2>
        </div>
        <div class="p-5 flex-1 flex flex-col gap-3 text-xs">
            @foreach(['Pending' => 'text-brand-orange', 'Responding' => 'text-brand-blue', 'Resolved' => 'text-brand-green'] as $label => $color)
            <div class="flex justify-between items-center">
                <span class="text-brand-text">{{ $label }}</span>
                <span class="{{ $color }} font-bold">{{ $recentHistory->where('status', $label)->count() }}</span>
            </div>
            @endforeach
            <div class="flex justify-between items-center border-t border-dashed border-brand-border pt-3">
                <span class="text-brand-text">Total today</span>
                <span class="text-brand-dark font-bold">{{ $recentHistory->count() }}</span>
            </div>
        </div>
    </div>
</div>

@endsection
