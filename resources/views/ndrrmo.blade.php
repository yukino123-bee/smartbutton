@extends('layouts.ndrrmo')

@section('content')
            
            <!-- Top Stats Row -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Stat Card 1: Active Alerts -->
                <div class="bg-white border border-slate-300 rounded-2xl p-5 flex items-center shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="w-12 h-12 rounded-xl bg-red-100 border border-red-300 flex items-center justify-center mr-4 shrink-0">
                        <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <div>
                        <div class="text-[11px] font-black text-black uppercase tracking-wider mb-1">ACTIVE ALERTS</div>
                        <div id="ndrrmo-stat-active" class="text-3xl font-black text-brand-red leading-none mb-1 tabular-nums">{{ $activeIncidents->count() }}</div>
                        <div class="text-[11px] font-bold text-slate-700">Require immediate attention</div>
                    </div>
                </div>

                <!-- Stat Card 2: Total Incidents -->
                <div class="bg-white border border-slate-300 rounded-2xl p-5 flex items-center shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 border border-amber-300 flex items-center justify-center mr-4 shrink-0">
                        <svg class="w-6 h-6 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <div>
                        <div class="text-[11px] font-black text-black uppercase tracking-wider mb-1">TOTAL INCIDENTS</div>
                        <div id="ndrrmo-stat-total" class="text-3xl font-black text-black leading-none mb-1 tabular-nums">{{ $totalIncidents }}</div>
                        <div class="text-[11px] font-bold text-slate-700">This month</div>
                    </div>
                </div>

                <!-- Stat Card 3: Resolved Incidents -->
                <div class="bg-white border border-slate-300 rounded-2xl p-5 flex items-center shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="w-12 h-12 rounded-xl bg-green-100 border border-green-300 flex items-center justify-center mr-4 shrink-0">
                        <svg class="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <div>
                        <div class="text-[11px] font-black text-black uppercase tracking-wider mb-1">RESOLVED INCIDENTS</div>
                        <div id="ndrrmo-stat-resolved" class="text-3xl font-black text-brand-green leading-none mb-1 tabular-nums">{{ $resolvedIncidents }}</div>
                        <div class="text-[11px] font-bold text-slate-700">This month</div>
                    </div>
                </div>

                <!-- Stat Card 4: Devices Online -->
                <div class="bg-white border border-slate-300 rounded-2xl p-5 flex items-center shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 border border-blue-300 flex items-center justify-center mr-4 shrink-0">
                        <svg class="w-6 h-6 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                    </div>
                    <div>
                        <div class="text-[11px] font-black text-black uppercase tracking-wider mb-1">DEVICES ONLINE</div>
                        <div id="ndrrmo-stat-devices" class="text-3xl font-black text-black leading-none mb-1 tabular-nums">{{ $onlineDevicesCount ?? 0 }} / {{ $devicesCount ?? 0 }}</div>
                        <div id="ndrrmo-stat-devices-subtitle" class="text-[11px] font-bold text-slate-700">{{ ($onlineDevicesCount ?? 0) > 0 ? 'Devices operational' : 'No devices online' }}</div>
                    </div>
                </div>
            </div>

            @if($activeIncidents->count() > 0)
            <!-- Active Emergency Command Console with Step-by-Step Response Lifecycle -->
            <div class="mb-6 space-y-4">
                <div class="bg-red-600 border-2 border-red-500 rounded-3xl px-6 py-4 flex flex-wrap items-center justify-between gap-3 shadow-lg text-white">
                    <div class="flex items-center gap-3">
                        <span class="w-3.5 h-3.5 rounded-full bg-white animate-ping"></span>
                        <div>
                            <span class="font-black text-sm uppercase tracking-wider block">
                                🚨 ACTIVE EMERGENCY COMMAND CONSOLE ({{ $activeIncidents->count() }} ACTIVE {{ Str::plural('INCIDENT', $activeIncidents->count()) }})
                            </span>
                            <span class="text-xs text-red-100 font-medium">
                                Immediate disaster risk reduction & incident response in progress. Inter-agency coordination with Clinic & Campus Security active.
                            </span>
                        </div>
                    </div>
                    @if($activeIncidents->where('status', 'Pending')->count() > 1)
                    <form method="POST" action="{{ route('ndrrmo.alerts.acknowledge-all') }}" class="shrink-0">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-white hover:bg-red-50 text-red-600 active:scale-95 rounded-xl font-black text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Acknowledge All ({{ $activeIncidents->where('status', 'Pending')->count() }} Alerts)</span>
                        </button>
                    </form>
                    @endif
                </div>

                <div class="grid grid-cols-1 {{ $activeIncidents->count() > 1 ? 'xl:grid-cols-2' : '' }} gap-4">
                    @foreach($activeIncidents as $index => $incident)
                        @php
                            $clinicNotified = $incident->notifications->where('recipient', 'Clinic')->isNotEmpty();
                            $clinicAck = $incident->notifications->where('recipient', 'Clinic')->where('status', 'Acknowledged')->isNotEmpty()
                                || $incident->notifications->where('status', 'Acknowledged by Clinic')->isNotEmpty();
                            $clinicDispatched = $incident->notifications->where('status', 'Clinic Medical Team Dispatched')->isNotEmpty();
                            
                            $isPending = $incident->status === 'Pending';
                            $isAck = $incident->status === 'Acknowledged';
                            $isResponding = $incident->status === 'Responding';

                            $badgeColor = 'bg-red-600';
                            $borderColor = 'border-red-500';
                            if (str_contains($incident->emergency_type, 'Medical')) {
                                $badgeColor = 'bg-orange-500';
                                $borderColor = 'border-orange-500';
                            } elseif (str_contains($incident->emergency_type, 'Public Safety') || str_contains($incident->emergency_type, 'Facility')) {
                                $badgeColor = 'bg-amber-500';
                                $borderColor = 'border-amber-500';
                            }
                        @endphp
                        <div id="incident-card-{{ $incident->id }}" 
                             x-data="{ 
                                 openDispatch: false, 
                                 openResolve: false, 
                                 unitName: '{{ addslashes($incident->responder_name ?? 'DRRMO Responders Unit 1') }}', 
                                 contact: '{{ addslashes($incident->responder_contact ?? '0917-889-1001') }}', 
                                 eta: {{ $incident->eta_minutes ?? 3 }}, 
                                 resolveType: 'Resolved' 
                             }" 
                             class="bg-white border-2 {{ $borderColor }} rounded-3xl overflow-hidden shadow-md flex flex-col justify-between transition-all duration-300">
                            <div>
                                {{-- Card Header --}}
                                <div class="{{ $badgeColor }} px-5 py-3 flex items-center justify-between text-white">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-white animate-ping"></span>
                                        <span class="font-black text-xs uppercase tracking-wider">
                                            Incident #{{ $incident->id }}: {{ $incident->emergency_type }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] font-extrabold bg-black/25 px-2.5 py-0.5 rounded-full">
                                            {{ $incident->reported_at->format('h:i A · M d') }}
                                        </span>
                                        <span class="text-[11px] font-mono font-bold bg-white/20 px-2 py-0.5 rounded-full">
                                            {{ $incident->reported_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Card Body: Location & Details --}}
                                <div class="p-5">
                                    <div class="flex items-start gap-4 mb-3">
                                        <div class="w-12 h-12 rounded-2xl bg-red-100 border border-red-200 flex items-center justify-center shrink-0 text-red-600 text-xl font-black shadow-xs">
                                            🚨
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs font-bold text-slate-700">
                                                <div>
                                                    <span class="text-slate-400 font-extrabold uppercase text-[10px] block">LOCATION</span>
                                                    <span class="font-black text-slate-900 text-sm">{{ $incident->device?->building ?? 'Location not recorded' }}</span>
                                                    @if($incident->device?->floor || $incident->device?->room)
                                                        <span class="text-slate-500 text-xs block font-semibold">{{ $incident->device->floor }} · {{ $incident->device->room }}</span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <span class="text-slate-400 font-extrabold uppercase text-[10px] block">DEVICE CODE</span>
                                                    <span class="font-mono font-black text-slate-900 text-sm">{{ $incident->device?->device_code ?? 'Not recorded' }}</span>
                                                    @if($incident->device?->latitude && $incident->device?->longitude)
                                                        <span class="text-slate-500 text-[10px] block font-mono">GPS: {{ number_format($incident->device->latitude, 4) }}, {{ number_format($incident->device->longitude, 4) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- DISPATCHED FIRST RESPONDER STRIP --}}
                                    @if($isResponding || $incident->responder_name)
                                    <div class="mb-3 bg-blue-50/90 border border-blue-200 rounded-2xl p-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-black text-base shrink-0 shadow-xs">
                                                🚑
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[11px] font-black text-blue-900 uppercase">RESPONDER:</span>
                                                    <span class="text-xs font-extrabold text-blue-700">{{ $incident->responder_name ?: 'DRRMO Responders Team' }}</span>
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
                                                    <span>📞 {{ $incident->responder_contact ?: 'Radio Ch. 1' }}</span>
                                                    @if($incident->dispatch_notes)
                                                        <span>📋 {{ $incident->dispatch_notes }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @if(!$incident->arrived_at)
                                        <form method="POST" action="{{ route('ndrrmo.incidents.on-scene', $incident) }}" class="shrink-0">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-extrabold text-xs rounded-xl shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span>Mark On Scene</span>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                    @endif

                                    {{-- STEP-BY-STEP ACTION LIFECYCLE TRACKER --}}
                                    <div class="border border-slate-200 rounded-2xl p-3.5 bg-slate-50/70 mb-3">
                                        <div class="flex items-center justify-between mb-2.5">
                                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                5-STEP RESPONSE LIFECYCLE
                                            </span>
                                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full {{ $isResponding ? 'bg-blue-100 text-blue-700 border border-blue-200' : ($isAck ? 'bg-amber-100 text-amber-700 border border-amber-200' : 'bg-red-100 text-red-700 border border-red-200 animate-pulse') }}">
                                                Current: {{ strtoupper($incident->status) }}
                                            </span>
                                        </div>

                                        {{-- 5-Step Visual Stepper Bar --}}
                                        <div class="grid grid-cols-5 gap-1 text-center text-[10px]">
                                            {{-- Step 1: Triggered --}}
                                            <div class="flex flex-col items-center">
                                                <div class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center font-black text-[10px] shadow-xs mb-1">✓</div>
                                                <span class="font-extrabold text-slate-800 leading-tight">1. Triggered</span>
                                            </div>

                                            {{-- Step 2: Acknowledged --}}
                                            <div class="flex flex-col items-center">
                                                @if(!$isPending)
                                                    <div class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center font-black text-[10px] shadow-xs mb-1">✓</div>
                                                    <span class="font-extrabold text-slate-800 leading-tight">2. Acknowledge</span>
                                                @else
                                                    <div class="w-5 h-5 rounded-full bg-red-600 text-white flex items-center justify-center font-black text-[10px] shadow-xs mb-1 animate-ping">2</div>
                                                    <span class="font-extrabold text-red-600 leading-tight">2. Acknowledge</span>
                                                @endif
                                            </div>

                                            {{-- Step 3: Dispatched --}}
                                            <div class="flex flex-col items-center">
                                                @if($isResponding)
                                                    <div class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-[10px] shadow-xs mb-1 animate-pulse">✓</div>
                                                    <span class="font-extrabold text-blue-700 leading-tight">3. Dispatch</span>
                                                @elseif($isAck)
                                                    <div class="w-5 h-5 rounded-full bg-amber-500 text-white flex items-center justify-center font-black text-[10px] shadow-xs mb-1">3</div>
                                                    <span class="font-extrabold text-amber-700 leading-tight">3. Dispatch</span>
                                                @else
                                                    <div class="w-5 h-5 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center font-bold text-[10px] mb-1">3</div>
                                                    <span class="font-bold text-slate-400 leading-tight">3. Dispatch</span>
                                                @endif
                                            </div>

                                            {{-- Step 4: Inter-Agency / On Scene --}}
                                            <div class="flex flex-col items-center">
                                                @if($incident->arrived_at)
                                                    <div class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center font-black text-[10px] shadow-xs mb-1">✓</div>
                                                    <span class="font-extrabold text-slate-800 leading-tight">4. Inter-Agency</span>
                                                @elseif($isResponding)
                                                    <div class="w-5 h-5 rounded-full bg-blue-400 text-white flex items-center justify-center font-black text-[10px] mb-1 animate-pulse">4</div>
                                                    <span class="font-extrabold text-blue-700 leading-tight">4. Inter-Agency</span>
                                                @else
                                                    <div class="w-5 h-5 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center font-bold text-[10px] mb-1">4</div>
                                                    <span class="font-bold text-slate-400 leading-tight">4. Inter-Agency</span>
                                                @endif
                                            </div>

                                            {{-- Step 5: Resolved --}}
                                            <div class="flex flex-col items-center">
                                                <div class="w-5 h-5 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center font-bold text-[10px] mb-1">5</div>
                                                <span class="font-bold text-slate-400 leading-tight">5. Resolution</span>
                                            </div>
                                        </div>

                                        {{-- Connected Agencies Status Strip --}}
                                        <div class="mt-2.5 pt-2 border-t border-slate-200 flex flex-wrap items-center justify-between gap-2 text-[11px]">
                                            <div class="flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full {{ $clinicNotified ? ($clinicDispatched ? 'bg-emerald-500' : 'bg-orange-500 animate-pulse') : 'bg-slate-300' }}"></span>
                                                <span class="font-bold text-slate-700">Clinic:</span>
                                                @if($clinicDispatched)
                                                    <span class="text-emerald-700 font-extrabold">Medical Dispatched</span>
                                                @elseif($clinicAck)
                                                    <span class="text-emerald-700 font-extrabold">Acknowledged</span>
                                                @elseif($clinicNotified)
                                                    <span class="text-orange-700 font-extrabold">Notified</span>
                                                @else
                                                    <span class="text-slate-400">Not Linked</span>
                                                @endif
                                            </div>

                                            <div class="flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full {{ $isResponding ? 'bg-blue-600 animate-pulse' : 'bg-slate-300' }}"></span>
                                                <span class="font-bold text-slate-700">DRRMO:</span>
                                                <span class="font-extrabold {{ $isResponding ? 'text-blue-800' : 'text-slate-700' }}">
                                                    {{ $incident->arrived_at ? 'On Scene Operating' : ($isResponding ? 'En Route to Location' : ($isAck ? 'Acknowledged' : 'Pending')) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- TIMESTAMPED RESPONSE AUDIT TRAIL --}}
                                    <div class="border border-slate-200 rounded-2xl p-3 bg-white mb-2">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 flex items-center gap-1">
                                                ⏱️ RESPONSE AUDIT MILESTONES
                                            </span>
                                            @if($incident->ack_duration)
                                                <span class="text-[9px] font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                                    Ack Time: {{ $incident->ack_duration }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5 text-[10px]">
                                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-1.5">
                                                <span class="text-slate-400 font-bold block text-[9px] uppercase">1. Reported</span>
                                                <span class="font-black text-slate-900">{{ $incident->reported_at->format('h:i:s A') }}</span>
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
                                </div>
                            </div>

                            {{-- Action Controls Footer --}}
                            <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    {{-- Action 1: Acknowledge (if Pending) --}}
                                    @if($isPending)
                                    <form method="POST" action="{{ route('ndrrmo.incidents.acknowledge', $incident) }}">
                                        @csrf
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 active:scale-95 text-white font-extrabold py-2 px-3.5 rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            <span>Acknowledge Alert</span>
                                        </button>
                                    </form>
                                    @endif

                                    {{-- Action 2: Open Dispatch Modal --}}
                                    @if($isPending || $isAck)
                                    <button type="button" id="open-dispatch-modal-{{ $incident->id }}" @click="openDispatch = true" class="bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-extrabold py-2 px-3.5 rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        <span>Dispatch Responders</span>
                                    </button>
                                    @else
                                    <button type="button" @click="openDispatch = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-black transition-all cursor-pointer">
                                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                                        Update Dispatch ({{ $incident->responder_name }})
                                    </button>
                                    @endif

                                    {{-- Action 3: Notify Clinic (if not notified) --}}
                                    @if(!$clinicNotified)
                                    <form method="POST" action="{{ route('ndrrmo.incidents.notify-clinic', $incident) }}">
                                        @csrf
                                        <button type="submit" class="bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-extrabold py-2 px-3.5 rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Notify Clinic</span>
                                        </button>
                                    </form>
                                    @endif
                                </div>

                                {{-- Action 4: Resolve / False Alarm Modal Button --}}
                                <div class="flex items-center gap-2">
                                    <button type="button" id="open-false-alarm-modal-{{ $incident->id }}" @click="resolveType = 'False Alarm'; openResolve = true" class="bg-white border border-slate-300 hover:bg-slate-100 active:scale-95 text-slate-700 font-bold py-2 px-3 rounded-xl text-xs shadow-xs transition-all cursor-pointer">
                                        ⚠️ False Alarm
                                    </button>

                                    <button type="button" @click="resolveType = 'Resolved'; openResolve = true" class="bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-extrabold py-2 px-4 rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Mark Incident Resolved</span>
                                    </button>
                                </div>
                            </div>

                            {{-- MODAL 1: DISPATCH ASSIGNMENT MODAL --}}
                            <div x-show="openDispatch" x-cloak class="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" @click.self="openDispatch = false">
                                <div class="bg-white border-2 border-blue-500 rounded-3xl p-6 shadow-2xl w-full max-w-lg text-slate-800" @click.stop>
                                    <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-black text-lg">
                                                🚑
                                            </div>
                                            <div>
                                                <h3 class="font-black text-sm text-slate-900 uppercase">Dispatch First Responders</h3>
                                                <p class="text-xs text-slate-500">Incident #{{ $incident->id }} · {{ $incident->device?->building }}</p>
                                            </div>
                                        </div>
                                        <button type="button" @click="openDispatch = false" class="text-slate-400 hover:text-slate-600 font-black text-lg cursor-pointer">✕</button>
                                    </div>

                                    <form method="POST" action="{{ route('ndrrmo.incidents.dispatch', $incident) }}" class="space-y-4">
                                        @csrf
                                        {{-- Presets --}}
                                        <div>
                                            <label class="block text-[11px] font-black uppercase text-slate-500 mb-1.5">1-Click Quick Presets</label>
                                            <div class="grid grid-cols-2 gap-2 text-xs">
                                                <button type="button" @click="unitName = 'DRRMO Responders Unit 1'; contact = '0917-889-1001'; eta = 3;" class="p-2.5 border border-slate-200 rounded-xl text-left hover:border-blue-500 hover:bg-blue-50 transition-all cursor-pointer">
                                                    <span class="font-extrabold text-slate-800 block text-xs">DRRMO Unit 1</span>
                                                    <span class="text-[10px] text-slate-500">ETA 3 mins · Radio Ch. 1</span>
                                                </button>
                                                <button type="button" @click="unitName = 'Campus Security Patrol Alpha'; contact = '0918-223-4002'; eta = 2;" class="p-2.5 border border-slate-200 rounded-xl text-left hover:border-blue-500 hover:bg-blue-50 transition-all cursor-pointer">
                                                    <span class="font-extrabold text-slate-800 block text-xs">Security Patrol</span>
                                                    <span class="text-[10px] text-slate-500">ETA 2 mins · Security</span>
                                                </button>
                                                <button type="button" @click="unitName = 'Emergency Medical Team'; contact = '0920-555-8888'; eta = 4;" class="p-2.5 border border-slate-200 rounded-xl text-left hover:border-blue-500 hover:bg-blue-50 transition-all cursor-pointer">
                                                    <span class="font-extrabold text-slate-800 block text-xs">Medical Team</span>
                                                    <span class="text-[10px] text-slate-500">ETA 4 mins · Clinic</span>
                                                </button>
                                                <button type="button" @click="unitName = 'JHCSC Fire & Rescue Unit'; contact = '0999-123-9999'; eta = 5;" class="p-2.5 border border-slate-200 rounded-xl text-left hover:border-blue-500 hover:bg-blue-50 transition-all cursor-pointer">
                                                    <span class="font-extrabold text-slate-800 block text-xs">Fire & Rescue</span>
                                                    <span class="text-[10px] text-slate-500">ETA 5 mins · Fire/Hazard</span>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Responder / Unit Name</label>
                                                <input type="text" name="responder_name" x-model="unitName" required class="w-full text-xs font-bold px-3 py-2 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none" placeholder="e.g. Officer Santos - DRRMO Team">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Contact / Radio</label>
                                                <input type="text" name="responder_contact" x-model="contact" class="w-full text-xs font-bold px-3 py-2 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none" placeholder="e.g. 0917-123-4567 / Radio Ch. 1">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Estimated Arrival (ETA in Minutes)</label>
                                            <input type="number" name="eta_minutes" x-model="eta" min="1" max="120" required class="w-full text-xs font-bold px-3 py-2 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none" placeholder="e.g. 3">
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Dispatch Instructions / Notes</label>
                                            <textarea name="dispatch_notes" rows="2" class="w-full text-xs font-semibold px-3 py-2 border border-slate-300 rounded-xl focus:border-blue-500 focus:outline-none" placeholder="e.g. Bring spine board, clear east entrance corridor"></textarea>
                                        </div>

                                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                                            <button type="button" @click="openDispatch = false" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-xs rounded-xl cursor-pointer">Cancel</button>
                                            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-xl shadow-md transition-all cursor-pointer">Confirm & Dispatch</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- MODAL 2: RESOLUTION & FALSE ALARM MODAL --}}
                            <div x-show="openResolve" x-cloak class="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" @click.self="openResolve = false">
                                <div class="bg-white border-2 border-emerald-500 rounded-3xl p-6 shadow-2xl w-full max-w-lg text-slate-800" @click.stop>
                                    <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-black text-lg">
                                                🏁
                                            </div>
                                            <div>
                                                <h3 class="font-black text-sm text-slate-900 uppercase">Incident Resolution & Classification</h3>
                                                <p class="text-xs text-slate-500">Incident #{{ $incident->id }} · {{ $incident->device?->building }}</p>
                                            </div>
                                        </div>
                                        <button type="button" @click="openResolve = false" class="text-slate-400 hover:text-slate-600 font-black text-lg cursor-pointer">✕</button>
                                    </div>

                                    <form method="POST" action="{{ route('ndrrmo.incidents.resolve', $incident) }}" class="space-y-4">
                                        @csrf
                                        {{-- Classification Radio Buttons --}}
                                        <div>
                                            <label class="block text-[11px] font-black uppercase text-slate-500 mb-1.5">Classification Type</label>
                                            <div class="grid grid-cols-3 gap-2">
                                                <label :class="resolveType === 'Resolved' ? 'border-emerald-600 bg-emerald-50 text-emerald-900 shadow-xs' : 'border-slate-200 bg-white text-slate-700'" class="p-2.5 border-2 rounded-xl text-center cursor-pointer font-extrabold text-xs transition-all flex flex-col items-center gap-1">
                                                    <input type="radio" name="resolution_type" value="Resolved" x-model="resolveType" class="sr-only">
                                                    <span>✅ Real Event</span>
                                                    <span class="text-[9px] font-normal text-slate-500">Resolved Normally</span>
                                                </label>
                                                <label :class="resolveType === 'False Alarm' ? 'border-amber-600 bg-amber-50 text-amber-900 shadow-xs' : 'border-slate-200 bg-white text-slate-700'" class="p-2.5 border-2 rounded-xl text-center cursor-pointer font-extrabold text-xs transition-all flex flex-col items-center gap-1">
                                                    <input type="radio" name="resolution_type" value="False Alarm" x-model="resolveType" class="sr-only">
                                                    <span>⚠️ False Alarm</span>
                                                    <span class="text-[9px] font-normal text-slate-500">Accidental Trigger</span>
                                                </label>
                                                <label :class="resolveType === 'Drill' ? 'border-purple-600 bg-purple-50 text-purple-900 shadow-xs' : 'border-slate-200 bg-white text-slate-700'" class="p-2.5 border-2 rounded-xl text-center cursor-pointer font-extrabold text-xs transition-all flex flex-col items-center gap-1">
                                                    <input type="radio" name="resolution_type" value="Drill" x-model="resolveType" class="sr-only">
                                                    <span>🛡️ Campus Drill</span>
                                                    <span class="text-[9px] font-normal text-slate-500">Simulation Exercise</span>
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
                                                <option value="Maintenance / inspection trigger">Maintenance / inspection trigger</option>
                                            </select>
                                        </div>

                                        {{-- Drill Type --}}
                                        <div x-show="resolveType === 'Drill'" class="space-y-2">
                                            <label class="block text-[11px] font-black uppercase text-purple-800">Campus Drill Type</label>
                                            <select name="remarks" class="w-full text-xs font-bold px-3 py-2 border border-purple-300 rounded-xl bg-purple-50/50 focus:outline-none">
                                                <option value="Quarterly Earthquake Drill (NSED)">Quarterly Earthquake Drill (NSED)</option>
                                                <option value="Fire Evacuation Drill">Fire Evacuation Drill</option>
                                                <option value="Campus DRRMO Rapid Response Simulation">Campus DRRMO Rapid Response Simulation</option>
                                                <option value="Active Incident / Lockdown Drill">Active Incident / Lockdown Drill</option>
                                            </select>
                                        </div>

                                        {{-- Real Emergency Summary --}}
                                        <div x-show="resolveType === 'Resolved'">
                                            <label class="block text-[11px] font-black uppercase text-slate-700 mb-1">Resolution Summary & Action Taken</label>
                                            <textarea name="remarks" rows="2" class="w-full text-xs font-semibold px-3 py-2 border border-slate-300 rounded-xl focus:border-emerald-500 focus:outline-none" placeholder="e.g. Incident neutralized, all students accounted for, building secured."></textarea>
                                        </div>

                                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                                            <button type="button" @click="openResolve = false" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-xs rounded-xl cursor-pointer">Cancel</button>
                                            <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md transition-all cursor-pointer">Submit Resolution</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Middle Row: Map and Alerts/Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Map Section -->
                <div class="lg:col-span-2 bg-brand-card border border-brand-border rounded-xl flex flex-col overflow-hidden">
                    <div class="px-5 py-4 border-b border-brand-border flex justify-between items-center bg-black/20">
                        <h2 class="text-xs font-bold text-brand-dark uppercase tracking-wider flex items-center">
                            <svg class="w-4 h-4 mr-2 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                            CAMPUS INCIDENT MAP
                        </h2>
                        <a href="{{ route('ndrrmo.map') }}" class="text-[10px] text-brand-blue hover:text-blue-400 font-medium">Full Map</a>
                    </div>
                    <div id="campus-map" class="relative flex-1 min-h-[450px] bg-brand-bg z-0 rounded-b-xl">
                        <!-- Leaflet Map will be injected here -->
                    </div>
                </div>

                <!-- Leaflet CSS & JS -->
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
                
                <style>
                    /* Custom Leaflet Map styling for Dark Mode */
                    .leaflet-container { background-color: #0f1011; }
                    .leaflet-popup-content-wrapper { background-color: #ffffff; color: #334155; border: 1px solid #e2e8f0; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); padding: 0; }
                    .leaflet-popup-tip { background-color: #ffffff; border-bottom: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; }
                    .leaflet-popup-content { margin: 0; }
                    .custom-marker { background: none; border: none; overflow: visible; }
                    .map-pin {
                        filter: drop-shadow(0 3px 2px rgba(15, 23, 42, 0.55)) drop-shadow(0 0 5px rgba(255, 255, 255, 0.95));
                    }
                    .map-pin-ground {
                        position: absolute;
                        bottom: 1px;
                        width: 22px;
                        height: 7px;
                        border-radius: 9999px;
                        background: rgba(15, 23, 42, 0.45);
                        filter: blur(2px);
                    }
                </style>
                
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const map = L.map('campus-map').setView([7.708601, 123.292456], 18);
                        window.ndrrmoCampusMap = map;
                        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
                            maxZoom: 19
                        }).addTo(map);

                        window.deviceMarkers = {};

                        const createMarkerIcon = (colorClass, pulseClass) => {
                            return L.divIcon({
                                className: 'custom-marker',
                                html: `
                                    <div class="relative flex h-14 w-12 items-start justify-center">
                                        ${pulseClass ? `<span class="absolute top-1 h-10 w-10 rounded-full ${pulseClass} opacity-70"></span>` : '<span class="absolute top-1 h-10 w-10 rounded-full bg-white/70 ring-2 ring-white"></span>'}
                                        <span class="map-pin-ground"></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="map-pin relative z-10 h-12 w-12 ${colorClass}" aria-hidden="true">
                                            <path stroke="#ffffff" stroke-width="1.4" stroke-linejoin="round" paint-order="stroke" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                            <circle cx="12" cy="9" r="3.2" fill="#ffffff"/>
                                            <circle cx="12" cy="9" r="1.45" fill="currentColor"/>
                                        </svg>
                                    </div>
                                `,
                                iconSize: [48, 56],
                                iconAnchor: [24, 54],
                                popupAnchor: [0, -52]
                            });
                        };

                        const iconNormal = createMarkerIcon('text-brand-blue', null);
                        const iconCritical = createMarkerIcon('text-brand-red', 'animate-ping bg-brand-red');
                        const iconWarning = createMarkerIcon('text-brand-orange', 'animate-ping bg-brand-orange');

                        const devicesData = @json($devicesList);
                        const activeIncidentsData = @json($activeIncidents);

                        devicesData.forEach(device => {
                            if (!device.latitude || !device.longitude) return;
                            
                            const incident = activeIncidentsData.find(i => i.device_id === device.id);
                            
                            let markerIcon;
                            let popupTitle = 'Device Status: Normal';
                            let popupColor = 'text-brand-green';
                            let statusText = 'Online';
                            let pulseClass = null;
                            let colorClass = 'text-brand-blue';
                            let svgIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>';
                            
                            if (incident) {
                                statusText = 'EMERGENCY ACTIVE';
                                svgIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>';
                                if (incident.emergency_type && incident.emergency_type.includes('Public Safety')) {
                                    pulseClass = 'animate-ping bg-brand-orange';
                                    colorClass = 'text-brand-orange';
                                    popupTitle = 'EMERGENCY: ' + incident.emergency_type;
                                    popupColor = 'text-brand-orange';
                                } else if (incident.emergency_type && (incident.emergency_type.includes('Facility') || incident.emergency_type.includes('Hazard'))) {
                                    pulseClass = 'animate-ping bg-yellow-500';
                                    colorClass = 'text-yellow-500';
                                    popupTitle = 'EMERGENCY: ' + incident.emergency_type;
                                    popupColor = 'text-yellow-500';
                                } else {
                                    pulseClass = 'animate-ping bg-brand-red';
                                    colorClass = 'text-brand-red';
                                    popupTitle = 'EMERGENCY: ' + (incident.emergency_type || 'General');
                                    popupColor = 'text-brand-red';
                                }
                            }

                            markerIcon = createMarkerIcon(colorClass, pulseClass);
                            const marker = L.marker([device.latitude, device.longitude], { icon: markerIcon }).addTo(map);
                            
                            const popupContent = `
                                <div class="p-3 min-w-[200px]">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-10 h-10 rounded-full bg-brand-green/10 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 ${popupColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">${svgIcon}</svg>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-brand-dark text-sm leading-tight">${device.building}</h3>
                                            <div class="text-[10px] text-slate-500 font-medium">Device ID: ${device.device_code}</div>
                                        </div>
                                    </div>
                                    <div class="border-t border-slate-100 pt-3">
                                        <div class="text-[10px] font-bold uppercase tracking-wider ${popupColor} mb-1">
                                            ${popupTitle}
                                        </div>
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-slate-500 font-medium">Status</span>
                                            <span class="font-bold ${popupColor} flex items-center"><span class="w-1.5 h-1.5 rounded-full ${incident ? 'bg-brand-red animate-pulse' : 'bg-brand-green'} mr-1.5"></span>${statusText}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            marker.bindPopup(popupContent);
                            window.deviceMarkers[device.id] = marker;

                            if (incident) {
                                marker.openPopup();
                                map.setView([device.latitude, device.longitude], 18);
                                L.circle([device.latitude, device.longitude], {
                                    color: '#dc2626',
                                    fillColor: '#ef4444',
                                    fillOpacity: 0.4,
                                    radius: 30
                                }).addTo(map);
                            }
                        });

                        window.updateMarkerStatus = function(deviceId, type) {
                            if (window.deviceMarkers[deviceId]) {
                                let newIcon = iconWarning;
                                if (type === 'Critical Emergency') newIcon = iconCritical;
                                window.deviceMarkers[deviceId].setIcon(newIcon);
                            }
                        };
                    });
                </script>

                <!-- Active Alerts & Actions -->
                <div class="flex flex-col gap-6">
                    <!-- Active Alerts -->
                    <div class="bg-brand-card border border-brand-border rounded-xl flex flex-col h-[280px]">
                        <div class="px-5 py-4 flex items-center justify-between border-b border-brand-border">
                            <h2 class="text-xs font-bold text-brand-dark uppercase tracking-wider">ACTIVE ALERTS</h2>
                            <a href="{{ route('ndrrmo.alerts') }}" class="text-[10px] text-brand-blue hover:text-blue-700">View All</a>
                        </div>
                        <div class="flex-1 p-4 flex flex-col gap-3 overflow-y-auto custom-scrollbar">
                            @forelse($activeIncidents as $incident)
                                @php
                                    $borderColor = 'border-brand-red/30';
                                    $bgColor = 'bg-brand-red/5';
                                    $stripeColor = 'bg-brand-red';
                                    $textColor = 'text-brand-red';
                                    $tagClass = 'bg-brand-red';
                                    if(str_contains($incident->emergency_type, 'Medical') || str_contains($incident->emergency_type, 'Safety')) {
                                        $borderColor = 'border-brand-orange/30';
                                        $bgColor = 'bg-brand-orange/5';
                                        $stripeColor = 'bg-brand-orange';
                                        $textColor = 'text-brand-orange';
                                        $tagClass = 'bg-brand-orange';
                                    } elseif(str_contains($incident->emergency_type, 'Facility') || str_contains($incident->emergency_type, 'Hazard')) {
                                        $borderColor = 'border-yellow-500/30';
                                        $bgColor = 'bg-yellow-500/5';
                                        $stripeColor = 'bg-yellow-500';
                                        $textColor = 'text-yellow-500';
                                        $tagClass = 'bg-yellow-500';
                                    }
                                @endphp
                                <div class="border {{ $borderColor }} {{ $bgColor }} rounded-lg p-3 relative overflow-hidden group">
                                    <div class="absolute left-0 top-0 bottom-0 w-1 {{ $stripeColor }}"></div>
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start">
                                            <div class="mt-1 mr-3 {{ $textColor }} animate-pulse">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/></svg>
                                            </div>
                                            <div>
                                                <div class="text-brand-dark font-bold text-sm leading-tight mb-1">{{ $incident->device?->building ?? 'Location not recorded' }}</div>
                                                <div class="text-brand-text text-[11px] mb-2">{{ $incident->emergency_type }}</div>
                                                <div class="text-[10px] text-slate-500">{{ $incident->reported_at->format('M d, g:i A') }} • Device ID: {{ $incident->device?->device_code ?? 'Not recorded' }}</div>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end">
                                            <span class="{{ $tagClass }} text-white text-[8px] font-bold px-2 py-0.5 rounded uppercase tracking-wider mb-2">{{ $incident->emergency_type }}</span>
                                            <span class="{{ $textColor }} text-[10px] font-bold uppercase tracking-wider mb-2">{{ $incident->status }}</span>
                                            <a href="{{ route('ndrrmo.alerts') }}" class="border border-brand-border text-brand-text text-[10px] hover:text-brand-dark hover:bg-brand-hover px-2 py-1 rounded transition-colors">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="flex flex-col items-center justify-center h-full text-brand-text">
                                    <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-sm font-medium">No active alerts</p>
                                </div>
                            @endforelse
                    </div>
                </div>
            </div>
            </div>

            <!-- Bottom Row: Table and Summary -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Incident Logs -->
                <div class="lg:col-span-2 bg-brand-card border border-brand-border rounded-xl flex flex-col">
                    <div class="px-5 py-4 flex items-center justify-between border-b border-brand-border">
                        <h2 class="text-xs font-bold text-brand-dark uppercase tracking-wider">RECENT INCIDENT LOGS</h2>
                        <a href="{{ route('ndrrmo.logs') }}" class="text-[10px] text-brand-blue hover:text-blue-700">View All Logs</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-brand-border text-[10px] text-brand-text uppercase tracking-wider">
                                    <th class="px-5 py-3 font-medium">#</th>
                                    <th class="px-5 py-3 font-medium">Time</th>
                                    <th class="px-5 py-3 font-medium">Incident Type</th>
                                    <th class="px-5 py-3 font-medium">Location</th>
                                    <th class="px-5 py-3 font-medium">Device ID</th>
                                    <th class="px-5 py-3 font-medium">Status</th>
                                    <th class="px-5 py-3 font-medium text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-xs text-brand-dark">
                                @forelse($recentLogs as $index => $log)
                                    @php
                                        $tagClass = 'bg-brand-red';
                                        $statusClass = 'text-brand-red';
                                        
                                        if(str_contains($log->emergency_type, 'Medical') || str_contains($log->emergency_type, 'Safety')) {
                                            $tagClass = 'bg-brand-orange';
                                        } elseif(str_contains($log->emergency_type, 'Facility') || str_contains($log->emergency_type, 'Hazard')) {
                                            $tagClass = 'bg-yellow-500';
                                        }
                                        
                                        if($log->status === 'Resolved') {
                                            $statusClass = 'text-brand-green';
                                        }
                                    @endphp
                                    <tr class="border-b border-brand-border/50 hover:bg-slate-50 hover:shadow-sm hover:-translate-y-0.5 transition-all duration-200 cursor-pointer" onclick="window.location.href='{{ route('ndrrmo.logs') }}'">
                                        <td class="px-5 py-3 text-brand-text">{{ $index + 1 }}</td>
                                        <td class="px-5 py-3">{{ $log->reported_at->format('M d, Y h:i A') }}</td>
                                        <td class="px-5 py-3"><span class="{{ $tagClass }} text-white text-[9px] font-bold px-2 py-0.5 rounded">{{ $log->emergency_type }}</span></td>
                                        <td class="px-5 py-3 text-brand-text">{{ $log->device?->building ?? 'Location not recorded' }}</td>
                                        <td class="px-5 py-3 text-brand-text">{{ $log->device?->device_code ?? 'Not recorded' }}</td>
                                        <td class="px-5 py-3 {{ $statusClass }} font-medium capitalize">{{ $log->status }}</td>
                                        <td class="px-5 py-3 text-center"><a href="{{ route('ndrrmo.logs') }}" class="text-brand-blue hover:text-blue-700" aria-label="View incident log"><svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></a></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-5 py-8 text-center text-brand-text">No recent incident logs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Footer -->
                    <div class="px-5 py-3 border-t border-brand-border flex items-center justify-between text-xs text-brand-text mt-auto">
                        <div>Showing 1 to {{ $recentLogs->count() }} of {{ $totalIncidents }} entries</div>
                        <a href="{{ route('ndrrmo.logs') }}" class="font-bold text-brand-blue hover:text-blue-700">Open complete log</a>
                    </div>
                </div>

                <!-- Incident Summary & Response Status -->
                <div class="bg-brand-card border border-brand-border rounded-xl flex flex-col">
                    <div class="px-5 py-4 border-b border-brand-border">
                        <h2 class="text-xs font-bold text-brand-dark uppercase tracking-wider">INCIDENT SUMMARY (TODAY)</h2>
                    </div>
                    <div class="p-5 flex items-center justify-between border-b border-brand-border border-dashed">
                        @php
                            $cCount = $stats['Critical'] ?? 0;
                            $mCount = $stats['Medical'] ?? 0;
                            $pCount = $stats['Public Safety'] ?? 0;
                            $totalStats = $cCount + $mCount + $pCount;
                            $percentageBase = max($totalStats, 1);
                            
                            $cPct = ($cCount / $percentageBase) * 100;
                            $mPct = ($mCount / $percentageBase) * 100;
                            $pPct = ($pCount / $percentageBase) * 100;
                            
                            $cEnd = $cPct;
                            $mEnd = $cEnd + $mPct;
                        @endphp
                        <!-- Donut Chart -->
                        <div class="relative w-24 h-24 shrink-0">
                            <!-- CSS pure donut chart hack using conic-gradient -->
                            <div class="w-full h-full rounded-full" style="background: {{ $totalStats > 0 ? "conic-gradient(#EF4444 0% {$cEnd}%, #F59E0B {$cEnd}% {$mEnd}%, #EAB308 {$mEnd}% 100%)" : '#e2e8f0' }};"></div>
                            <!-- Inner circle for donut -->
                            <div class="absolute inset-2 bg-brand-card rounded-full flex flex-col items-center justify-center">
                                <span class="text-xl font-bold text-brand-dark leading-none">{{ $totalStats }}</span>
                                <span class="text-[10px] text-brand-text mt-1">Today</span>
                            </div>
                        </div>
                        
                        <!-- Legend -->
                        <div class="ml-4 flex-1 text-[11px]">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-brand-red mr-2"></span><span class="text-brand-text">Critical Emergency</span></div>
                                <div class="text-brand-dark font-medium">{{ $cCount }} ({{ round($cPct, 1) }}%)</div>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-brand-orange mr-2"></span><span class="text-brand-text">Medical Emergency</span></div>
                                <div class="text-brand-dark font-medium">{{ $mCount }} ({{ round($mPct, 1) }}%)</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></span><span class="text-brand-text">Public Safety</span></div>
                                <div class="text-brand-dark font-medium">{{ $pCount }} ({{ round($pPct, 1) }}%)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Response Status -->
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="text-[10px] font-bold text-brand-text uppercase tracking-wider mb-4">RESPONSE STATUS</h3>
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center text-brand-orange">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span class="text-brand-text text-xs">Pending</span>
                            </div>
                            <span class="text-brand-orange font-bold">{{ $stats['Pending'] }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center text-brand-blue">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span class="text-brand-text text-xs">Responding</span>
                            </div>
                            <span class="text-brand-blue font-bold">{{ $stats['Responding'] }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center text-brand-green">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-brand-text text-xs">Resolved</span>
                            </div>
                            <span class="text-brand-green font-bold">{{ $stats['Resolved'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center text-slate-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-brand-text text-xs">Acknowledged</span>
                            </div>
                            <span class="text-brand-dark font-bold">{{ $stats['Acknowledged'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
@endsection
