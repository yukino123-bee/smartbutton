@extends('layouts.clinic')

@section('content')

{{-- Header Row --}}
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-slate-200 shadow-sm">
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

{{-- Top Metrics Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm">
        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">TOTAL CLINIC INCIDENTS</div>
        <div class="text-3xl font-black text-slate-900 leading-none mb-1 tabular-nums">{{ $totalIncidents }}</div>
        <div class="text-[11px] font-bold text-slate-500">Recorded system-wide</div>
    </div>

    <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm">
        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">PATIENTS TREATED</div>
        <div class="text-3xl font-black text-emerald-600 leading-none mb-1 tabular-nums">{{ $totalTreated }}</div>
        <div class="text-[11px] font-bold text-slate-500">Resolved medical cases</div>
    </div>

    <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm">
        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">MEDICAL EMERGENCIES</div>
        <div class="text-3xl font-black text-orange-600 leading-none mb-1 tabular-nums">{{ $typeStats['Medical Emergency'] ?? 0 }}</div>
        <div class="text-[11px] font-bold text-slate-500">Health & first aid calls</div>
    </div>

    <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm">
        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">CRITICAL ALERTS</div>
        <div class="text-3xl font-black text-red-600 leading-none mb-1 tabular-nums">{{ $typeStats['Critical Emergency'] ?? 0 }}</div>
        <div class="text-[11px] font-bold text-slate-500">Severe emergency alerts</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Category Breakdown Card -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide mb-4 pb-3 border-b border-slate-100">
            Emergency Type Distribution
        </h3>
        <div class="space-y-4">
            @forelse($typeStats as $type => $count)
            <div>
                <div class="flex justify-between text-xs font-bold text-slate-800 mb-1.5">
                    <span>{{ $type }}</span>
                    <span class="font-black">{{ $count }} cases ({{ $totalIncidents > 0 ? round(($count / $totalIncidents) * 100) : 0 }}%)</span>
                </div>
                <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                    <div class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width: {{ $totalIncidents > 0 ? ($count / $totalIncidents) * 100 : 0 }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-xs text-slate-500 font-bold">No incident data available yet.</p>
            @endforelse
        </div>
    </div>

    <!-- Building Breakdown Card -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide mb-4 pb-3 border-b border-slate-100">
            Incidents by Building Location
        </h3>
        <div class="space-y-4">
            @forelse($buildingStats as $building => $count)
            <div>
                <div class="flex justify-between text-xs font-bold text-slate-800 mb-1.5">
                    <span>{{ $building }}</span>
                    <span class="font-black">{{ $count }} calls</span>
                </div>
                <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $totalIncidents > 0 ? ($count / $totalIncidents) * 100 : 0 }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-xs text-slate-500 font-bold">No location records logged yet.</p>
            @endforelse
        </div>
    </div>
</div>

@endsection
