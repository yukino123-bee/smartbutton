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
                    .custom-marker { background: none; border: none; }
                </style>
                
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const map = L.map('campus-map').setView([7.708601, 123.292456], 18);
                        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
                            maxZoom: 19
                        }).addTo(map);

                        window.deviceMarkers = {};

                        const createMarkerIcon = (colorClass, pulseClass) => {
                            return L.divIcon({
                                className: 'custom-marker',
                                html: `
                                    <div class="relative flex items-center justify-center w-8 h-8">
                                        ${pulseClass ? `<span class="absolute w-full h-full rounded-full ${pulseClass} opacity-75"></span>` : ''}
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="relative z-10 w-8 h-8 ${colorClass} drop-shadow-md">
                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                        </svg>
                                    </div>
                                `,
                                iconSize: [32, 32],
                                iconAnchor: [16, 32],
                                popupAnchor: [0, -32]
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
                            <a href="#" class="text-[10px] text-brand-blue hover:text-blue-400">View All</a>
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
                                                <div class="text-brand-dark font-bold text-sm leading-tight mb-1">{{ $incident->device->building ?? 'Unknown Location' }}</div>
                                                <div class="text-brand-text text-[11px] mb-2">{{ $incident->emergency_type }}</div>
                                                <div class="text-[10px] text-slate-500">{{ $incident->created_at->format('M d, g:i A') }} • Device ID: {{ $incident->device->device_code ?? 'N/A' }}</div>
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
                        <a href="#" class="text-[10px] text-brand-blue hover:text-blue-400">View All Logs</a>
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
                                    <th class="px-5 py-3 font-medium">Responded By</th>
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
                                        
                                        if($log->status === 'resolved') {
                                            $statusClass = 'text-brand-green';
                                        }
                                    @endphp
                                    <tr class="border-b border-brand-border/50 hover:bg-slate-50 hover:shadow-sm hover:-translate-y-0.5 transition-all duration-200 cursor-pointer" onclick="window.location.href='{{ route('ndrrmo.logs') }}'">
                                        <td class="px-5 py-3 text-brand-text">{{ $index + 1 }}</td>
                                        <td class="px-5 py-3">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                                        <td class="px-5 py-3"><span class="{{ $tagClass }} text-white text-[9px] font-bold px-2 py-0.5 rounded">{{ $log->emergency_type }}</span></td>
                                        <td class="px-5 py-3 text-brand-text">{{ $log->device->building ?? 'N/A' }}</td>
                                        <td class="px-5 py-3 text-brand-text">{{ $log->device->device_code ?? 'N/A' }}</td>
                                        <td class="px-5 py-3 {{ $statusClass }} font-medium capitalize">{{ $log->status }}</td>
                                        <td class="px-5 py-3 text-brand-text">{{ $log->responded_by ?? '-' }}</td>
                                        <td class="px-5 py-3 text-center"><button class="text-brand-blue hover:text-blue-400"><svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-5 py-8 text-center text-brand-text">No recent incident logs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Footer -->
                    <div class="px-5 py-3 border-t border-brand-border flex items-center justify-between text-xs text-brand-text mt-auto">
                        <div>Showing 1 to {{ $recentLogs->count() }} of {{ $totalIncidents }} entries</div>
                        <div class="flex items-center space-x-1">
                            <button class="w-6 h-6 rounded flex items-center justify-center hover:bg-brand-hover text-brand-text">&lt;</button>
                            <button class="w-6 h-6 rounded flex items-center justify-center bg-brand-blue text-white">1</button>
                            <button class="w-6 h-6 rounded flex items-center justify-center hover:bg-brand-hover">2</button>
                            <button class="w-6 h-6 rounded flex items-center justify-center hover:bg-brand-hover">3</button>
                            <button class="w-6 h-6 rounded flex items-center justify-center hover:bg-brand-hover text-brand-text">&gt;</button>
                        </div>
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
                            $fCount = $stats['Facility & Hazard'] ?? 0;
                            $totalStats = $cCount + $mCount + $pCount + $fCount;
                            $totalStats = $totalStats > 0 ? $totalStats : 1; // avoid division by zero
                            
                            $cPct = ($cCount / $totalStats) * 100;
                            $mPct = ($mCount / $totalStats) * 100;
                            $fPct = ($fCount / $totalStats) * 100;
                            $pPct = ($pCount / $totalStats) * 100;
                            
                            $cEnd = $cPct;
                            $mEnd = $cEnd + $mPct;
                            $fEnd = $mEnd + $fPct;
                        @endphp
                        <!-- Donut Chart -->
                        <div class="relative w-24 h-24 shrink-0">
                            <!-- CSS pure donut chart hack using conic-gradient -->
                            <div class="w-full h-full rounded-full" style="background: conic-gradient(#EF4444 0% {{ $cEnd }}%, #F59E0B {{ $cEnd }}% {{ $mEnd }}%, #10B981 {{ $mEnd }}% {{ $fEnd }}%, #EAB308 {{ $fEnd }}% 100%);"></div>
                            <!-- Inner circle for donut -->
                            <div class="absolute inset-2 bg-brand-card rounded-full flex flex-col items-center justify-center">
                                <span class="text-xl font-bold text-brand-dark leading-none">{{ $totalIncidents }}</span>
                                <span class="text-[10px] text-brand-text mt-1">Total</span>
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
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-brand-green mr-2"></span><span class="text-brand-text">Facility & Hazard</span></div>
                                <div class="text-brand-dark font-medium">{{ $fCount }} ({{ round($fPct, 1) }}%)</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></span><span class="text-brand-text">Public Safety</span></div>
                                <div class="text-brand-dark font-medium">{{ $pCount }} ({{ round($pPct, 1) }}%)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Response Status -->
                    @php
                        $pending = \App\Models\Incident::where('status', 'pending')->count();
                        $responding = \App\Models\Incident::where('status', 'responding')->count();
                        $resolved = \App\Models\Incident::where('status', 'resolved')->count();
                    @endphp
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="text-[10px] font-bold text-brand-text uppercase tracking-wider mb-4">RESPONSE STATUS</h3>
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center text-brand-orange">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span class="text-brand-text text-xs">Pending</span>
                            </div>
                            <span class="text-brand-orange font-bold">{{ $pending }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center text-brand-blue">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span class="text-brand-text text-xs">Responding</span>
                            </div>
                            <span class="text-brand-blue font-bold">{{ $responding }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center text-brand-green">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-brand-text text-xs">Resolved</span>
                            </div>
                            <span class="text-brand-green font-bold">{{ $resolved }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center text-slate-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-brand-text text-xs">Closed</span>
                            </div>
                            <span class="text-brand-dark font-bold">1</span>
                        </div>
                    </div>
                </div>
            </div>
            
@endsection
