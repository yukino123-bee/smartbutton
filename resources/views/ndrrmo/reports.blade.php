@extends('layouts.ndrrmo')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-xl font-bold text-brand-dark mb-1">Incident Reports & Analytics</h2>
        <p class="text-xs text-brand-text">Statistical overview of campus emergencies and response efficiency.</p>
    </div>
    <div class="flex items-center space-x-3 text-xs">
        <select class="bg-brand-card border border-brand-border text-brand-dark rounded-lg px-4 py-2 outline-none focus:border-brand-blue">
            <option>This Month</option>
            <option>Last Month</option>
            <option>This Year</option>
            <option>All Time</option>
        </select>

    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Total Incidents Card -->
    <div class="bg-brand-card border border-brand-border rounded-xl p-6 flex flex-col justify-center relative overflow-hidden">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-brand-blue/5 rounded-full"></div>
        <div class="text-[10px] text-brand-text uppercase font-bold tracking-wider mb-2 z-10">TOTAL INCIDENTS LOGGED</div>
        <div class="text-5xl font-black text-brand-dark mb-2 z-10">{{ $totalIncidents }}</div>
        <div class="text-xs text-brand-blue font-medium flex items-center z-10">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            12% increase from last period
        </div>
    </div>

    <!-- Status Breakdown -->
    <div class="lg:col-span-2 bg-brand-card border border-brand-border rounded-xl p-6 flex items-center justify-around">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-brand-red/10 text-brand-red mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="text-3xl font-bold text-brand-dark mb-1">{{ $stats['pending'] ?? 0 }}</div>
            <div class="text-[10px] text-brand-text uppercase font-bold tracking-wider">PENDING</div>
        </div>
        
        <div class="w-px h-16 bg-brand-border"></div>

        <div class="text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-brand-blue/10 text-brand-blue mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div class="text-3xl font-bold text-brand-dark mb-1">{{ $stats['responding'] ?? 0 }}</div>
            <div class="text-[10px] text-brand-text uppercase font-bold tracking-wider">RESPONDING</div>
        </div>
        
        <div class="w-px h-16 bg-brand-border"></div>

        <div class="text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-brand-green/10 text-brand-green mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="text-3xl font-bold text-brand-dark mb-1">{{ $stats['resolved'] ?? 0 }}</div>
            <div class="text-[10px] text-brand-text uppercase font-bold tracking-wider">RESOLVED</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Incidents by Type -->
    <div class="bg-brand-card border border-brand-border rounded-xl p-6">
        <h3 class="text-xs font-bold text-brand-dark uppercase tracking-wider mb-6">Incidents by Type</h3>
        
        <div class="space-y-5">
            @foreach($typeStats as $type => $count)
                @php
                    $percentage = $totalIncidents > 0 ? round(($count / $totalIncidents) * 100) : 0;
                    $bg = 'bg-slate-500';
                    if ($type === 'Medical Emergency') $bg = 'bg-brand-orange';
                    elseif ($type === 'General Emergency' || $type === 'Critical Emergency') $bg = 'bg-brand-red';
                    elseif ($type === 'Public Safety Emergency') $bg = 'bg-yellow-500';
                @endphp
                <div>
                    <div class="flex justify-between items-end mb-1 text-xs">
                        <span class="text-brand-dark font-medium">{{ $type }}</span>
                        <span class="text-brand-text">{{ $count }} ({{ $percentage }}%)</span>
                    </div>
                    <div class="w-full bg-brand-bg rounded-full h-2 overflow-hidden border border-brand-border">
                        <div class="{{ $bg }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            @endforeach
            
            @if($typeStats->isEmpty())
                <div class="text-center py-8 text-brand-text text-sm">No incident data available.</div>
            @endif
        </div>
    </div>
    
    <!-- Average Response Time -->
    <div class="bg-brand-card border border-brand-border rounded-xl p-6 flex flex-col items-center justify-center text-center">
        <div class="w-24 h-24 rounded-full border-4 border-brand-border flex items-center justify-center mb-6 relative">
            <svg class="w-10 h-10 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div class="absolute -right-2 -bottom-2 bg-brand-green text-white text-[10px] font-bold px-2 py-1 rounded-full border-2 border-brand-card">GOOD</div>
        </div>
        <div class="text-[10px] text-brand-text uppercase font-bold tracking-wider mb-2">AVERAGE RESPONSE TIME</div>
        <div class="text-4xl font-bold text-brand-dark mb-2">3m 42s</div>
        <p class="text-xs text-slate-400 max-w-xs">Time elapsed between button press and NDRRMO team acknowledging the alert.</p>
    </div>
</div>
@endsection
