@extends('layouts.ndrrmo')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-xl font-bold text-brand-dark mb-1">SMS Logs</h2>
        <p class="text-xs text-brand-text">View all automated SMS alerts sent by the system during emergencies.</p>
    </div>
    <div class="flex items-center space-x-3 text-xs">
        <div class="flex items-center text-brand-text bg-brand-card border border-brand-border px-3 py-2 rounded-lg">
            <span class="w-2 h-2 rounded-full bg-brand-green mr-2 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></span>
            GSM Module Status: <span class="text-brand-dark font-medium ml-1">Online</span>
        </div>
    </div>
</div>

<div class="bg-brand-card border border-brand-border rounded-xl flex flex-col overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-brand-bg/50 border-b border-brand-border text-[10px] text-brand-text uppercase tracking-wider">
                    <th class="px-6 py-4 font-bold">Timestamp</th>
                    <th class="px-6 py-4 font-bold">Recipient</th>
                    <th class="px-6 py-4 font-bold">Message Content</th>
                    <th class="px-6 py-4 font-bold">Related Incident</th>
                    <th class="px-6 py-4 font-bold">Delivery Status</th>
                </tr>
            </thead>
            <tbody class="text-xs text-brand-dark">
                @forelse($smsLogs as $sms)
                <tr class="border-b border-brand-border/50 hover:bg-brand-hover transition-colors group">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-brand-dark font-medium">{{ $sms->created_at->format('M d, Y') }}</div>
                        <div class="text-[10px] text-slate-500">{{ $sms->created_at->format('h:i:s A') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-brand-blue/20 text-brand-blue flex items-center justify-center mr-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <div class="text-brand-dark font-medium">{{ $sms->recipient_role ?? 'DRRMO Staff' }}</div>
                                <div class="text-[10px] font-mono text-brand-text">{{ $sms->recipient ?? 'Primary Number' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 max-w-md">
                        <div class="bg-brand-bg/80 border border-brand-border rounded p-3 text-[11px] font-mono text-brand-text leading-relaxed relative">
                            <!-- Tail -->
                            <div class="absolute -left-1.5 top-3 w-3 h-3 bg-brand-bg/80 border-l border-t border-brand-border transform -rotate-45"></div>
                            <span class="relative z-10">{{ $sms->message }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($sms->incident)
                        <div class="text-[10px] text-brand-text uppercase font-bold tracking-wider mb-0.5">LOCATION</div>
                        <div class="text-brand-dark font-medium text-xs">{{ $sms->incident->device->building ?? 'Unknown' }}</div>
                        <div class="text-[10px] text-slate-500 mt-0.5">{{ $sms->incident->emergency_type }}</div>
                        @else
                        <span class="text-slate-500 italic">N/A</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($sms->status === 'delivered' || $sms->status === 'sent')
                        <span class="text-brand-green bg-brand-green/10 border border-brand-green/20 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider flex items-center w-max">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Sent
                        </span>
                        @else
                        <span class="text-brand-red bg-brand-red/10 border border-brand-red/20 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider flex items-center w-max">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Failed
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-brand-bg mb-4">
                            <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </div>
                        <div class="text-brand-dark font-medium text-lg mb-1">No SMS Logs</div>
                        <div class="text-brand-text text-sm">Automated SMS alerts will appear here when dispatched.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($smsLogs->hasPages())
    <div class="px-6 py-4 border-t border-brand-border bg-brand-bg/30">
        {{ $smsLogs->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
