

            <!-- Middle Row: Map and Alerts/Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Map Section -->
                <div class="lg:col-span-2 panel flex flex-col overflow-hidden">
                    <div class="panel-head">
                        <h2 class="panel-title">
                            <svg class="w-4 h-4 mr-2 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                            Campus Incident Map
                        </h2>
                        @if($mapRoute ?? null)<a href="{{ $mapRoute }}" class="panel-link">Full Map</a>@endif
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
                    .leaflet-container { background-color: #e2e8f0; }
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
                            maxNativeZoom: 17,
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

                        const iconNormal = createMarkerIcon('text-emerald-600', null);
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
                            let colorClass = 'text-emerald-600';
                            let svgIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>';
                            
                            if (incident) {
                                statusText = 'EMERGENCY ACTIVE';
                                svgIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>';
                                if (incident.emergency_type && incident.emergency_type.includes('Public Safety')) {
                                    pulseClass = 'animate-ping bg-blue-600';
                                    colorClass = 'text-blue-600';
                                    popupTitle = 'EMERGENCY: ' + incident.emergency_type;
                                    popupColor = 'text-blue-600';
                                } else if (incident.emergency_type && incident.emergency_type.includes('Medical')) {
                                    pulseClass = 'animate-ping bg-brand-orange';
                                    colorClass = 'text-brand-orange';
                                    popupTitle = 'EMERGENCY: ' + incident.emergency_type;
                                    popupColor = 'text-brand-orange';
                                } else {
                                    pulseClass = 'animate-ping bg-brand-red';
                                    colorClass = 'text-brand-red';
                                    popupTitle = 'EMERGENCY: ' + (incident.emergency_type || 'General');
                                    popupColor = 'text-brand-red';
                                }
                            }

                            markerIcon = createMarkerIcon(colorClass, pulseClass);
                            const marker = L.marker([device.latitude, device.longitude], { icon: markerIcon }).addTo(map);
                            
                            // Idle devices show their real link state; an active alert always wins
                            const render = (online) => {
                            const down = !incident && !online;
                            const color = down ? 'text-slate-500' : popupColor;
                            return `
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
                                        <div class="text-[10px] font-bold uppercase tracking-wider ${color} mb-1">
                                            ${down ? 'Device Status: Offline' : popupTitle}
                                        </div>
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-slate-500 font-medium">Status</span>
                                            <span class="font-bold ${color} flex items-center"><span class="w-1.5 h-1.5 rounded-full ${incident ? 'bg-brand-red animate-pulse' : (down ? 'bg-slate-400' : 'bg-brand-green')} mr-1.5"></span>${down ? 'Offline' : statusText}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                            };
                            marker.bindPopup(render(device.is_online));
                            // Refresh the link state each time the popup opens, so it never shows a stale page-load value
                            const syncPopup = () => fetch('{{ $statsUrl }}').then(r => r.json())
                                .then(d => marker.setPopupContent(render(d.device_online[device.id]))).catch(() => {});
                            let popupTimer;
                            marker.on('popupopen', () => { syncPopup(); popupTimer = setInterval(syncPopup, 3000); });
                            marker.on('popupclose', () => clearInterval(popupTimer));
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
                    <div class="panel flex flex-col h-[280px]">
                        <div class="panel-head">
                            <h2 class="panel-title">Active Alerts</h2>
                            <a href="{{ $alertsRoute }}" class="panel-link">View All</a>
                        </div>
                        <div id="active-alerts-list" class="flex-1 p-4 flex flex-col gap-3 overflow-y-auto custom-scrollbar">
                            @forelse($activeIncidents as $incident)
                                @php
                                    $borderColor = 'border-brand-red/30';
                                    $bgColor = 'bg-brand-red/5';
                                    $stripeColor = 'bg-brand-red';
                                    $textColor = 'text-brand-red';
                                    $tagClass = 'bg-brand-red';
                                    if(str_contains($incident->emergency_type, 'Medical')) {
                                        $borderColor = 'border-brand-orange/30';
                                        $bgColor = 'bg-brand-orange/5';
                                        $stripeColor = 'bg-brand-orange';
                                        $textColor = 'text-brand-orange';
                                        $tagClass = 'bg-brand-orange';
                                    } elseif(str_contains($incident->emergency_type, 'Safety')) {
                                        $borderColor = 'border-blue-600/30';
                                        $bgColor = 'bg-blue-600/5';
                                        $stripeColor = 'bg-blue-600';
                                        $textColor = 'text-blue-600';
                                        $tagClass = 'bg-blue-600';
                                    }
                                @endphp
                                <div onclick="window.location.href='{{ $alertsRoute }}'" class="border {{ $borderColor }} {{ $bgColor }} rounded-lg p-3 relative overflow-hidden group cursor-pointer">
                                    <div class="absolute left-0 top-0 bottom-0 w-1 {{ $stripeColor }}"></div>
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start min-w-0">
                                            <div class="mt-1 mr-3 {{ $textColor }} animate-pulse">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-brand-dark font-bold text-sm leading-tight mb-1">{{ $incident->device?->building ?? 'Location not recorded' }}</div>
                                                <div class="text-brand-text text-[11px] mb-1">{{ $incident->emergency_type }}</div>
                                                <div class="text-[10px] text-slate-500">{{ $incident->reported_at->format('M d, g:i A') }} • Device ID: {{ $incident->device?->device_code ?? 'Not recorded' }}</div>
                                            </div>
                                        </div>
                                        <span class="{{ $textColor }} text-[10px] font-bold uppercase tracking-wider shrink-0">{{ $incident->status }}</span>
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
