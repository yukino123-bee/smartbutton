@extends('layouts.clinic')

@section('content')

{{-- ===== STAT CARDS ROW (NDRRMO Layout) ===== --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 text-black">

    {{-- Active Alerts --}}
    <div class="bg-white border border-slate-300 rounded-2xl p-5 flex items-center shadow-sm hover:shadow-md transition-all duration-200">
        <div class="w-12 h-12 rounded-xl bg-red-100 border border-red-300 flex items-center justify-center mr-4 shrink-0">
            <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <div class="text-[11px] font-black text-black uppercase tracking-wider mb-1">ACTIVE ALERTS</div>
            <div id="stat-active-alerts" class="text-3xl font-black text-brand-red leading-none mb-1 tabular-nums">{{ $activeAlerts ?? 0 }}</div>
            <div class="text-[11px] font-bold text-brand-red">Needs attention</div>
        </div>
    </div>

    {{-- Incoming Patients --}}
    <div class="bg-white border border-slate-300 rounded-2xl p-5 flex items-center shadow-sm hover:shadow-md transition-all duration-200">
        <div class="w-12 h-12 rounded-xl bg-blue-100 border border-blue-300 flex items-center justify-center mr-4 shrink-0">
            <svg class="w-6 h-6 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
        </div>
        <div>
            <div class="text-[11px] font-black text-black uppercase tracking-wider mb-1">INCOMING</div>
            <div id="stat-incoming" class="text-3xl font-black text-black leading-none mb-1 tabular-nums">{{ $incomingCount ?? 0 }}</div>
            <div class="text-[11px] font-bold text-slate-700">Patient today</div>
        </div>
    </div>

    {{-- Patients Treated --}}
    <div class="bg-white border border-slate-300 rounded-2xl p-5 flex items-center shadow-sm hover:shadow-md transition-all duration-200">
        <div class="w-12 h-12 rounded-xl bg-green-100 border border-green-300 flex items-center justify-center mr-4 shrink-0">
            <svg class="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div>
            <div class="text-[11px] font-black text-black uppercase tracking-wider mb-1">TREATED TODAY</div>
            <div id="stat-treated" class="text-3xl font-black text-brand-green leading-none mb-1 tabular-nums">{{ $treatedTodayCount ?? 0 }}</div>
            <div class="text-[11px] font-bold text-slate-700">Total patients</div>
        </div>
    </div>

    {{-- Resolved Incidents --}}
    <div class="bg-white border border-slate-300 rounded-2xl p-5 flex items-center shadow-sm hover:shadow-md transition-all duration-200">
        <div class="w-12 h-12 rounded-xl bg-teal-100 border border-teal-300 flex items-center justify-center mr-4 shrink-0">
            <svg class="w-6 h-6 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="text-[11px] font-black text-black uppercase tracking-wider mb-1">RESOLVED TODAY</div>
            <div id="stat-resolved" class="text-3xl font-black text-black leading-none mb-1 tabular-nums">{{ $resolvedTodayCount ?? 0 }}</div>
            <div class="text-[11px] font-bold text-slate-700">Incidents closed</div>
        </div>
    </div>
</div>

{{-- ===== ACTIVE EMERGENCY BANNER (Dynamic) ===== --}}
<div id="active-emergency-banner" class="bg-white border-2 border-red-500 rounded-3xl mb-6 overflow-hidden shadow-lg text-black {{ $activeEmergency ? '' : 'hidden' }}">
    {{-- Header bar --}}
    <div class="bg-red-600 px-6 py-3.5 flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-3">
            <span class="w-3 h-3 rounded-full bg-white animate-ping"></span>
            <span class="text-white font-black text-xs md:text-sm uppercase tracking-widest">⚠ CRITICAL EMERGENCY — ACTIVE RESPONSE</span>
        </div>
        <span id="emergency-timestamp" class="text-white text-xs font-extrabold bg-black/30 px-3.5 py-1 rounded-full">
            {{ $activeEmergency ? $activeEmergency->created_at->format('h:i A · M d, Y') : '' }}
        </span>
    </div>

    {{-- Body --}}
    <div class="flex flex-col lg:flex-row divide-y lg:divide-y-0 lg:divide-x divide-slate-200">

        {{-- Emergency Details --}}
        <div class="flex-1 p-6 flex flex-col sm:flex-row gap-5 items-start">
            <div class="w-14 h-14 rounded-2xl bg-red-100 border-2 border-red-300 flex items-center justify-center shrink-0 shadow-sm text-red-600 text-2xl font-black">
                🚨
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-black text-slate-900 mb-2">Emergency Patient Incoming!</h2>
                <div class="space-y-1.5 text-xs font-bold text-slate-700">
                    <div class="flex items-center gap-2">
                        <span class="text-slate-400 font-extrabold uppercase tracking-wider text-[10px]">BUILDING:</span>
                        <span id="emergency-building" class="font-black text-slate-900 text-sm">{{ $activeEmergency->device->building ?? 'Engineering Building' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-slate-400 font-extrabold uppercase tracking-wider text-[10px]">DEVICE:</span>
                        <span id="emergency-device-code" class="font-mono font-black text-slate-900">{{ $activeEmergency->device->device_code ?? 'ENG-001' }}</span>
                        <span id="emergency-device-name" class="text-slate-500 font-semibold">({{ $activeEmergency->device->room ?? 'Room N/A' }})</span>
                    </div>
                </div>

                {{-- Checklist --}}
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2 pt-3 border-t border-slate-100">
                    @foreach(['Prepare medical stretcher', 'Alert clinic doctor/nurses', 'Prepare oxygen & first aid', 'Coordinate with NDRRMO'] as $item)
                    <div class="flex items-center gap-2 text-[11px] font-bold text-slate-700">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ $item }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Timer + Action --}}
        <div class="w-full lg:w-72 p-6 flex flex-col items-center justify-center gap-4 bg-red-50/70 shrink-0">
            <div class="text-center">
                <p class="text-[10px] font-black text-red-700 uppercase tracking-widest mb-1">Estimated Arrival</p>
                <p id="emergency-countdown" class="text-4xl font-black text-red-600 leading-none tabular-nums">03:00</p>
                <p class="text-[11px] text-slate-500 font-bold mt-1">minutes</p>
            </div>
            <button id="btn-patient-arrived" onclick="resolveActiveEmergency({{ $activeEmergency->id ?? 'null' }})" class="w-full bg-red-600 hover:bg-red-700 active:scale-95 text-white font-extrabold py-3 px-4 rounded-2xl shadow-md shadow-red-200 transition-all flex items-center justify-center gap-2 text-xs cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>Patient Arrived & Treated</span>
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
                <tbody id="active-alerts-tbody" class="text-xs divide-y divide-slate-200">
                    @forelse($criticalIncidents as $index => $incident)
                    <tr class="bg-red-50/60 hover:bg-red-100/50 transition-colors" id="incident-row-{{ $incident->id }}">
                        <td class="px-5 py-4 text-black font-bold row-no">{{ $index + 1 }}</td>
                        <td class="px-5 py-4 font-black text-black">{{ $incident->created_at->format('h:i A') }}</td>
                        <td class="px-5 py-4 font-bold text-black">{{ $incident->device->building ?? 'Campus Building' }}</td>
                        <td class="px-5 py-4 text-black font-mono font-bold text-[11px]">{{ $incident->device->device_code ?? 'N/A' }}</td>
                        <td class="px-5 py-4"><span class="inline-flex items-center bg-red-600 text-white text-[10px] font-black px-2.5 py-1 rounded-full">Critical Emergency</span></td>
                        <td class="px-5 py-4"><span class="inline-flex items-center gap-1.5 text-brand-red font-black"><span class="w-2 h-2 rounded-full bg-brand-red animate-pulse"></span>{{ ucfirst($incident->status) }}</span></td>
                        <td class="px-5 py-4 text-center">
                            <button onclick="resolveActiveEmergency({{ $incident->id }})" class="bg-brand-blue hover:bg-blue-800 text-white font-bold px-3 py-1.5 rounded-lg text-xs transition-colors shadow-sm">
                                Acknowledge
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr id="no-active-alerts-row">
                        <td colspan="7" class="px-5 py-8 text-center text-slate-700 font-bold">
                            No active critical alerts right now.
                        </td>
                    </tr>
                    @endforelse
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
                <tbody id="alert-history-tbody" class="text-xs divide-y divide-slate-200">
                    @forelse($recentHistory as $index => $incident)
                    <tr class="hover:bg-slate-100/70 transition-colors" id="history-row-{{ $incident->id }}">
                        <td class="px-5 py-4 text-black font-bold row-no">{{ $index + 1 }}</td>
                        <td class="px-5 py-4 font-black text-black">{{ $incident->created_at->format('h:i A') }}</td>
                        <td class="px-5 py-4 font-bold text-black">{{ $incident->device->building ?? 'Campus Building' }}</td>
                        <td class="px-5 py-4 text-black font-mono font-bold text-[11px]">{{ $incident->device->device_code ?? 'N/A' }}</td>
                        <td class="px-5 py-4"><span class="inline-flex items-center bg-red-600 text-white text-[10px] font-black px-2.5 py-1 rounded-full">{{ $incident->emergency_type }}</span></td>
                        <td class="px-5 py-4 status-cell">
                            @if($incident->status === 'resolved')
                                <span class="inline-flex items-center gap-1.5 text-brand-green font-black"><span class="w-2 h-2 rounded-full bg-brand-green"></span>Resolved</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-black font-bold"><span class="w-2 h-2 rounded-full bg-slate-600"></span>{{ ucfirst($incident->status) }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-xs font-bold text-slate-700">Recorded</span>
                        </td>
                    </tr>
                    @empty
                    <tr id="no-history-row">
                        <td colspan="7" class="px-5 py-8 text-center text-slate-700 font-bold">
                            No alert history recorded today.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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

            // Remove row from Active Critical Alerts table
            const row = document.getElementById('incident-row-' + incidentId);
            if (row) row.remove();

            // Check if active alerts table is empty
            const tbody = document.getElementById('active-alerts-tbody');
            if (tbody && tbody.querySelectorAll('tr[id^="incident-row-"]').length === 0) {
                tbody.innerHTML = `
                <tr id="no-active-alerts-row">
                    <td colspan="7" class="px-5 py-8 text-center text-slate-700 font-bold">
                        No active critical alerts right now.
                    </td>
                </tr>`;
            }

            // Update status in history table
            const histRow = document.getElementById('history-row-' + incidentId);
            if (histRow) {
                const statusCell = histRow.querySelector('.status-cell');
                if (statusCell) {
                    statusCell.innerHTML = `<span class="inline-flex items-center gap-1.5 text-brand-green font-black"><span class="w-2 h-2 rounded-full bg-brand-green"></span>Resolved</span>`;
                }
            }
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
