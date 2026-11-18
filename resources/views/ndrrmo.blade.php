@extends('layouts.ndrrmo')

@section('content')
            
            <!-- Top Stats Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
                <x-kpi-card label="Active alerts" tone="red" :value="$activeIncidents->count()" value-id="ndrrmo-stat-active" hint="Require immediate attention" :urgent="$activeIncidents->count() > 0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg></x-kpi-card>
                <x-kpi-card label="Total incidents" tone="amber" :value="$totalIncidents" value-id="ndrrmo-stat-total" hint="All recorded incidents"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg></x-kpi-card>
                <x-kpi-card label="Resolved" tone="green" :value="$resolvedIncidents" value-id="ndrrmo-stat-resolved" hint="Closed incidents"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></x-kpi-card>
                <x-kpi-card label="Devices online" tone="blue" :value="($onlineDevicesCount ?? 0).' / '.($devicesCount ?? 0)" value-id="ndrrmo-stat-devices" hint-id="ndrrmo-stat-devices-subtitle" :hint="($onlineDevicesCount ?? 0) > 0 ? 'Devices operational' : 'No devices online'"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg></x-kpi-card>
            </div>
            @include('partials.dashboard-map-alerts', ['mapRoute' => route('ndrrmo.map'), 'alertsRoute' => route('ndrrmo.alerts'), 'statsUrl' => '/ndrrmo/stats-json'])

            <!-- Bottom Row: Table and Summary -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Incident Logs -->
                <div class="lg:col-span-2 panel flex flex-col">
                    <div class="panel-head">
                        <h2 class="panel-title">Recent Incident Logs</h2>
                        <a href="{{ route('ndrrmo.logs') }}" class="panel-link">View All Logs</a>
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
                                        
                                        if(str_contains($log->emergency_type, 'Medical')) {
                                            $tagClass = 'bg-brand-orange';
                                        } elseif(str_contains($log->emergency_type, 'Safety')) {
                                            $tagClass = 'bg-blue-600';
                                        }
                                        
                                        if($log->status === 'Resolved') {
                                            $statusClass = 'text-brand-green';
                                        }
                                    @endphp
                                    <tr class="border-b border-brand-border/50 hover:bg-slate-50 hover:shadow-sm hover:-translate-y-0.5 transition-all duration-200 cursor-pointer" onclick="window.location.href='{{ route('ndrrmo.logs') }}'">
                                        <td class="px-5 py-3 text-brand-text">{{ $index + 1 }}</td>
                                        <td class="px-5 py-3">{{ $log->reported_at->format('M d, Y h:i A') }}</td>
                                        <td class="px-5 py-3"><span class="{{ $tagClass }} text-white text-[10px] font-semibold px-2.5 py-1 rounded-full whitespace-nowrap">{{ $log->emergency_type }}</span></td>
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
                <div class="panel flex flex-col">
                    <div class="panel-head">
                        <h2 class="panel-title">Incident Summary (Today)</h2>
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
                                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-blue-600 mr-2"></span><span class="text-brand-text">Public Safety</span></div>
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
