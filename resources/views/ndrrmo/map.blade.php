@extends('layouts.ndrrmo')

@section('content')
<div class="mb-4">
    <h1 class="text-brand-dark text-2xl font-bold">Campus Incident Map</h1>
    <p class="text-brand-text text-sm mt-1">Live geographical overview of all registered ESP32 devices and active emergencies.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 h-[calc(100vh-140px)]">
    <!-- Map Container -->
    <div class="lg:col-span-3 bg-brand-card border border-brand-border rounded-xl overflow-hidden relative shadow-2xl h-full flex flex-col">
        <div id="campusMap" class="w-full h-full flex-grow z-0"></div>
        
        <!-- Legend Overlay -->
        <div class="absolute bottom-6 right-6 z-[1000] bg-black/80 backdrop-blur-md border border-brand-border p-4 rounded-xl shadow-lg">
            <h4 class="text-brand-dark text-xs font-bold uppercase tracking-wider mb-3">Map Legend</h4>
            <div class="space-y-2">
                <div class="flex items-center text-xs text-brand-text">
                    <span class="w-3 h-3 rounded-full bg-brand-red mr-2 shadow-[0_0_8px_rgba(239,68,68,0.8)]"></span>
                    Critical Emergency
                </div>
                <div class="flex items-center text-xs text-brand-text">
                    <span class="w-3 h-3 rounded-full bg-orange-500 mr-2 shadow-[0_0_8px_rgba(249,115,22,0.8)]"></span>
                    Public Safety
                </div>
                <div class="flex items-center text-xs text-brand-text">
                    <span class="w-3 h-3 rounded-full bg-yellow-500 mr-2 shadow-[0_0_8px_rgba(234,179,8,0.8)]"></span>
                    Facility & Hazard
                </div>
                <div class="flex items-center text-xs text-brand-text mt-2 pt-2 border-t border-white/10">
                    <span class="w-3 h-3 rounded-full bg-brand-green mr-2 opacity-70"></span>
                    Device Online (Idle)
                </div>
            </div>
        </div>
    </div>
    
    <!-- Active Incidents Panel -->
    <div class="bg-brand-card border border-brand-border rounded-xl flex flex-col h-full overflow-hidden shadow-xl">
        <div class="p-4 border-b border-brand-border bg-black/20 flex justify-between items-center shrink-0">
            <h3 class="text-brand-dark font-bold flex items-center">
                <svg class="w-5 h-5 mr-2 text-brand-red animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Active Incidents
            </h3>
            <span class="bg-brand-red text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $activeIncidents->count() }}</span>
        </div>
        
        <div class="p-4 overflow-y-auto flex-grow space-y-3" id="incidentList">
            @forelse($activeIncidents as $incident)
                @php
                    $borderColor = 'border-brand-red/50';
                    $textColor = 'text-brand-red';
                    $bgColor = 'bg-brand-red/10';
                    
                    if (str_contains($incident->emergency_type, 'Public Safety')) {
                        $borderColor = 'border-orange-500/50';
                        $textColor = 'text-orange-500';
                        $bgColor = 'bg-orange-500/10';
                    } elseif (str_contains($incident->emergency_type, 'Facility') || str_contains($incident->emergency_type, 'Hazard')) {
                        $borderColor = 'border-yellow-500/50';
                        $textColor = 'text-yellow-500';
                        $bgColor = 'bg-yellow-500/10';
                    }
                @endphp
                <div class="border {{ $borderColor }} {{ $bgColor }} rounded-lg p-3 transition-colors cursor-pointer hover:bg-black/40" onclick="focusOnDevice('{{ $incident->device->device_code }}')">
                    <div class="flex justify-between items-start mb-1">
                        <div class="font-bold text-brand-dark text-sm">{{ $incident->device->device_code }}</div>
                        <span class="text-[9px] font-bold uppercase tracking-wider {{ $textColor }}">{{ $incident->status }}</span>
                    </div>
                    <div class="text-xs {{ $textColor }} font-medium mb-1">{{ $incident->emergency_type }}</div>
                    <div class="text-[10px] text-brand-text">{{ $incident->device->building }} ({{ $incident->device->floor }}, {{ $incident->device->room }})</div>
                    <div class="text-[10px] text-brand-text mt-1 text-right">{{ $incident->created_at->diffForHumans() }}</div>
                </div>
            @empty
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-brand-text mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-brand-text text-sm">No active emergencies on campus.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Include Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
    /* Custom Leaflet Map styling */
    .leaflet-container { background-color: #0f1011; }
    .leaflet-popup-content-wrapper { background-color: #ffffff; color: #334155; border: 1px solid #e2e8f0; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); padding: 0; }
    .leaflet-popup-tip { background-color: #ffffff; border-bottom: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; }
    .leaflet-popup-content { margin: 0; }
    .custom-marker { background: none; border: none; }
</style>

<script>
    // Initialize Map data
    const devicesData = @json($devices);
    const activeIncidentsData = @json($activeIncidents);
    const mapMarkers = {};
    
    // Default Map Center (adjust to specific campus coords, e.g., somewhere in the Philippines)
    // For now, defaulting to Manila area, but it will automatically center if devices exist with coords.
    let centerLat = 7.708601;
    let centerLng = 123.292456;
    
    // Auto-center based on first device with coordinates
    const deviceWithCoords = devicesData.find(d => d.latitude && d.longitude);
    if(deviceWithCoords) {
        centerLat = deviceWithCoords.latitude;
        centerLng = deviceWithCoords.longitude;
    }

    const map = L.map('campusMap').setView([centerLat, centerLng], 18); // Zoom 18 for campus scale

    // Add Esri World Imagery Satellite tiles
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
        maxZoom: 19
    }).addTo(map);

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

    // Plot Devices on Map
    devicesData.forEach(device => {
        if (!device.latitude || !device.longitude) return; // Skip if no coords
        
        // Check if device has active incident
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
            if (incident.emergency_type.includes('Public Safety')) {
                pulseClass = 'animate-ping bg-brand-orange';
                colorClass = 'text-brand-orange';
                popupTitle = 'EMERGENCY: ' + incident.emergency_type;
                popupColor = 'text-brand-orange';
            } else if (incident.emergency_type.includes('Facility') || incident.emergency_type.includes('Hazard')) {
                pulseClass = 'animate-ping bg-yellow-500';
                colorClass = 'text-yellow-500';
                popupTitle = 'EMERGENCY: ' + incident.emergency_type;
                popupColor = 'text-yellow-500';
            } else {
                // Critical
                pulseClass = 'animate-ping bg-brand-red';
                colorClass = 'text-brand-red';
                popupTitle = 'EMERGENCY: ' + incident.emergency_type;
                popupColor = 'text-brand-red';
            }
        }

        markerIcon = createMarkerIcon(colorClass, pulseClass);

        const marker = L.marker([device.latitude, device.longitude], {icon: markerIcon}).addTo(map);
        
        const popupContent = `
            <div class="p-3 min-w-[200px]">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 ${popupColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">${svgIcon}</svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-brand-dark text-sm leading-tight">${device.device_code}</h3>
                        <div class="text-[10px] text-slate-500 font-medium">${device.building} (${device.floor}, ${device.room})</div>
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
        mapMarkers[device.device_code] = marker;
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
