@extends('layouts.ndrrmo')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-xl font-bold text-brand-dark mb-1">Live Alerts</h2>
        <p class="text-xs text-brand-text">Monitor active emergencies requiring immediate response.</p>
    </div>
    <div class="flex items-center space-x-3 text-xs">
        <span class="px-3 py-1 bg-brand-red/10 border border-brand-red/30 text-brand-red rounded-lg font-medium flex items-center">
            <span class="w-2 h-2 rounded-full bg-brand-red animate-pulse mr-2"></span> {{ $alerts->where('status', 'pending')->count() }} Pending
        </span>
        <span class="px-3 py-1 bg-brand-blue/10 border border-brand-blue/30 text-brand-blue rounded-lg font-medium flex items-center">
            <span class="w-2 h-2 rounded-full bg-brand-blue mr-2"></span> {{ $alerts->where('status', 'responding')->count() }} Responding
        </span>
    </div>
</div>

@if($alerts->isEmpty())
<div class="bg-brand-card border border-brand-border rounded-xl p-12 text-center flex flex-col items-center justify-center">
    <div class="w-16 h-16 bg-brand-bg rounded-full flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-brand-text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    </div>
    <h3 class="text-brand-dark font-medium text-lg mb-1">All Clear</h3>
    <p class="text-brand-text text-sm">There are no active emergency alerts at this time.</p>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($alerts as $alert)
        @php
            $color = 'brand-red';
            $bgColor = 'bg-brand-red';
            if ($alert->emergency_type === 'Medical Emergency') { $color = 'brand-orange'; $bgColor = 'bg-brand-orange'; }
            if ($alert->emergency_type === 'Public Safety Emergency') { $color = 'yellow-500'; $bgColor = 'bg-yellow-500'; }
        @endphp
        
        <div class="bg-brand-card border border-brand-border rounded-xl flex flex-col overflow-hidden shadow-lg relative group">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $bgColor }}"></div>
            
            <div class="p-5 border-b border-brand-border flex justify-between items-start">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded bg-{{ $color }}/10 flex items-center justify-center text-{{ $color }} mr-3">
                        <svg class="w-6 h-6 animate-pulse" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/></svg>
                    </div>
                    <div>
                        <div class="text-[10px] text-brand-text uppercase font-bold tracking-wider mb-0.5">LOCATION</div>
                        <h3 class="text-brand-dark font-bold text-sm">{{ $alert->device->building ?? 'Unknown' }}</h3>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-[10px] text-brand-text uppercase font-bold tracking-wider mb-0.5">STATUS</div>
                    <span class="text-{{ $color }} text-[11px] font-bold uppercase tracking-wider">{{ $alert->status }}</span>
                </div>
            </div>
            
            <div class="p-5 flex-1">
                <div class="mb-4">
                    <div class="text-[10px] text-brand-text uppercase font-bold tracking-wider mb-1">INCIDENT TYPE</div>
                    <div class="inline-block {{ $bgColor }} text-brand-dark text-[11px] font-bold px-2 py-1 rounded uppercase tracking-wide">
                        {{ $alert->emergency_type }}
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-[10px] text-brand-text uppercase font-bold tracking-wider mb-1">TIME REPORTED</div>
                        <div class="text-xs text-brand-dark">{{ $alert->created_at->format('M d, Y h:i A') }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] text-brand-text uppercase font-bold tracking-wider mb-1">DEVICE ID</div>
                        <div class="text-xs text-brand-dark">{{ $alert->device->device_code ?? 'N/A' }}</div>
                    </div>
                </div>
                
                @if($alert->remarks)
                <div class="mt-4 pt-4 border-t border-brand-border border-dashed">
                    <div class="text-[10px] text-brand-text uppercase font-bold tracking-wider mb-1">REMARKS</div>
                    <div class="text-xs text-slate-400 italic">{{ $alert->remarks }}</div>
                </div>
                @endif
            </div>
            
            <div class="p-4 bg-brand-bg border-t border-brand-border grid grid-cols-2 gap-3">
                <button class="bg-brand-blue hover:bg-blue-600 text-white rounded py-2 text-xs font-medium transition-colors">
                    Acknowledge
                </button>
                <button class="bg-brand-green hover:bg-green-600 text-white rounded py-2 text-xs font-medium transition-colors">
                    Mark Resolved
                </button>
            </div>
        </div>
    @endforeach
</div>
@endif
@endsection
