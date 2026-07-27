@extends('layouts.ndrrmo')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-xl font-bold text-brand-dark mb-1">Incident Logs</h2>
        <p class="text-xs text-brand-text">Complete history of all reported emergencies and responses.</p>
    </div>
    <div class="flex items-center space-x-3">
        <button class="bg-brand-card hover:bg-brand-hover border border-brand-border text-brand-dark rounded-lg flex items-center px-4 py-2 text-xs transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            Filter Logs
        </button>
        <button class="bg-brand-blue hover:bg-blue-600 text-white rounded-lg flex items-center px-4 py-2 text-xs transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Export to CSV
        </button>
    </div>
</div>

<div class="bg-brand-card border border-brand-border rounded-xl flex flex-col overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-brand-bg/50 border-b border-brand-border text-[10px] text-brand-text uppercase tracking-wider">
                    <th class="px-6 py-4 font-bold">Time Reported</th>
                    <th class="px-6 py-4 font-bold">Incident Type</th>
                    <th class="px-6 py-4 font-bold">Location</th>
                    <th class="px-6 py-4 font-bold">Device ID</th>
                    <th class="px-6 py-4 font-bold">Status</th>
                    <th class="px-6 py-4 font-bold text-center">Action</th>
                </tr>
            </thead>
            <tbody class="text-xs text-brand-dark">
                @forelse($logs as $log)
                <tr class="border-b border-brand-border/50 hover:bg-brand-hover transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-brand-dark font-medium">{{ $log->created_at->format('M d, Y') }}</div>
                        <div class="text-[10px] text-slate-500">{{ $log->created_at->format('h:i:s A') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $bg = 'bg-slate-500';
                            if ($log->emergency_type === 'Medical Emergency') $bg = 'bg-brand-orange';
                            elseif ($log->emergency_type === 'General Emergency' || $log->emergency_type === 'Critical Emergency') $bg = 'bg-brand-red';
                            elseif ($log->emergency_type === 'Public Safety Emergency') $bg = 'bg-yellow-500';
                        @endphp
                        <span class="{{ $bg }} text-brand-dark text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider shadow-sm">
                            {{ $log->emergency_type }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-brand-dark font-medium">{{ $log->device->building ?? 'Unknown Location' }}</div>
                        <div class="text-[10px] text-brand-text">{{ $log->device->floor ?? '' }} {{ $log->device->room ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4 text-brand-text font-mono text-[11px]">{{ $log->device->device_code ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        @php
                            $statusColor = 'text-brand-text';
                            if ($log->status === 'pending') $statusColor = 'text-brand-red';
                            elseif ($log->status === 'responding') $statusColor = 'text-brand-blue';
                            elseif ($log->status === 'resolved') $statusColor = 'text-brand-green';
                        @endphp
                        <span class="{{ $statusColor }} font-bold text-[11px] uppercase tracking-wider flex items-center">
                            @if($log->status === 'pending' || $log->status === 'responding')
                            <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5 animate-pulse"></span>
                            @endif
                            {{ $log->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button class="text-brand-blue hover:text-blue-400 bg-brand-blue/10 hover:bg-brand-blue/20 p-2 rounded transition-colors" title="View Details">
                            <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-brand-text">
                        No incident logs found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-brand-border bg-brand-bg/30">
        {{ $logs->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
