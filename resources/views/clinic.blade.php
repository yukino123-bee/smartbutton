@extends('layouts.clinic')

@section('content')

{{-- ===== STAT CARDS ROW ===== --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6 text-black">

    {{-- Active Alerts --}}
    <div class="bg-white border border-slate-300 rounded-2xl p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-red-100 border border-red-300 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <p class="text-[11px] font-black text-black uppercase tracking-wider mb-0.5">Active Alerts</p>
            <p id="stat-active-alerts" class="text-3xl font-black text-black leading-none tabular-nums">{{ $activeAlerts ?? 0 }}</p>
            <p class="text-[11px] text-brand-red font-extrabold mt-1">Needs attention</p>
        </div>
    </div>

    {{-- Incoming Patients --}}
    <div class="bg-white border border-slate-300 rounded-2xl p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-blue-100 border border-blue-300 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
        </div>
        <div>
            <p class="text-[11px] font-black text-black uppercase tracking-wider mb-0.5">Incoming</p>
            <p id="stat-incoming" class="text-3xl font-black text-black leading-none tabular-nums">{{ $incomingCount ?? 0 }}</p>
            <p class="text-[11px] text-black font-bold mt-1">Patient today</p>
        </div>
    </div>

    {{-- Patients Treated --}}
    <div class="bg-white border border-slate-300 rounded-2xl p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-green-100 border border-green-300 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div>
            <p class="text-[11px] font-black text-black uppercase tracking-wider mb-0.5">Treated Today</p>
            <p id="stat-treated" class="text-3xl font-black text-black leading-none tabular-nums">{{ $treatedTodayCount ?? 0 }}</p>
            <p class="text-[11px] text-black font-bold mt-1">Total patients</p>
        </div>
    </div>

    {{-- Resolved --}}
    <div class="bg-white border border-slate-300 rounded-2xl p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-teal-100 border border-teal-300 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-[11px] font-black text-black uppercase tracking-wider mb-0.5">Resolved Today</p>
            <p id="stat-resolved" class="text-3xl font-black text-black leading-none tabular-nums">{{ $resolvedTodayCount ?? 0 }}</p>
            <p class="text-[11px] text-black font-bold mt-1">Incidents closed</p>
        </div>
    </div>
</div>

{{-- ===== ACTIVE EMERGENCY BANNER (Dynamic) ===== --}}
<div id="active-emergency-banner" class="bg-white border-2 border-red-300 rounded-2xl mb-6 overflow-hidden shadow-md text-black {{ $activeEmergency ? '' : 'hidden' }}">
    {{-- Header bar --}}
    <div class="bg-brand-red px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="w-2.5 h-2.5 rounded-full bg-white animate-ping"></span>
            <span class="text-white font-black text-sm uppercase tracking-widest">⚠ Critical Emergency — Active</span>
        </div>
        <span id="emergency-timestamp" class="text-white text-xs font-bold bg-black/30 px-3 py-1 rounded-full">
            {{ $activeEmergency ? $activeEmergency->created_at->format('h:i A · M d, Y') : '' }}
        </span>
    </div>

    {{-- Body --}}
    <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-200">

        {{-- Emergency Details --}}
        <div class="col-span-1 md:col-span-2 p-6 flex gap-5 items-start">
            <div class="w-14 h-14 rounded-full bg-red-100 border-2 border-red-300 flex items-center justify-center shrink-0 shadow-sm">
                <svg class="w-8 h-8 text-brand-red" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c-4.97 0-9 4.03-9 9 0 3.86 2.43 7.15 5.91 8.46l.09.04V22h6v-2.5l.09-.04C18.57 18.15 21 14.86 21 11c0-4.97-4.03-9-9-9zm0 2c3.86 0 7 3.14 7 7 0 2.89-1.74 5.38-4.26 6.45L14 17.75V19h-4v-1.25l-.74-.3C6.74 16.38 5 13.89 5 11c0-3.86 3.14-7 7-7zm-1 3v5h2V7h-2zm0 7v2h2v-2h-2z"/></svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-black mb-3">Emergency Incoming!</h2>
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-sm text-black font-bold">
                        <svg class="w-5 h-5 text-brand-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span id="emergency-building" class="font-extrabold text-black text-base">{{ $activeEmergency->device->building ?? 'Engineering Building' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-black font-bold">
                        <svg class="w-5 h-5 text-brand-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        <span>Device: <span id="emergency-device-code" class="font-black text-black">{{ $activeEmergency->device->device_code ?? 'GYM-001' }}</span> <span id="emergency-device-name" class="text-black font-bold">({{ $activeEmergency->device->name ?? 'Gymnasium' }})</span></span>
                    </div>
                </div>

                {{-- Checklist --}}
                <div class="mt-4 grid grid-cols-2 gap-2">
                    @foreach(['Prepare emergency equipment', 'Prepare medical staff', 'Standby for patient', 'Coordinate with NDRRMO'] as $item)
                    <div class="flex items-center gap-2 text-xs font-bold text-black">
                        <svg class="w-4 h-4 text-brand-green shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ $item }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Timer + Action --}}
        <div class="p-6 flex flex-col items-center justify-center gap-4 bg-red-100/50">
            <div class="text-center">
                <p class="text-[11px] font-black text-black uppercase tracking-wider mb-1">Estimated Arrival</p>
                <p id="emergency-countdown" class="text-5xl font-black text-brand-red leading-none tabular-nums">03:00</p>
                <p class="text-xs text-black font-bold mt-1">minutes</p>
            </div>
            <button id="btn-patient-arrived" onclick="resolveActiveEmergency({{ $activeEmergency->id ?? 'null' }})" class="w-full bg-brand-red hover:bg-red-700 active:scale-95 text-white font-black py-3 px-4 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2 text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                Patient Arrived
            </button>
        </div>
    </div>
</div>

{{-- ===== NO EMERGENCY STANDBY BANNER ===== --}}
<div id="no-emergency-banner" class="bg-white border border-green-300 rounded-2xl mb-6 p-6 shadow-sm flex items-center justify-between text-black {{ $activeEmergency ? 'hidden' : '' }}">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-green-100 border border-green-300 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <h3 class="text-base font-black text-black">System Normal — No Active Emergency</h3>
            <p class="text-xs font-bold text-slate-700 mt-0.5">Clinic emergency response team is on standby. All panic button devices are online.</p>
        </div>
    </div>
    <span class="inline-flex items-center gap-1.5 bg-green-100 text-brand-green text-xs font-black px-3 py-1.5 rounded-full border border-green-300">
        <span class="w-2.5 h-2.5 rounded-full bg-brand-green animate-pulse"></span>STANDBY READY
    </span>
</div>

{{-- ===== BOTTOM SECTION: TABLES ===== --}}
<div class="flex flex-col gap-6 text-black">

    {{-- Active Critical Alerts Table --}}
    <div class="bg-white border border-slate-300 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 flex items-center justify-between border-b border-slate-200 bg-slate-50">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-brand-red animate-pulse"></span>
                <h2 class="text-xs font-black text-black uppercase tracking-wider">Active Critical Alerts</h2>
            </div>
            <a href="{{ route('clinic.alerts') }}" class="text-[11px] text-brand-blue hover:underline font-extrabold">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100 border-b border-slate-300 text-[11px] text-black font-black uppercase tracking-wider">
                        <th class="px-5 py-3.5">#</th>
                        <th class="px-5 py-3.5">Time</th>
                        <th class="px-5 py-3.5">Location</th>
                        <th class="px-5 py-3.5">Device</th>
                        <th class="px-5 py-3.5">Type</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-200">
                    <tr class="bg-red-50/60 hover:bg-red-100/50 transition-colors">
                        <td class="px-5 py-4 text-black font-bold">1</td>
                        <td class="px-5 py-4 font-black text-black">10:28 AM</td>
                        <td class="px-5 py-4 font-bold text-black">Engineering Building</td>
                        <td class="px-5 py-4 text-black font-mono font-bold text-[11px]">GYM-001</td>
                        <td class="px-5 py-4"><span class="inline-flex items-center bg-red-600 text-white text-[10px] font-black px-2.5 py-1 rounded-full">Critical Emergency</span></td>
                        <td class="px-5 py-4"><span class="inline-flex items-center gap-1.5 text-brand-red font-black"><span class="w-2 h-2 rounded-full bg-brand-red animate-pulse"></span>Incoming</span></td>
                        <td class="px-5 py-4 text-center">
                            <button class="text-brand-blue hover:text-blue-900 transition-colors p-1.5 rounded-lg hover:bg-blue-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Alert History Table --}}
    <div class="bg-white border border-slate-300 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 flex items-center justify-between border-b border-slate-200 bg-slate-50">
            <h2 class="text-xs font-black text-black uppercase tracking-wider">Alert History (Today)</h2>
            <a href="{{ route('clinic.logs') }}" class="text-[11px] text-brand-blue hover:underline font-extrabold">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100 border-b border-slate-300 text-[11px] text-black font-black uppercase tracking-wider">
                        <th class="px-5 py-3.5">#</th>
                        <th class="px-5 py-3.5">Time</th>
                        <th class="px-5 py-3.5">Location</th>
                        <th class="px-5 py-3.5">Device</th>
                        <th class="px-5 py-3.5">Type</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-200">
                    @foreach([
                        ['1', '09:15 AM', 'Gymnasium', 'GYM-001', 'Resolved'],
                        ['2', '08:42 AM', 'Library', 'LIB-001', 'Resolved'],
                        ['3', '07:55 AM', 'Engineering Building', 'ENG-001', 'Resolved'],
                        ['4', '06:30 AM', 'Gymnasium', 'GYM-001', 'Cancelled'],
                    ] as [$no, $time, $loc, $dev, $status])
                    <tr class="hover:bg-slate-100/70 transition-colors">
                        <td class="px-5 py-4 text-black font-bold">{{ $no }}</td>
                        <td class="px-5 py-4 font-black text-black">{{ $time }}</td>
                        <td class="px-5 py-4 font-bold text-black">{{ $loc }}</td>
                        <td class="px-5 py-4 text-black font-mono font-bold text-[11px]">{{ $dev }}</td>
                        <td class="px-5 py-4"><span class="inline-flex items-center bg-red-600 text-white text-[10px] font-black px-2.5 py-1 rounded-full">Critical</span></td>
                        <td class="px-5 py-4">
                            @if($status === 'Resolved')
                                <span class="inline-flex items-center gap-1.5 text-brand-green font-black"><span class="w-2 h-2 rounded-full bg-brand-green"></span>{{ $status }}</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-black font-bold"><span class="w-2 h-2 rounded-full bg-slate-600"></span>{{ $status }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            <button class="text-brand-blue hover:text-blue-900 transition-colors p-1.5 rounded-lg hover:bg-blue-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>

<script>
    window.currentEmergencyId = {{ $activeEmergency->id ?? 'null' }};
    window.countdownInterval = null;

    window.resolveActiveEmergency = function(id) {
        const incidentId = id || window.currentEmergencyId;
        if (incidentId) {
            fetch('/clinic/incidents/' + incidentId + '/resolve', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => console.log('Resolved:', data))
            .catch(err => console.error(err));
        }

        // Hide active banner, show standby banner
        document.getElementById('active-emergency-banner')?.classList.add('hidden');
        document.getElementById('no-emergency-banner')?.classList.remove('hidden');

        // Update stats
        const activeEl = document.getElementById('stat-active-alerts');
        const treatedEl = document.getElementById('stat-treated');
        const resolvedEl = document.getElementById('stat-resolved');

        if (activeEl) {
            let num = parseInt(activeEl.textContent || '0');
            activeEl.textContent = Math.max(0, isNaN(num) ? 0 : num - 1);
        }
        if (treatedEl) {
            let num = parseInt(treatedEl.textContent || '0');
            treatedEl.textContent = isNaN(num) ? 1 : num + 1;
        }
        if (resolvedEl) {
            let num = parseInt(resolvedEl.textContent || '0');
            resolvedEl.textContent = isNaN(num) ? 1 : num + 1;
        }

        if (window.updateAlertBadges) {
            let cur = parseInt(document.getElementById('sidebar-alert-badge')?.textContent || '0');
            window.updateAlertBadges(Math.max(0, isNaN(cur) ? 0 : cur - 1));
        }
    };
</script>

@endsection
