@extends('layouts.clinic')

@section('content')

{{-- ===== STAT CARDS ROW (Enhanced Clinical Cards) ===== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Active Alerts --}}
    <div class="bg-white/90 backdrop-blur-xs border {{ ($activeAlerts ?? 0) > 0 ? 'border-red-300 ring-2 ring-red-500/20 bg-red-50/20' : 'border-slate-200/90' }} rounded-2xl p-4 sm:p-5 flex items-center justify-between shadow-xs hover:shadow-md transition-all duration-200 group">
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="w-12 h-12 rounded-xl {{ ($activeAlerts ?? 0) > 0 ? 'bg-red-500 text-white shadow-md shadow-red-200' : 'bg-red-50 text-brand-red border border-red-200' }} flex items-center justify-center shrink-0 transition-transform group-hover:scale-105">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest truncate">ACTIVE ALERTS</div>
                <div id="stat-active-alerts" class="text-3xl font-black text-brand-red leading-tight tabular-nums">{{ $activeAlerts ?? 0 }}</div>
                <div class="text-[11px] font-bold text-brand-red">Needs attention</div>
            </div>
        </div>
        @if(($activeAlerts ?? 0) > 0)
            <span class="w-2.5 h-2.5 rounded-full bg-red-600 animate-ping shrink-0 mr-1"></span>
        @endif
    </div>

    {{-- Incoming Patients --}}
    <div class="bg-white/90 backdrop-blur-xs border border-slate-200/90 rounded-2xl p-4 sm:p-5 flex items-center justify-between shadow-xs hover:shadow-md transition-all duration-200 group">
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-200 text-brand-blue flex items-center justify-center shrink-0 transition-transform group-hover:scale-105">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest truncate">INCOMING</div>
                <div id="stat-incoming" class="text-3xl font-black text-black leading-tight tabular-nums">{{ $incomingCount ?? 0 }}</div>
                <div class="text-[11px] font-bold text-slate-700">Patient today</div>
            </div>
        </div>
    </div>

    {{-- Patients Treated --}}
    <div class="bg-white/90 backdrop-blur-xs border border-slate-200/90 rounded-2xl p-4 sm:p-5 flex items-center justify-between shadow-xs hover:shadow-md transition-all duration-200 group">
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="w-12 h-12 rounded-xl bg-green-50 border border-green-200 text-brand-green flex items-center justify-center shrink-0 transition-transform group-hover:scale-105">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest truncate">TREATED TODAY</div>
                <div id="stat-treated" class="text-3xl font-black text-brand-green leading-tight tabular-nums">{{ $treatedTodayCount ?? 0 }}</div>
                <div class="text-[11px] font-bold text-slate-700">Total patients</div>
            </div>
        </div>
    </div>

    {{-- Resolved Incidents --}}
    <div class="bg-white/90 backdrop-blur-xs border border-slate-200/90 rounded-2xl p-4 sm:p-5 flex items-center justify-between shadow-xs hover:shadow-md transition-all duration-200 group">
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="w-12 h-12 rounded-xl bg-teal-50 border border-teal-200 text-brand-teal flex items-center justify-center shrink-0 transition-transform group-hover:scale-105">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest truncate">RESOLVED TODAY</div>
                <div id="stat-resolved" class="text-3xl font-black text-black leading-tight tabular-nums">{{ $resolvedTodayCount ?? 0 }}</div>
                <div class="text-[11px] font-bold text-slate-700">Incidents closed</div>
            </div>
        </div>
    </div>
</div>

