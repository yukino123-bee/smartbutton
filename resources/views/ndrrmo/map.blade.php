@extends('layouts.ndrrmo')

@section('content')
<div class="flex flex-col h-full space-y-4">
    {{-- Page Title Header --}}
    <div class="shrink-0">
        <h1 class="text-slate-800 text-xl font-bold">Campus Incident Map</h1>
        <p class="text-slate-500 text-xs mt-0.5">Live geographical overview of all registered ESP32 devices and active emergencies.</p>
    </div>

    {{-- Map & Side Panel Container --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 flex-1 min-h-0">
        {{-- Map Box --}}
        <div class="lg:col-span-3 bg-white border border-slate-200 rounded-2xl overflow-hidden relative shadow-sm h-full flex flex-col min-h-[450px]">
            <div id="campusMap" class="w-full h-full flex-grow z-0"></div>
            
            {{-- Map Legend Overlay --}}
            <div class="absolute bottom-5 right-5 z-[1000] bg-white/95 backdrop-blur-md border border-slate-200 p-4 rounded-xl shadow-xl max-w-xs">
                <h4 class="text-slate-800 text-xs font-bold uppercase tracking-wider mb-2.5 border-b border-slate-100 pb-1.5">Map Legend</h4>
                <div class="space-y-2 text-xs font-semibold text-slate-700">
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-red-600 mr-2.5 shrink-0 ring-2 ring-red-200"></span>
                        <span>Critical Emergency</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-orange-500 mr-2.5 shrink-0 ring-2 ring-orange-200"></span>
                        <span>Public Safety</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-amber-500 mr-2.5 shrink-0 ring-2 ring-amber-200"></span>
                        <span>Facility & Hazard</span>
                    </div>
                    <div class="flex items-center pt-2 border-t border-slate-100">
                        <span class="w-3 h-3 rounded-full bg-blue-600 mr-2.5 shrink-0 ring-2 ring-blue-200"></span>
                        <span>Device Online (Idle)</span>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Active Incidents Panel --}}
        <div class="bg-white border border-slate-200 rounded-2xl flex flex-col h-full overflow-hidden shadow-sm min-h-[300px]">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center shrink-0">
                <h3 class="text-slate-800 font-bold text-sm flex items-center">
                    <svg class="w-4 h-4 mr-2 text-red-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Active Incidents
                </h3>
                <span class="bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $activeIncidents->count() }}</span>
            </div>
            
            <div class="p-4 overflow-y-auto flex-grow space-y-3" id="incidentList">
                @forelse($activeIncidents as $incident)
                    @php
                        if (str_contains($incident->emergency_type, 'Public Safety')) {
                            $cardBg = 'bg-orange-50/60 border-orange-200 text-orange-800';
                            $badgeColor = 'text-orange-700 bg-orange-100';
                        } elseif (str_contains($incident->emergency_type, 'Facility') || str_contains($incident->emergency_type, 'Hazard')) {
                            $cardBg = 'bg-amber-50/60 border-amber-200 text-amber-800';
                            $badgeColor = 'text-amber-700 bg-amber-100';
                        } else {
                            $cardBg = 'bg-red-50/60 border-red-200 text-red-800';
                            $badgeColor = 'text-red-700 bg-red-100';
                        }
                    @endphp
                    <div class="border {{ $cardBg }} rounded-xl p-3 transition-all cursor-pointer hover:shadow-md active:scale-[0.98]" onclick="focusOnDevice('{{ $incident->device->device_code }}')">
                        <div class="flex justify-between items-start mb-1">
                            <div class="font-bold text-slate-800 text-sm">{{ $incident->device->device_code }}</div>
                            <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $badgeColor }}">{{ $incident->status }}</span>
                        </div>
                        <div class="text-xs font-bold mb-1">{{ $incident->emergency_type }}</div>
                        <div class="text-[10px] text-slate-500 font-medium">{{ $incident->device->building }} ({{ $incident->device->floor }}, {{ $incident->device->room }})</div>
                        <div class="text-[10px] text-slate-400 mt-1.5 text-right font-medium">{{ $incident->created_at->diffForHumans() }}</div>
                    </div>
                @empty
                    <div class="text-center py-12 flex flex-col items-center justify-center h-full">
                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-slate-500 text-xs font-medium">No active emergencies on campus.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Include Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
    /* Custom Leaflet Map styling */
    .leaflet-container { background-color: #f8fafc; font-family: inherit; }
    .leaflet-popup-content-wrapper { background-color: #ffffff; color: #1e293b; border: 1px solid #e2e8f0; border-radius: 0.75rem; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); padding: 0; }
    .leaflet-popup-tip { background-color: #ffffff; }
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
    // Initialize Map data
    const devicesData = @json($devices);
    const activeIncidentsData = @json($activeIncidents);
    const mapMarkers = {};
    
    // Default Map Center
    let centerLat = 7.708601;
    let centerLng = 123.292456;
    
    // Auto-center based on first device with coordinates
    const deviceWithCoords = devicesData.find(d => d.latitude && d.longitude);
    if(deviceWithCoords) {
        centerLat = deviceWithCoords.latitude;
        centerLng = deviceWithCoords.longitude;
    }

    const map = L.map('campusMap').setView([centerLat, centerLng], 18);

    // Add Esri World Imagery Satellite tiles
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
        maxZoom: 19
    }).addTo(map);

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

    // Plot Devices on Map
    devicesData.forEach(device => {
        if (!device.latitude || !device.longitude) return;
        
        const incident = activeIncidentsData.find(i => i.device_id === device.id);
        
        let markerIcon;
        let popupTitle = 'Device Status: Normal';
        let popupColor = 'text-blue-600';
        let statusText = 'Online';
        let pulseClass = null;
        let colorClass = 'text-blue-600';
        let svgIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>';
        
        if (incident) {
            statusText = 'EMERGENCY ACTIVE';
            svgIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>';
            if (incident.emergency_type.includes('Public Safety')) {
                pulseClass = 'animate-ping bg-orange-500';
                colorClass = 'text-orange-500';
                popupTitle = 'EMERGENCY: ' + incident.emergency_type;
                popupColor = 'text-orange-600';
            } else if (incident.emergency_type.includes('Facility') || incident.emergency_type.includes('Hazard')) {
                pulseClass = 'animate-ping bg-amber-500';
                colorClass = 'text-amber-500';
                popupTitle = 'EMERGENCY: ' + incident.emergency_type;
                popupColor = 'text-amber-600';
            } else {
                pulseClass = 'animate-ping bg-red-600';
                colorClass = 'text-red-600';
                popupTitle = 'EMERGENCY: ' + incident.emergency_type;
                popupColor = 'text-red-600';
            }
        }

        markerIcon = createMarkerIcon(colorClass, pulseClass);

        const marker = L.marker([device.latitude, device.longitude], {icon: markerIcon}).addTo(map);
        
        const popupContent = `
            <div class="p-4 min-w-[220px]">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 ${popupColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">${svgIcon}</svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm leading-tight">${device.device_code}</h3>
                        <div class="text-[11px] text-slate-500 font-medium">${device.building} (${device.floor}, ${device.room})</div>
                    </div>
                </div>
                <div class="border-t border-slate-100 pt-3">
                    <div class="text-[10px] font-bold uppercase tracking-wider ${popupColor} mb-1">
                        ${popupTitle}
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-500 font-medium">Status</span>
                        <span class="font-bold ${popupColor} flex items-center"><span class="w-1.5 h-1.5 rounded-full ${incident ? 'bg-red-600 animate-pulse' : 'bg-blue-600'} mr-1.5"></span>${statusText}</span>
                    </div>
                </div>
            </div>
        `;
        
        marker.bindPopup(popupContent);
        mapMarkers[device.device_code] = marker;

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
    
    // Fit bounds to show all markers if any exist
    if (Object.keys(mapMarkers).length > 0) {
        const group = new L.featureGroup(Object.values(mapMarkers));
        map.fitBounds(group.getBounds().pad(0.1));
    }

    // Function called when clicking a card in the active incidents panel
    function focusOnDevice(deviceCode) {
        const marker = mapMarkers[deviceCode];
        if (marker) {
            map.flyTo(marker.getLatLng(), 19, {
                animate: true,
                duration: 1
            });
            setTimeout(() => {
                marker.openPopup();
            }, 1000);
        }
    }
</script>
@endsection
