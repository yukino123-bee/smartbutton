@extends('layouts.ndrrmo')

@section('content')
<div class="mb-4">
    <h1 class="text-white text-2xl font-bold">Campus Incident Map</h1>
    <p class="text-brand-text text-sm mt-1">Live geographical overview of all registered ESP32 devices and active emergencies.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 h-[calc(100vh-140px)]">
    <!-- Map Container -->
    <div class="lg:col-span-3 bg-brand-card border border-brand-border rounded-xl overflow-hidden relative shadow-2xl h-full flex flex-col">
        <div id="campusMap" class="w-full h-full flex-grow z-0"></div>
        
        <!-- Legend Overlay -->
        <div class="absolute bottom-6 right-6 z-[1000] bg-black/80 backdrop-blur-md border border-brand-border p-4 rounded-xl shadow-lg">
            <h4 class="text-white text-xs font-bold uppercase tracking-wider mb-3">Map Legend</h4>
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
            <h3 class="text-white font-bold flex items-center">
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
                        <div class="font-bold text-white text-sm">{{ $incident->device->device_code }}</div>
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
    /* Custom Leaflet Map styling for Dark Mode */
    .leaflet-container {
        background-color: #0f1011;
    }
    .leaflet-popup-content-wrapper, .leaflet-popup-tip {
        background-color: #1a1a1a;
        color: #fff;
        border: 1px solid #333;
    }
    .pulse-marker-critical {
        background-color: #ef4444;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
        animation: pulse-red 1.5s infinite;
        border: 2px solid white;
    }
    .pulse-marker-safety {
        background-color: #f97316;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.7);
        animation: pulse-orange 1.5s infinite;
        border: 2px solid white;
    }
    .pulse-marker-hazard {
        background-color: #eab308;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(234, 179, 8, 0.7);
        animation: pulse-yellow 1.5s infinite;
        border: 2px solid white;
    }
    .marker-idle {
        background-color: #10b981;
        border-radius: 50%;
        border: 2px solid white;
        opacity: 0.8;
    }
    
    @keyframes pulse-red {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
    @keyframes pulse-orange {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(249, 115, 22, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(249, 115, 22, 0); }
    }
    @keyframes pulse-yellow {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(234, 179, 8, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(234, 179, 8, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(234, 179, 8, 0); }
    }
</style>

<script>
    // Initialize Map data
    const devicesData = @json($devices);
    const activeIncidentsData = @json($activeIncidents);
    const mapMarkers = {};
    
    // Default Map Center (adjust to specific campus coords, e.g., somewhere in the Philippines)
    // For now, defaulting to Manila area, but it will automatically center if devices exist with coords.
    let centerLat = 14.5995;
    let centerLng = 120.9842;
    
    // Auto-center based on first device with coordinates
    const deviceWithCoords = devicesData.find(d => d.latitude && d.longitude);
    if(deviceWithCoords) {
        centerLat = deviceWithCoords.latitude;
        centerLng = deviceWithCoords.longitude;
    }

    const map = L.map('campusMap').setView([centerLat, centerLng], 18); // Zoom 18 for campus scale

    // Add Dark Matter CartoDB tiles for the dark mode aesthetic
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    // Plot Devices on Map
    devicesData.forEach(device => {
        if (!device.latitude || !device.longitude) return; // Skip if no coords
        
        // Check if device has active incident
        const incident = activeIncidentsData.find(i => i.device_id === device.id);
        
        let markerClass = 'marker-idle';
        let popupTitle = 'Device Status: Normal';
        let popupColor = 'text-brand-green';
        
        if (incident) {
            if (incident.emergency_type.includes('Public Safety')) {
                markerClass = 'pulse-marker-safety';
                popupTitle = 'EMERGENCY: ' + incident.emergency_type;
                popupColor = 'text-orange-500';
            } else if (incident.emergency_type.includes('Facility') || incident.emergency_type.includes('Hazard')) {
                markerClass = 'pulse-marker-hazard';
                popupTitle = 'EMERGENCY: ' + incident.emergency_type;
                popupColor = 'text-yellow-500';
            } else {
                // Critical
                markerClass = 'pulse-marker-critical';
                popupTitle = 'EMERGENCY: ' + incident.emergency_type;
                popupColor = 'text-brand-red';
            }
        }

        const customIcon = L.divIcon({
            className: markerClass,
            iconSize: incident ? [24, 24] : [16, 16],
            iconAnchor: incident ? [12, 12] : [8, 8]
        });

        const marker = L.marker([device.latitude, device.longitude], {icon: customIcon}).addTo(map);
        
        const popupContent = `
            <div class="p-1">
                <h3 class="font-bold text-white mb-1">${device.device_code}</h3>
                <div class="text-xs mb-2">${device.building} (${device.floor}, ${device.room})</div>
                <div class="text-[10px] font-bold uppercase tracking-wider ${popupColor} border-t border-gray-700 pt-2">
                    ${popupTitle}
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