@if($criticalIncidents->count() > 1)
{{-- ===== MULTIPLE ACTIVE EMERGENCIES BANNER ===== --}}
<div class="mb-6 space-y-4">
    <div class="bg-red-600 border-2 border-red-500 rounded-3xl px-6 py-4 flex flex-wrap items-center justify-between gap-3 shadow-lg text-white">
        <div class="flex items-center gap-3">
            <span class="w-3.5 h-3.5 rounded-full bg-white animate-ping"></span>
            <div>
                <span class="font-black text-sm uppercase tracking-wider block">🚨 MULTIPLE ACTIVE MEDICAL EMERGENCIES ({{ $criticalIncidents->count() }} UNHANDLED ALERTS)</span>
                <span class="text-xs text-red-100 font-medium">Multiple emergency panic alarms require immediate medical attention and response. Interconnected with Campus DRRMO.</span>
            </div>
        </div>
        <form method="POST" action="{{ route('clinic.alerts.acknowledge-all') }}" class="shrink-0">
            @csrf
            <button type="submit" class="px-4 py-2 bg-white hover:bg-red-50 text-red-600 active:scale-95 rounded-xl font-black text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Acknowledge All ({{ $criticalIncidents->count() }} Alerts)</span>
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach($criticalIncidents as $index => $incident)
        @php
            $drrmoDispatched = $incident->notifications->where('recipient', 'DRRMO')->where('status', 'Dispatched')->isNotEmpty()
                || $incident->notifications->where('status', 'DRRMO Responders Dispatched')->isNotEmpty();
            $drrmoAck = $incident->notifications->where('recipient', 'DRRMO')->where('status', 'Acknowledged')->isNotEmpty()
                || $incident->notifications->where('status', 'Acknowledged by DRRMO')->isNotEmpty();
            
            $isPending = $incident->status === 'Pending';
            $isAck = $incident->status === 'Acknowledged';
            $isResponding = $incident->status === 'Responding';
        @endphp
        <div id="clinic-incident-card-{{ $incident->id }}"
             x-data="{ 
                 openClinicDispatch: false, 
                 openClinicTriage: false, 
                 unitName: '{{ addslashes($incident->responder_name ?? 'Clinic Medical Team 1') }}', 
                 contact: '{{ addslashes($incident->responder_contact ?? '0917-555-0199') }}', 
                 eta: {{ $incident->eta_minutes ?? 3 }}, 
                 resolveType: '{{ $incident->resolution_type ?? 'Resolved' }}',
                 triageLevel: '{{ $incident->triage_level ?? 'Yellow' }}',
                 patientName: '{{ addslashes($incident->patient_name ?? '') }}',
                 patientId: '{{ addslashes($incident->patient_id_number ?? '') }}',
                 disposition: '{{ addslashes($incident->disposition ?? 'Treated and released to class') }}'
             }"
             class="bg-white border-2 border-red-500 rounded-3xl overflow-hidden shadow-md flex flex-col justify-between">
            <div>
                {{-- Header --}}
                <div class="bg-red-600 px-5 py-2.5 flex items-center justify-between text-white">
                    <span class="font-black text-xs uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                        Alert #{{ $index + 1 }}: {{ $incident->emergency_type }}
                    </span>
                    <span class="text-[11px] font-bold bg-black/25 px-2.5 py-0.5 rounded-full">
                        {{ $incident->created_at->format('h:i A') }}
                    </span>
                </div>

                {{-- Location Details --}}
                <div class="p-5">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-red-100 border border-red-300 flex items-center justify-center shrink-0 text-red-600 text-xl font-black shadow-xs">
                            🚨
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs font-bold text-slate-700">
                                <div>
                                    <span class="text-slate-400 font-extrabold uppercase text-[10px] block">LOCATION</span>
                                    <span class="font-black text-slate-900 text-sm">{{ $incident->device?->building ?? 'Location not recorded' }}</span>
                                    @if($incident->device?->room)
                                        <span class="text-slate-500 text-xs block font-semibold">{{ $incident->device->room }}</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-slate-400 font-extrabold uppercase text-[10px] block">DEVICE CODE</span>
                                    <span class="font-mono font-bold text-slate-800">{{ $incident->device?->device_code ?? 'Not recorded' }}</span>
                                    <span class="text-slate-500 text-[11px] block pt-0.5 font-normal">Reported {{ $incident->reported_at?->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DISPATCHED MEDICAL TEAM STRIP --}}
                    @if($isResponding || $incident->responder_name)
                    <div class="mb-4 bg-blue-50/90 border border-blue-200 rounded-2xl p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-black text-base shrink-0 shadow-xs">
                                🩺
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-black text-blue-900 uppercase">MEDICAL TEAM:</span>
                                    <span class="text-xs font-extrabold text-blue-700">{{ $incident->responder_name ?: 'Clinic Medical Team' }}</span>
                                    @if($incident->arrived_at)
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black flex items-center gap-1 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> ON SCENE ({{ $incident->arrived_at->format('h:i A') }})
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black animate-pulse flex items-center gap-1 border border-blue-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> EN ROUTE (ETA: {{ $incident->eta_minutes ?: 3 }}m)
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-600 flex flex-wrap items-center gap-3 mt-0.5 font-medium">
                                    <span>📞 {{ $incident->responder_contact ?: '0917-555-0199' }}</span>
                                    @if($incident->dispatch_notes)
                                        <span>📋 {{ $incident->dispatch_notes }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if(!$incident->arrived_at)
                        <form method="POST" action="{{ route('clinic.incidents.on-scene', $incident) }}" class="shrink-0">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-extrabold text-xs rounded-xl shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Mark On Scene</span>
                            </button>
                        </form>
                        @endif
                    </div>
                    @endif

                    {{-- 5-Step Medical Response Lifecycle Stepper --}}
                    <div class="border border-slate-200 rounded-2xl p-3.5 bg-slate-50/70 mb-3">
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                5-STEP MEDICAL RESPONSE LIFECYCLE
                            </span>
                            <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full {{ $isResponding ? 'bg-blue-100 text-blue-700 border border-blue-200' : ($isAck ? 'bg-amber-100 text-amber-700 border border-amber-200' : 'bg-red-100 text-red-700 border border-red-200 animate-pulse') }}">
                                {{ strtoupper($incident->status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-5 gap-1 text-center text-[9px]">
                            <div class="flex flex-col items-center">
                                <div class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center font-black text-[10px] shadow-xs mb-1">✓</div>
                                <span class="font-extrabold text-slate-800">1. Triggered</span>
                            </div>
                            <div class="flex flex-col items-center">
                                @if(!$isPending)
                                    <div class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center font-black text-[10px] shadow-xs mb-1">✓</div>
                                    <span class="font-extrabold text-slate-800">2. Acknowledge</span>
                                @else
                                    <div class="w-5 h-5 rounded-full bg-red-600 text-white flex items-center justify-center font-black text-[10px] shadow-xs mb-1 animate-ping">2</div>
                                    <span class="font-extrabold text-red-600">2. Acknowledge</span>
                                @endif
                            </div>
                            <div class="flex flex-col items-center">
                                @if($isResponding)
                                    <div class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-[10px] shadow-xs mb-1 animate-pulse">✓</div>
                                    <span class="font-extrabold text-blue-700">3. Dispatch</span>
                                @elseif($isAck)
                                    <div class="w-5 h-5 rounded-full bg-amber-500 text-white flex items-center justify-center font-black text-[10px] shadow-xs mb-1">3</div>
                                    <span class="font-extrabold text-amber-700">3. Dispatch</span>
                                @else
                                    <div class="w-5 h-5 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center font-bold text-[10px] mb-1">3</div>
                                    <span class="font-bold text-slate-400">3. Dispatch</span>
                                @endif
                            </div>
                            <div class="flex flex-col items-center">
                                @if($incident->arrived_at)
                                    <div class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center font-black text-[10px] shadow-xs mb-1">✓</div>
                                    <span class="font-extrabold text-slate-800">4. Triage</span>
                                @elseif($isResponding)
                                    <div class="w-5 h-5 rounded-full bg-blue-500 text-white flex items-center justify-center font-black text-[10px] shadow-xs mb-1 animate-pulse">4</div>
                                    <span class="font-extrabold text-blue-700">4. Triage</span>
                                @else
                                    <div class="w-5 h-5 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center font-bold text-[10px] mb-1">4</div>
                                    <span class="font-bold text-slate-400">4. Triage</span>
                                @endif
                            </div>
                            <div class="flex flex-col items-center">
                                <div class="w-5 h-5 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center font-bold text-[10px] mb-1">5</div>
                                <span class="font-bold text-slate-400">5. Resolution</span>
                            </div>
                        </div>

                        {{-- DRRMO Inter-Agency Connection Strip --}}
                        <div class="mt-2.5 pt-2 border-t border-slate-200 flex items-center justify-between text-[10px]">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full {{ $drrmoDispatched ? 'bg-blue-600 animate-pulse' : ($drrmoAck ? 'bg-emerald-500' : 'bg-slate-400') }}"></span>
                                <span class="font-extrabold text-slate-700">DRRMO Coordination:</span>
                                <span class="font-black {{ $drrmoDispatched ? 'text-blue-700' : ($drrmoAck ? 'text-emerald-700' : 'text-slate-500') }}">
                                    {{ $drrmoDispatched ? 'Security & Responders En Route' : ($drrmoAck ? 'Command Center Acknowledged' : 'Alert Broadcasted') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- TIMESTAMPED RESPONSE AUDIT TRAIL --}}
                    <div class="border border-slate-200 rounded-2xl p-3 bg-white mb-3">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 flex items-center gap-1">
                                ⏱️ RESPONSE AUDIT MILESTONES
                            </span>
                            @if($incident->ack_duration)
                                <span class="text-[9px] font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                    Ack: {{ $incident->ack_duration }}
                                </span>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5 text-[10px]">
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-1.5">
                                <span class="text-slate-400 font-bold block text-[9px] uppercase">1. Reported</span>
                                <span class="font-black text-slate-900">{{ $incident->reported_at?->format('h:i:s A') }}</span>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-1.5">
                                <span class="text-slate-400 font-bold block text-[9px] uppercase">2. Acknowledged</span>
                                <span class="font-black text-slate-900">{{ $incident->acknowledged_at ? $incident->acknowledged_at->format('h:i:s A') : 'Pending' }}</span>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-1.5">
                                <span class="text-slate-400 font-bold block text-[9px] uppercase">3. Dispatched</span>
                                <span class="font-black text-slate-900">{{ $incident->dispatched_at ? $incident->dispatched_at->format('h:i:s A') : 'Standby' }}</span>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-1.5">
                                <span class="text-slate-400 font-bold block text-[9px] uppercase">4. On Scene</span>
                                <span class="font-black text-slate-900">{{ $incident->arrived_at ? $incident->arrived_at->format('h:i:s A') : 'En Route' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Checklist --}}
                    <div class="grid grid-cols-2 gap-1.5 text-[10px] font-bold text-slate-600">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            <span>Prepare medical stretcher</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            <span>Deploy first aid & oxygen</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Action Buttons --}}
            <div class="p-4 bg-red-50/70 border-t border-red-100 flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    @if($isPending)
                    <form method="POST" action="{{ route('clinic.incidents.acknowledge', $incident) }}">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 active:scale-95 text-white font-extrabold py-2 px-3.5 rounded-xl shadow-xs transition-all flex items-center gap-1.5 text-xs cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Acknowledge</span>
                        </button>
                    </form>
                    @endif

                    @if($isPending || $isAck)
                    <button type="button" @click="openClinicDispatch = true" class="bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-extrabold py-2 px-3.5 rounded-xl shadow-xs transition-all flex items-center gap-1.5 text-xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Dispatch Medical Team</span>
                    </button>
                    @else
                    <button type="button" @click="openClinicDispatch = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-black transition-all cursor-pointer">
                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                        Update Dispatch ({{ $incident->responder_name }})
                    </button>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" @click="resolveType = 'False Alarm'; openClinicTriage = true" class="bg-white border border-slate-300 hover:bg-slate-100 active:scale-95 text-slate-700 font-bold py-2 px-3 rounded-xl text-xs shadow-xs transition-all cursor-pointer">
                        ⚠️ False Alarm
                    </button>

                    <button type="button" @click="resolveType = 'Resolved'; openClinicTriage = true" class="bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-extrabold py-2 px-4 rounded-xl shadow-xs transition-all flex items-center gap-1.5 text-xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Mark Treated & Resolved</span>
                    </button>
                </div>
            </div>

            {{-- CLINIC DISPATCH MODAL --}}
            <div x-show="openClinicDispatch" x-cloak class="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" @click.self="openClinicDispatch = false">
                <div class="bg-white border-2 border-blue-500 rounded-3xl p-6 shadow-2xl w-full max-w-lg text-slate-800" @click.stop>
                    <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-black text-lg">
                                🩺
                            </div>
                            <div>
                                <h3 class="font-black text-sm text-slate-900 uppercase">Dispatch Medical Responders</h3>
                                <p class="text-xs text-slate-500">Alert #{{ $index + 1 }} · {{ $incident->device?->building }}</p>
                            </div>
                        </div>
                        <button type="button" @click="openClinicDispatch = false" class="text-slate-400 hover:text-slate-600 font-black text-lg cursor-pointer">✕</button>
                    </div>

                    <form method="POST" action="{{ route('clinic.incidents.dispatch', $incident) }}" class="space-y-4">
                        @csrf
                        {{-- Presets --}}
                        <div>
                            <label class="block text-[11px] font-black uppercase text-slate-500 mb-1.5">1-Click Medical Presets</label>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <button type="button" @click="unitName = 'Clinic Emergency Team Alpha'; contact = '0917-555-0199'; eta = 2;" class="p-2.5 border border-slate-200 rounded-xl text-left hover:border-blue-500 hover:bg-blue-50 transition-all cursor-pointer">
                                    <span class="font-extrabold text-slate-800 block text-xs">Nurse Team Alpha</span>
                                    <span class="text-[10px] text-slate-500">ETA 2 mins · Nurse on duty</span>
                                </button>
                                <button type="button" @click="unitName = 'Campus First Aid Volunteers'; contact = '0928-111-2233'; eta = 3;" class="p-2.5 border border-slate-200 rounded-xl text-left hover:border-blue-500 hover:bg-blue-50 transition-all cursor-pointer">
                                    <span class="font-extrabold text-slate-800 block text-xs">First Aid Volunteers</span>
                                    <span class="text-[10px] text-slate-500">ETA 3 mins · Red Cross Youth</span>
                                </button>
                                <button type="button" @click="unitName = 'Ambulance Transport Team'; contact = '0939-999-4455'; eta = 5;" class="p-2.5 border border-slate-200 rounded-xl text-left hover:border-blue-500 hover:bg-blue-50 transition-all cursor-pointer">
                                    <span class="font-extrabold text-slate-800 block text-xs">Ambulance Team</span>
                                    <span class="text-[10px] text-slate-500">ETA 5 mins · Mobile Stretcher</span>
                                </button>
                                <button type="button" @click="unitName = 'Attending School Physician'; contact = '0919-888-7766'; eta = 4;" class="p-2.5 border border-slate-200 rounded-xl text-left hover:border-blue-500 hover:bg-blue-50 transition-all cursor-pointer">
                                    <span class="font-extrabold text-slate-800 block text-xs">School Physician</span>
                                    <span class="text-[10px] text-slate-500">ETA 4 mins · Doctor Response</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Medical Unit / Nurse</label>
                                <input type="text" name="responder_name" x-model="unitName" required class="w-full text-xs font-bold px-3 py-2 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none" placeholder="e.g. Nurse Santos">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Mobile / Radio</label>
                                <input type="text" name="responder_contact" x-model="contact" class="w-full text-xs font-bold px-3 py-2 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none" placeholder="e.g. 0917-555-0199">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Estimated Arrival (ETA in Minutes)</label>
                            <input type="number" name="eta_minutes" x-model="eta" min="1" max="120" required class="w-full text-xs font-bold px-3 py-2 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none" placeholder="e.g. 3">
                        </div>

                        <div>
                            <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Medical Instructions / Equipments Required</label>
                            <textarea name="dispatch_notes" rows="2" class="w-full text-xs font-semibold px-3 py-2 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none" placeholder="e.g. Bring trauma bag, cervical collar, portable oxygen unit"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                            <button type="button" @click="openClinicDispatch = false" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-xs rounded-xl cursor-pointer">Cancel</button>
                            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-xl shadow-md transition-all cursor-pointer">Confirm & Dispatch</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- CLINIC CASUALTY TRIAGE & RESOLVE MODAL --}}
            <div x-show="openClinicTriage" x-cloak class="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" @click.self="openClinicTriage = false">
                <div class="bg-white border-2 border-emerald-500 rounded-3xl p-6 shadow-2xl w-full max-w-lg text-slate-800 max-h-[90vh] overflow-y-auto" @click.stop>
                    <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-black text-lg">
                                🏥
                            </div>
                            <div>
                                <h3 class="font-black text-sm text-slate-900 uppercase">Casualty Triage & Case Resolution</h3>
                                <p class="text-xs text-slate-500">Alert #{{ $index + 1 }} · {{ $incident->device?->building }}</p>
                            </div>
                        </div>
                        <button type="button" @click="openClinicTriage = false" class="text-slate-400 hover:text-slate-600 font-black text-lg cursor-pointer">✕</button>
                    </div>

                    <form method="POST" action="{{ route('clinic.incidents.resolve', $incident) }}" class="space-y-4">
                        @csrf
                        {{-- Classification Radio Buttons --}}
                        <div>
                            <label class="block text-[11px] font-black uppercase text-slate-500 mb-1.5">Resolution Classification</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label :class="resolveType === 'Resolved' ? 'border-emerald-600 bg-emerald-50 text-emerald-900 shadow-xs' : 'border-slate-200 bg-white text-slate-700'" class="p-2.5 border-2 rounded-xl text-center cursor-pointer font-extrabold text-xs transition-all flex flex-col items-center gap-1">
                                    <input type="radio" name="resolution_type" value="Resolved" x-model="resolveType" class="sr-only">
                                    <span>✅ Treated</span>
                                    <span class="text-[9px] font-normal text-slate-500">Patient Attended</span>
                                </label>
                                <label :class="resolveType === 'False Alarm' ? 'border-amber-600 bg-amber-50 text-amber-900 shadow-xs' : 'border-slate-200 bg-white text-slate-700'" class="p-2.5 border-2 rounded-xl text-center cursor-pointer font-extrabold text-xs transition-all flex flex-col items-center gap-1">
                                    <input type="radio" name="resolution_type" value="False Alarm" x-model="resolveType" class="sr-only">
                                    <span>⚠️ False Alarm</span>
                                    <span class="text-[9px] font-normal text-slate-500">Accidental Trigger</span>
                                </label>
                                <label :class="resolveType === 'Drill' ? 'border-purple-600 bg-purple-50 text-purple-900 shadow-xs' : 'border-slate-200 bg-white text-slate-700'" class="p-2.5 border-2 rounded-xl text-center cursor-pointer font-extrabold text-xs transition-all flex flex-col items-center gap-1">
                                    <input type="radio" name="resolution_type" value="Drill" x-model="resolveType" class="sr-only">
                                    <span>🛡️ Medical Drill</span>
                                    <span class="text-[9px] font-normal text-slate-500">First Aid Practice</span>
                                </label>
                            </div>
                        </div>

                        {{-- False Alarm Reason --}}
                        <div x-show="resolveType === 'False Alarm'" class="space-y-2">
                            <label class="block text-[11px] font-black uppercase text-amber-800">False Alarm Reason</label>
                            <select name="false_alarm_reason" class="w-full text-xs font-bold px-3 py-2 border border-amber-300 rounded-xl bg-amber-50/50 focus:outline-none">
                                <option value="Accidental button push by student / staff">Accidental button push by student / staff</option>
                                <option value="Sensor / hardware glitch">Sensor / hardware glitch</option>
                                <option value="Curiosity / unintended test">Curiosity / unintended test</option>
                                <option value="Prank / unverified report">Prank / unverified report</option>
                                <option value="Routine inspection / clinic test">Routine inspection / clinic test</option>
                            </select>
                        </div>

                        {{-- Drill Type --}}
                        <div x-show="resolveType === 'Drill'" class="space-y-2">
                            <label class="block text-[11px] font-black uppercase text-purple-800">Medical Simulation Type</label>
                            <select name="remarks" class="w-full text-xs font-bold px-3 py-2 border border-purple-300 rounded-xl bg-purple-50/50 focus:outline-none">
                                <option value="Campus Mass Casualty Incident Simulation">Campus Mass Casualty Incident Simulation</option>
                                <option value="Basic Life Support & First Aid Drill">Basic Life Support & First Aid Drill</option>
                                <option value="Evacuation Triage Station Exercise">Evacuation Triage Station Exercise</option>
                                <option value="DRRMO-Clinic Joint Response Drill">DRRMO-Clinic Joint Response Drill</option>
                            </select>
                        </div>

                        {{-- Patient & Triage Details (for Real Incidents) --}}
                        <div x-show="resolveType === 'Resolved'" class="space-y-3.5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Patient Full Name</label>
                                    <input type="text" name="patient_name" x-model="patientName" class="w-full text-xs font-bold px-3 py-2 border border-slate-300 rounded-xl focus:border-emerald-500 focus:outline-none" placeholder="e.g. Maria Santos">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Student / Employee ID</label>
                                    <input type="text" name="patient_id_number" x-model="patientId" class="w-full text-xs font-bold px-3 py-2 border border-slate-300 rounded-xl focus:border-emerald-500 focus:outline-none" placeholder="e.g. 2024-12345">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-black uppercase text-slate-700 mb-1.5">Triage Severity Level</label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                                    <label :class="triageLevel === 'Red' ? 'border-red-600 bg-red-50 text-red-900 font-black' : 'border-slate-200 text-slate-700'" class="p-2 border-2 rounded-xl text-center cursor-pointer transition-all flex flex-col items-center">
                                        <input type="radio" name="triage_level" value="Red" x-model="triageLevel" class="sr-only">
                                        <span class="text-sm">🔴 Red</span>
                                        <span class="text-[9px] text-red-600 font-extrabold uppercase">Immediate</span>
                                    </label>
                                    <label :class="triageLevel === 'Yellow' ? 'border-amber-500 bg-amber-50 text-amber-900 font-black' : 'border-slate-200 text-slate-700'" class="p-2 border-2 rounded-xl text-center cursor-pointer transition-all flex flex-col items-center">
                                        <input type="radio" name="triage_level" value="Yellow" x-model="triageLevel" class="sr-only">
                                        <span class="text-sm">🟡 Yellow</span>
                                        <span class="text-[9px] text-amber-600 font-extrabold uppercase">Delayed</span>
                                    </label>
                                    <label :class="triageLevel === 'Green' ? 'border-emerald-600 bg-emerald-50 text-emerald-900 font-black' : 'border-slate-200 text-slate-700'" class="p-2 border-2 rounded-xl text-center cursor-pointer transition-all flex flex-col items-center">
                                        <input type="radio" name="triage_level" value="Green" x-model="triageLevel" class="sr-only">
                                        <span class="text-sm">🟢 Green</span>
                                        <span class="text-[9px] text-emerald-600 font-extrabold uppercase">Minor</span>
                                    </label>
                                    <label :class="triageLevel === 'Black' ? 'border-slate-800 bg-slate-100 text-slate-900 font-black' : 'border-slate-200 text-slate-700'" class="p-2 border-2 rounded-xl text-center cursor-pointer transition-all flex flex-col items-center">
                                        <input type="radio" name="triage_level" value="Black" x-model="triageLevel" class="sr-only">
                                        <span class="text-sm">⚫ Black</span>
                                        <span class="text-[9px] text-slate-600 font-extrabold uppercase">Expectant</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Treatment & Clinical Care Given</label>
                                <textarea name="treatment_summary" rows="2" class="w-full text-xs font-semibold px-3 py-2 border border-slate-300 rounded-xl focus:border-emerald-500 focus:outline-none" placeholder="e.g. Wound cleaned and dressed, vital signs taken (BP 120/80), rest administered with cold compress."></textarea>
                            </div>

                            <div>
                                <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Patient Disposition</label>
                                <select name="disposition" x-model="disposition" class="w-full text-xs font-bold px-3 py-2 border border-slate-300 rounded-xl focus:border-emerald-500 focus:outline-none">
                                    <option value="Treated and released to class">Treated and released to class</option>
                                    <option value="Admitted to Clinic observation bed">Admitted to Clinic observation bed</option>
                                    <option value="Referred / Transferred to external hospital via EMS">Referred / Transferred to external hospital via EMS</option>
                                    <option value="Released into care of parent / guardian">Released into care of parent / guardian</option>
                                    <option value="Sent home for rest">Sent home for rest</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                            <button type="button" @click="openClinicTriage = false" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-xs rounded-xl cursor-pointer">Cancel</button>
                            <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md transition-all cursor-pointer">Save & Mark Resolved</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@elseif($criticalIncidents->count() === 1)
{{-- ===== SINGLE ACTIVE EMERGENCY BANNER (Dynamic) ===== --}}
@php
    $incident = $activeEmergency;
    $drrmoDispatched = $incident?->notifications->where('recipient', 'DRRMO')->where('status', 'Dispatched')->isNotEmpty()
        || $incident?->notifications->where('status', 'DRRMO Responders Dispatched')->isNotEmpty();
    $drrmoAck = $incident?->notifications->where('recipient', 'DRRMO')->where('status', 'Acknowledged')->isNotEmpty()
        || $incident?->notifications->where('status', 'Acknowledged by DRRMO')->isNotEmpty();
    
    $isPending = $incident?->status === 'Pending';
    $isAck = $incident?->status === 'Acknowledged';
    $isResponding = $incident?->status === 'Responding';
@endphp
<div id="active-emergency-banner" 
     x-data="{ 
         openClinicDispatch: false, 
         openClinicTriage: false, 
         unitName: '{{ addslashes($activeEmergency->responder_name ?? 'Clinic Medical Team 1') }}', 
         contact: '{{ addslashes($activeEmergency->responder_contact ?? '0917-555-0199') }}', 
         eta: {{ $activeEmergency->eta_minutes ?? 3 }}, 
         resolveType: '{{ $activeEmergency->resolution_type ?? 'Resolved' }}',
         triageLevel: '{{ $activeEmergency->triage_level ?? 'Yellow' }}',
         patientName: '{{ addslashes($activeEmergency->patient_name ?? '') }}',
         patientId: '{{ addslashes($activeEmergency->patient_id_number ?? '') }}',
         disposition: '{{ addslashes($activeEmergency->disposition ?? 'Treated and released to class') }}'
     }"
     class="bg-white border-2 border-red-500 rounded-3xl mb-6 overflow-hidden shadow-lg text-black">
    {{-- Header bar --}}
    <div class="bg-red-600 px-6 py-3.5 flex flex-wrap items-center justify-between gap-3 text-white">
        <div class="flex items-center gap-3">
            <span class="w-3 h-3 rounded-full bg-white animate-ping shrink-0"></span>
            <span class="text-white font-black text-xs md:text-sm uppercase tracking-widest">⚠ {{ $activeEmergency?->emergency_type }} — ACTIVE RESPONSE</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-[11px] font-extrabold bg-black/25 text-white px-3 py-1 rounded-full flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full {{ $drrmoDispatched ? 'bg-blue-300 animate-pulse' : ($drrmoAck ? 'bg-emerald-300' : 'bg-white/80') }}"></span>
                <span>DRRMO: {{ $drrmoDispatched ? 'Dispatched' : ($drrmoAck ? 'Acknowledged' : 'Linked') }}</span>
            </span>
            <span id="emergency-timestamp" class="text-white text-xs font-extrabold bg-black/30 px-3.5 py-1 rounded-full">
                {{ $activeEmergency ? $activeEmergency->created_at->format('h:i A · M d, Y') : '' }}
            </span>
        </div>
    </div>

    {{-- Balanced 2-Panel Emergency Console Body --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 p-6">
        {{-- Left / Operations & Timeline (Col 7) --}}
        <div class="lg:col-span-7 space-y-4">
            {{-- Incident Hero Details --}}
            <div class="bg-slate-50/90 border border-slate-200/90 rounded-2xl p-4.5">
                <div class="flex flex-col sm:flex-row gap-4 items-start">
                    <div class="w-13 h-13 rounded-2xl bg-red-100 border border-red-300 flex items-center justify-center shrink-0 shadow-xs text-red-600 text-2xl font-black">
                        🚨
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
                            <h2 class="text-xl font-black text-slate-900 leading-tight">{{ $activeEmergency?->emergency_type }}</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black {{ $isResponding ? 'bg-blue-100 text-blue-700 border border-blue-200' : ($isAck ? 'bg-amber-100 text-amber-700 border border-amber-200' : 'bg-red-100 text-red-700 border border-red-200 animate-pulse') }}">
                                {{ strtoupper($incident?->status ?? 'PENDING') }}
                            </span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs font-bold text-slate-700">
                            <div>
                                <span class="text-slate-400 font-extrabold uppercase tracking-wider text-[10px] block">BUILDING:</span>
                                <span id="emergency-building" class="font-black text-slate-900 text-sm">{{ $activeEmergency?->device?->building ?? 'Location not recorded' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 font-extrabold uppercase tracking-wider text-[10px] block">DEVICE:</span>
                                <div class="flex items-center gap-1.5">
                                    <span id="emergency-device-code" class="font-mono font-black text-slate-900">{{ $activeEmergency?->device?->device_code ?? 'Not recorded' }}</span>
                                    @if($activeEmergency?->device?->room)
                                        <span id="emergency-device-name" class="text-slate-500 font-semibold text-xs">({{ $activeEmergency->device->room }})</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="mt-2.5 pt-2 border-t border-slate-200/80 flex items-center justify-between text-[11px] text-slate-500 font-semibold">
                            <span>Reported {{ $activeEmergency?->reported_at?->format('h:i:s A') }}</span>
                            <span class="font-bold text-red-600">{{ $activeEmergency?->reported_at?->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DISPATCHED MEDICAL TEAM STRIP --}}
            @if($isResponding || $activeEmergency?->responder_name)
            <div class="bg-blue-50/90 border border-blue-200/90 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-black text-lg shrink-0 shadow-xs">
                        🩺
                    </div>
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-[10px] font-black text-blue-900 uppercase tracking-wider">MEDICAL TEAM:</span>
                            <span class="text-xs font-black text-blue-800">{{ $activeEmergency->responder_name ?: 'Clinic Medical Team' }}</span>
                            @if($activeEmergency->arrived_at)
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black flex items-center gap-1 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> ON SCENE ({{ $activeEmergency->arrived_at->format('h:i A') }})
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black animate-pulse flex items-center gap-1 border border-blue-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> EN ROUTE (ETA: {{ $activeEmergency->eta_minutes ?: 3 }}m)
                                </span>
                            @endif
                        </div>
                        <div class="text-[11px] text-slate-600 flex flex-wrap items-center gap-3 mt-1 font-medium">
                            <span>📞 {{ $activeEmergency->responder_contact ?: '0917-555-0199' }}</span>
                            @if($activeEmergency->dispatch_notes)
                                <span class="truncate">📋 {{ $activeEmergency->dispatch_notes }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if(!$activeEmergency->arrived_at)
                <form method="POST" action="{{ route('clinic.incidents.on-scene', $activeEmergency) }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-extrabold text-xs rounded-xl shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Mark On Scene</span>
                    </button>
                </form>
                @endif
            </div>
            @endif

            {{-- TIMESTAMPED RESPONSE AUDIT TRAIL --}}
            <div class="border border-slate-200/90 rounded-2xl p-4 bg-white shadow-xs">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                        ⏱️ RESPONSE AUDIT MILESTONES
                    </span>
                    @if($activeEmergency?->ack_duration)
                        <span class="text-[10px] font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                            Ack Time: {{ $activeEmergency->ack_duration }}
                        </span>
                    @endif
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-[10px]">
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5">
                        <span class="text-slate-400 font-bold block text-[9px] uppercase">1. Reported</span>
                        <span class="font-black text-slate-900 text-xs">{{ $activeEmergency?->reported_at?->format('h:i:s A') }}</span>
                    </div>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5">
                        <span class="text-slate-400 font-bold block text-[9px] uppercase">2. Acknowledged</span>
                        <span class="font-black text-slate-900 text-xs">{{ $activeEmergency?->acknowledged_at ? $activeEmergency->acknowledged_at->format('h:i:s A') : 'Pending' }}</span>
                    </div>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5">
                        <span class="text-slate-400 font-bold block text-[9px] uppercase">3. Dispatched</span>
                        <span class="font-black text-slate-900 text-xs">{{ $activeEmergency?->dispatched_at ? $activeEmergency->dispatched_at->format('h:i:s A') : 'Standby' }}</span>
                    </div>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5">
                        <span class="text-slate-400 font-bold block text-[9px] uppercase">4. On Scene</span>
                        <span class="font-black text-slate-900 text-xs">{{ $activeEmergency?->arrived_at ? $activeEmergency->arrived_at->format('h:i:s A') : 'En Route' }}</span>
                    </div>
                </div>
            </div>

            {{-- DRRMO Inter-Agency Connection Strip --}}
            <div class="border border-slate-200/90 rounded-2xl p-3 bg-slate-50/80 flex flex-wrap items-center justify-between gap-2 text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full {{ $drrmoDispatched ? 'bg-blue-600 animate-pulse' : ($drrmoAck ? 'bg-emerald-500' : 'bg-slate-400') }}"></span>
                    <span class="font-extrabold text-slate-700 text-[11px]">DRRMO Coordination:</span>
                    <span class="font-black {{ $drrmoDispatched ? 'text-blue-700' : ($drrmoAck ? 'text-emerald-700' : 'text-slate-600') }} text-[11px]">
                        {{ $drrmoDispatched ? 'Security & Responders En Route' : ($drrmoAck ? 'Command Center Acknowledged' : 'Alert Broadcasted & Linked') }}
                    </span>
                </div>
                <span class="text-[10px] font-bold text-slate-500">Live Campus Command Sync</span>
            </div>
        </div>

        {{-- Right / Lifecycle & Action Command Deck (Col 5) --}}
        <div class="lg:col-span-5 flex flex-col justify-between space-y-4 bg-slate-50/90 border border-slate-200/90 rounded-2xl p-5">
            {{-- 5-Step Medical Response Lifecycle Stepper --}}
            <div class="border border-slate-200 rounded-2xl p-4 bg-white shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        5-STEP MEDICAL RESPONSE LIFECYCLE
                    </span>
                    <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full {{ $isResponding ? 'bg-blue-100 text-blue-700 border border-blue-200' : ($isAck ? 'bg-amber-100 text-amber-700 border border-amber-200' : 'bg-red-100 text-red-700 border border-red-200 animate-pulse') }}">
                        CURRENT: {{ strtoupper($incident?->status ?? 'PENDING') }}
                    </span>
                </div>

                <div class="grid grid-cols-5 gap-1.5 text-center text-[10px]">
                    <div class="flex flex-col items-center">
                        <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center font-black text-[11px] shadow-xs mb-1">✓</div>
                        <span class="font-extrabold text-slate-800 leading-tight">1. Triggered</span>
                        <span class="text-[9px] text-slate-500">Alarm active</span>
                    </div>
                    <div class="flex flex-col items-center">
                        @if(!$isPending)
                            <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center font-black text-[11px] shadow-xs mb-1">✓</div>
                            <span class="font-extrabold text-slate-800 leading-tight">2. Acknowledge</span>
                            <span class="text-[9px] text-emerald-600 font-bold">Confirmed</span>
                        @else
                            <div class="w-6 h-6 rounded-full bg-red-600 text-white flex items-center justify-center font-black text-[11px] shadow-xs mb-1 animate-ping">2</div>
                            <span class="font-extrabold text-red-600 leading-tight">2. Acknowledge</span>
                            <span class="text-[9px] text-red-500 font-bold">Action Needed</span>
                        @endif
                    </div>
                    <div class="flex flex-col items-center">
                        @if($isResponding)
                            <div class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-[11px] shadow-xs mb-1 animate-pulse">✓</div>
                            <span class="font-extrabold text-blue-700 leading-tight">3. Dispatch</span>
                            <span class="text-[9px] text-blue-600 font-bold">En Route</span>
                        @elseif($isAck)
                            <div class="w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center font-black text-[11px] shadow-xs mb-1">3</div>
                            <span class="font-extrabold text-amber-700 leading-tight">3. Dispatch</span>
                            <span class="text-[9px] text-amber-600 font-bold">Ready</span>
                        @else
                            <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center font-bold text-[11px] mb-1">3</div>
                            <span class="font-bold text-slate-400 leading-tight">3. Dispatch</span>
                            <span class="text-[9px] text-slate-400">Waiting</span>
                        @endif
                    </div>
                    <div class="flex flex-col items-center">
                        @if($activeEmergency?->arrived_at)
                            <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center font-black text-[11px] shadow-xs mb-1">✓</div>
                            <span class="font-extrabold text-slate-800 leading-tight">4. Triage</span>
                            <span class="text-[9px] text-emerald-600 font-bold">On Scene</span>
                        @elseif($isResponding)
                            <div class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center font-black text-[11px] shadow-xs mb-1 animate-pulse">4</div>
                            <span class="font-extrabold text-blue-700 leading-tight">4. Triage</span>
                            <span class="text-[9px] text-blue-600 font-bold">In Care</span>
                        @else
                            <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center font-bold text-[11px] mb-1">4</div>
                            <span class="font-bold text-slate-400 leading-tight">4. Triage</span>
                            <span class="text-[9px] text-slate-400">Waiting</span>
                        @endif
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center font-bold text-[11px] mb-1">5</div>
                        <span class="font-bold text-slate-400 leading-tight">5. Resolution</span>
                        <span class="text-[9px] text-slate-400">Final Step</span>
                    </div>
                </div>
            </div>

            {{-- Action Buttons Console --}}
            @if($activeEmergency)
            <div class="space-y-2">
                @if($isPending)
                <form method="POST" action="{{ route('clinic.incidents.acknowledge', $activeEmergency) }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 active:scale-95 text-white font-black py-2.5 px-4 rounded-xl shadow-md shadow-red-200 transition-all flex items-center justify-center gap-2 text-xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Acknowledge Alert</span>
                    </button>
                </form>
                @endif

                @if($isPending || $isAck)
                <button type="button" @click="openClinicDispatch = true" class="w-full bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-black py-2.5 px-4 rounded-xl shadow-md shadow-blue-200 transition-all flex items-center justify-center gap-2 text-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Dispatch Medical Team</span>
                </button>
                @else
                <button type="button" @click="openClinicDispatch = true" class="w-full py-2.5 px-3 rounded-xl bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-black text-center flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    Update Dispatch ({{ $activeEmergency->responder_name }})
                </button>
                @endif

                <div class="grid grid-cols-2 gap-2 pt-1">
                    <button type="button" @click="resolveType = 'False Alarm'; openClinicTriage = true" class="w-full bg-white border border-slate-300 hover:bg-slate-100 active:scale-95 text-slate-700 font-bold py-2 px-3 rounded-xl text-xs shadow-xs transition-all cursor-pointer text-center">
                        ⚠️ False Alarm
                    </button>

                    <button id="btn-patient-arrived" type="button" @click="resolveType = 'Resolved'; openClinicTriage = true" class="w-full bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-extrabold py-2 px-3 rounded-xl shadow-xs transition-all flex items-center justify-center gap-1 text-xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Mark Treated & Resolved</span>
                    </button>
                </div>
            </div>
            @endif

            {{-- Protocol Checklist --}}
            <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-200 text-[10px] font-bold text-slate-600">
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    <span>Prepare stretcher</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    <span>Alert doctor/nurses</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    <span>Oxygen & First Aid</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    <span>Coordinate DRRMO</span>
                </div>
            </div>
        </div>
    </div>

    {{-- CLINIC DISPATCH MODAL --}}
    @if($activeEmergency)
    <div x-show="openClinicDispatch" x-cloak class="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" @click.self="openClinicDispatch = false">
        <div class="bg-white border-2 border-blue-500 rounded-3xl p-6 shadow-2xl w-full max-w-lg text-slate-800" @click.stop>
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-black text-lg">
                        🩺
                    </div>
                    <div>
                        <h3 class="font-black text-sm text-slate-900 uppercase">Dispatch Medical Responders</h3>
                        <p class="text-xs text-slate-500">{{ $activeEmergency->emergency_type }} · {{ $activeEmergency->device?->building }}</p>
                    </div>
                </div>
                <button type="button" @click="openClinicDispatch = false" class="text-slate-400 hover:text-slate-600 font-black text-lg cursor-pointer">✕</button>
            </div>

            <form method="POST" action="{{ route('clinic.incidents.dispatch', $activeEmergency) }}" class="space-y-4">
                @csrf
                {{-- Presets --}}
                <div>
                    <label class="block text-[11px] font-black uppercase text-slate-500 mb-1.5">1-Click Medical Presets</label>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <button type="button" @click="unitName = 'Clinic Emergency Team Alpha'; contact = '0917-555-0199'; eta = 2;" class="p-2.5 border border-slate-200 rounded-xl text-left hover:border-blue-500 hover:bg-blue-50 transition-all cursor-pointer">
                            <span class="font-extrabold text-slate-800 block text-xs">Nurse Team Alpha</span>
                            <span class="text-[10px] text-slate-500">ETA 2 mins · Nurse on duty</span>
                        </button>
                        <button type="button" @click="unitName = 'Campus First Aid Volunteers'; contact = '0928-111-2233'; eta = 3;" class="p-2.5 border border-slate-200 rounded-xl text-left hover:border-blue-500 hover:bg-blue-50 transition-all cursor-pointer">
                            <span class="font-extrabold text-slate-800 block text-xs">First Aid Volunteers</span>
                            <span class="text-[10px] text-slate-500">ETA 3 mins · Red Cross Youth</span>
                        </button>
                        <button type="button" @click="unitName = 'Ambulance Transport Team'; contact = '0939-999-4455'; eta = 5;" class="p-2.5 border border-slate-200 rounded-xl text-left hover:border-blue-500 hover:bg-blue-50 transition-all cursor-pointer">
                            <span class="font-extrabold text-slate-800 block text-xs">Ambulance Team</span>
                            <span class="text-[10px] text-slate-500">ETA 5 mins · Mobile Stretcher</span>
                        </button>
                        <button type="button" @click="unitName = 'Attending School Physician'; contact = '0919-888-7766'; eta = 4;" class="p-2.5 border border-slate-200 rounded-xl text-left hover:border-blue-500 hover:bg-blue-50 transition-all cursor-pointer">
                            <span class="font-extrabold text-slate-800 block text-xs">School Physician</span>
                            <span class="text-[10px] text-slate-500">ETA 4 mins · Doctor Response</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Medical Unit / Nurse</label>
                        <input type="text" name="responder_name" x-model="unitName" required class="w-full text-xs font-bold px-3 py-2 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none" placeholder="e.g. Nurse Santos">
                    </div>
                    <div>
                        <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Mobile / Radio</label>
                        <input type="text" name="responder_contact" x-model="contact" class="w-full text-xs font-bold px-3 py-2 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none" placeholder="e.g. 0917-555-0199">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Estimated Arrival (ETA in Minutes)</label>
                    <input type="number" name="eta_minutes" x-model="eta" min="1" max="120" required class="w-full text-xs font-bold px-3 py-2 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none" placeholder="e.g. 3">
                </div>

                <div>
                    <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Medical Instructions / Equipments Required</label>
                    <textarea name="dispatch_notes" rows="2" class="w-full text-xs font-semibold px-3 py-2 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none" placeholder="e.g. Bring trauma bag, cervical collar, portable oxygen unit"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="openClinicDispatch = false" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-xs rounded-xl cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-xl shadow-md transition-all cursor-pointer">Confirm & Dispatch</button>
                </div>
            </form>
        </div>
    </div>

    {{-- CLINIC CASUALTY TRIAGE & RESOLVE MODAL --}}
    <div x-show="openClinicTriage" x-cloak class="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" @click.self="openClinicTriage = false">
        <div class="bg-white border-2 border-emerald-500 rounded-3xl p-6 shadow-2xl w-full max-w-lg text-slate-800 max-h-[90vh] overflow-y-auto" @click.stop>
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-black text-lg">
                        🏥
                    </div>
                    <div>
                        <h3 class="font-black text-sm text-slate-900 uppercase">Casualty Triage & Case Resolution</h3>
                        <p class="text-xs text-slate-500">{{ $activeEmergency->emergency_type }} · {{ $activeEmergency->device?->building }}</p>
                    </div>
                </div>
                <button type="button" @click="openClinicTriage = false" class="text-slate-400 hover:text-slate-600 font-black text-lg cursor-pointer">✕</button>
            </div>

            <form method="POST" action="{{ route('clinic.incidents.resolve', $activeEmergency) }}" class="space-y-4">
                @csrf
                {{-- Classification Radio Buttons --}}
                <div>
                    <label class="block text-[11px] font-black uppercase text-slate-500 mb-1.5">Resolution Classification</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label :class="resolveType === 'Resolved' ? 'border-emerald-600 bg-emerald-50 text-emerald-900 shadow-xs' : 'border-slate-200 bg-white text-slate-700'" class="p-2.5 border-2 rounded-xl text-center cursor-pointer font-extrabold text-xs transition-all flex flex-col items-center gap-1">
                            <input type="radio" name="resolution_type" value="Resolved" x-model="resolveType" class="sr-only">
                            <span>✅ Treated</span>
                            <span class="text-[9px] font-normal text-slate-500">Patient Attended</span>
                        </label>
                        <label :class="resolveType === 'False Alarm' ? 'border-amber-600 bg-amber-50 text-amber-900 shadow-xs' : 'border-slate-200 bg-white text-slate-700'" class="p-2.5 border-2 rounded-xl text-center cursor-pointer font-extrabold text-xs transition-all flex flex-col items-center gap-1">
                            <input type="radio" name="resolution_type" value="False Alarm" x-model="resolveType" class="sr-only">
                            <span>⚠️ False Alarm</span>
                            <span class="text-[9px] font-normal text-slate-500">Accidental Trigger</span>
                        </label>
                        <label :class="resolveType === 'Drill' ? 'border-purple-600 bg-purple-50 text-purple-900 shadow-xs' : 'border-slate-200 bg-white text-slate-700'" class="p-2.5 border-2 rounded-xl text-center cursor-pointer font-extrabold text-xs transition-all flex flex-col items-center gap-1">
                            <input type="radio" name="resolution_type" value="Drill" x-model="resolveType" class="sr-only">
                            <span>🛡️ Medical Drill</span>
                            <span class="text-[9px] font-normal text-slate-500">First Aid Practice</span>
                        </label>
                    </div>
                </div>

                {{-- False Alarm Reason --}}
                <div x-show="resolveType === 'False Alarm'" class="space-y-2">
                    <label class="block text-[11px] font-black uppercase text-amber-800">False Alarm Reason</label>
                    <select name="false_alarm_reason" class="w-full text-xs font-bold px-3 py-2 border border-amber-300 rounded-xl bg-amber-50/50 focus:outline-none">
                        <option value="Accidental button push by student / staff">Accidental button push by student / staff</option>
                        <option value="Sensor / hardware glitch">Sensor / hardware glitch</option>
                        <option value="Curiosity / unintended test">Curiosity / unintended test</option>
                        <option value="Prank / unverified report">Prank / unverified report</option>
                        <option value="Routine inspection / clinic test">Routine inspection / clinic test</option>
                    </select>
                </div>

                {{-- Drill Type --}}
                <div x-show="resolveType === 'Drill'" class="space-y-2">
                    <label class="block text-[11px] font-black uppercase text-purple-800">Medical Simulation Type</label>
                    <select name="remarks" class="w-full text-xs font-bold px-3 py-2 border border-purple-300 rounded-xl bg-purple-50/50 focus:outline-none">
                        <option value="Campus Mass Casualty Incident Simulation">Campus Mass Casualty Incident Simulation</option>
                        <option value="Basic Life Support & First Aid Drill">Basic Life Support & First Aid Drill</option>
                        <option value="Evacuation Triage Station Exercise">Evacuation Triage Station Exercise</option>
                        <option value="DRRMO-Clinic Joint Response Drill">DRRMO-Clinic Joint Response Drill</option>
                    </select>
                </div>

                {{-- Patient & Triage Details (for Real Incidents) --}}
                <div x-show="resolveType === 'Resolved'" class="space-y-3.5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Patient Full Name</label>
                            <input type="text" name="patient_name" x-model="patientName" class="w-full text-xs font-bold px-3 py-2 border border-slate-300 rounded-xl focus:border-emerald-500 focus:outline-none" placeholder="e.g. Maria Santos">
                        </div>
                        <div>
                            <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Student / Employee ID</label>
                            <input type="text" name="patient_id_number" x-model="patientId" class="w-full text-xs font-bold px-3 py-2 border border-slate-300 rounded-xl focus:border-emerald-500 focus:outline-none" placeholder="e.g. 2024-12345">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-black uppercase text-slate-700 mb-1.5">Triage Severity Level</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                            <label :class="triageLevel === 'Red' ? 'border-red-600 bg-red-50 text-red-900 font-black' : 'border-slate-200 text-slate-700'" class="p-2 border-2 rounded-xl text-center cursor-pointer transition-all flex flex-col items-center">
                                <input type="radio" name="triage_level" value="Red" x-model="triageLevel" class="sr-only">
                                <span class="text-sm">🔴 Red</span>
                                <span class="text-[9px] text-red-600 font-extrabold uppercase">Immediate</span>
                            </label>
                            <label :class="triageLevel === 'Yellow' ? 'border-amber-500 bg-amber-50 text-amber-900 font-black' : 'border-slate-200 text-slate-700'" class="p-2 border-2 rounded-xl text-center cursor-pointer transition-all flex flex-col items-center">
                                <input type="radio" name="triage_level" value="Yellow" x-model="triageLevel" class="sr-only">
                                <span class="text-sm">🟡 Yellow</span>
                                <span class="text-[9px] text-amber-600 font-extrabold uppercase">Delayed</span>
                            </label>
                            <label :class="triageLevel === 'Green' ? 'border-emerald-600 bg-emerald-50 text-emerald-900 font-black' : 'border-slate-200 text-slate-700'" class="p-2 border-2 rounded-xl text-center cursor-pointer transition-all flex flex-col items-center">
                                <input type="radio" name="triage_level" value="Green" x-model="triageLevel" class="sr-only">
                                <span class="text-sm">🟢 Green</span>
                                <span class="text-[9px] text-emerald-600 font-extrabold uppercase">Minor</span>
                            </label>
                            <label :class="triageLevel === 'Black' ? 'border-slate-800 bg-slate-100 text-slate-900 font-black' : 'border-slate-200 text-slate-700'" class="p-2 border-2 rounded-xl text-center cursor-pointer transition-all flex flex-col items-center">
                                <input type="radio" name="triage_level" value="Black" x-model="triageLevel" class="sr-only">
                                <span class="text-sm">⚫ Black</span>
                                <span class="text-[9px] text-slate-600 font-extrabold uppercase">Expectant</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Treatment & Clinical Care Given</label>
                        <textarea name="treatment_summary" rows="2" class="w-full text-xs font-semibold px-3 py-2 border border-slate-300 rounded-xl focus:border-emerald-500 focus:outline-none" placeholder="e.g. Wound cleaned and dressed, vital signs taken (BP 120/80), rest administered with cold compress."></textarea>
                    </div>

                    <div>
                        <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Patient Disposition</label>
                        <select name="disposition" x-model="disposition" class="w-full text-xs font-bold px-3 py-2 border border-slate-300 rounded-xl focus:border-emerald-500 focus:outline-none">
                            <option value="Treated and released to class">Treated and released to class</option>
                            <option value="Admitted to Clinic observation bed">Admitted to Clinic observation bed</option>
                            <option value="Referred / Transferred to external hospital via EMS">Referred / Transferred to external hospital via EMS</option>
                            <option value="Released into care of parent / guardian">Released into care of parent / guardian</option>
                            <option value="Sent home for rest">Sent home for rest</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="openClinicTriage = false" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-xs rounded-xl cursor-pointer">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md transition-all cursor-pointer">Save & Mark Resolved</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@else
{{-- ===== NO EMERGENCY STANDBY BANNER ===== --}}
<div id="no-emergency-banner" class="bg-gradient-to-r from-emerald-50/70 via-white to-slate-50 border border-emerald-300/80 rounded-2xl mb-6 p-6 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4 text-black">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-emerald-100 border border-emerald-300 flex items-center justify-center shrink-0 shadow-xs">
            <svg class="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="flex items-center gap-2 mb-0.5">
                <h3 class="text-base font-black text-slate-900">System Normal — No Active Emergency</h3>
                <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-800 text-[10px] font-black px-2 py-0.5 rounded-full border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>Operational
                </span>
            </div>
            <p class="text-xs font-bold text-slate-600">Clinic emergency response team is on standby. Device availability is monitored by DRRMO.</p>
            <div class="flex flex-wrap items-center gap-4 mt-2 text-[11px] font-bold text-slate-500">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Campus Mesh Online</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-500"></span>DRRMO Sync Active</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-slate-400"></span>Medical Responders Ready</span>
            </div>
        </div>
    </div>
    <span class="inline-flex items-center gap-1.5 bg-green-100 text-brand-green text-xs font-black px-3.5 py-2 rounded-xl border border-green-300 shadow-xs shrink-0">
        <span class="w-2.5 h-2.5 rounded-full bg-brand-green animate-pulse"></span>STANDBY READY
    </span>
</div>
@endif

{{-- ===== BOTTOM SECTION: TABLES ===== --}}
<div class="flex flex-col gap-6 text-black">

    {{-- Active Critical Alerts Table --}}
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        <div class="px-6 py-4 flex items-center justify-between border-b border-slate-200 bg-slate-50/80">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-brand-red animate-pulse"></span>
                <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider">Active Medical & Critical Alerts</h2>
            </div>
            <div class="flex items-center gap-3">
                @if($criticalIncidents->where('status', 'Pending')->count() > 1)
                <form method="POST" action="{{ route('clinic.alerts.acknowledge-all') }}">
                    @csrf
                    <button type="submit" class="px-2.5 py-1 bg-red-600 hover:bg-red-700 active:scale-95 text-white font-extrabold rounded-lg text-[10px] transition-all shadow-xs cursor-pointer flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Acknowledge All ({{ $criticalIncidents->where('status', 'Pending')->count() }})</span>
                    </button>
                </form>
                @endif
                <a href="{{ route('clinic.alerts') }}" class="text-[11px] text-brand-blue hover:underline font-extrabold">View All →</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-[11px] text-slate-700 font-black uppercase tracking-wider">
                        <th class="px-5 py-3.5">#</th>
                        <th class="px-5 py-3.5">Time</th>
                        <th class="px-5 py-3.5">Location</th>
                        <th class="px-5 py-3.5">Device</th>
                        <th class="px-5 py-3.5">Type</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="active-alerts-tbody" class="text-xs divide-y divide-slate-100">
                    @forelse($criticalIncidents as $index => $incident)
                    <tr class="bg-red-50/50 hover:bg-red-100/50 transition-colors" id="incident-row-{{ $incident->id }}">
                        <td class="px-5 py-4 text-slate-900 font-bold row-no">{{ $index + 1 }}</td>
                        <td class="px-5 py-4 font-black text-slate-900">{{ $incident->created_at->format('h:i A') }}</td>
                        <td class="px-5 py-4 font-bold text-slate-900">{{ $incident->device?->building ?? 'Location not recorded' }}</td>
                        <td class="px-5 py-4 text-slate-700 font-mono font-bold text-[11px]">{{ $incident->device?->device_code ?? 'Not recorded' }}</td>
                        <td class="px-5 py-4"><span class="inline-flex items-center bg-red-600 text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-xs">{{ $incident->emergency_type }}</span></td>
                        <td class="px-5 py-4"><span class="inline-flex items-center gap-1.5 text-brand-red font-black"><span class="w-2 h-2 rounded-full bg-brand-red animate-pulse"></span>{{ ucfirst($incident->status) }}</span></td>
                        <td class="px-5 py-4 text-center">
                            <form method="POST" action="{{ $incident->status === 'Pending' ? route('clinic.incidents.acknowledge', $incident) : route('clinic.incidents.resolve', $incident) }}">
                                @csrf
                                <button type="submit" class="bg-brand-blue hover:bg-blue-800 text-white font-bold px-3 py-1.5 rounded-lg text-xs transition-colors shadow-xs cursor-pointer">
                                    {{ $incident->status === 'Pending' ? 'Acknowledge' : 'Resolve' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr id="no-active-alerts-row">
                        <td colspan="7" class="px-5 py-8 text-center text-slate-500 font-semibold">
                            No active medical or critical alerts right now.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Alert History Table --}}
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        <div class="px-6 py-4 flex items-center justify-between border-b border-slate-200 bg-slate-50/80">
            <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider">Alert History (Today)</h2>
            <a href="{{ route('clinic.logs') }}" class="text-[11px] text-brand-blue hover:underline font-extrabold">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-[11px] text-slate-700 font-black uppercase tracking-wider">
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
</div>

@endsection
