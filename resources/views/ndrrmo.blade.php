@extends('layouts.ndrrmo')

@section('content')
            
            <!-- Top Stats Row -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Stat Card 1 -->
                <div class="bg-brand-card border border-brand-border rounded-xl p-4 flex items-center hover:shadow-lg hover:border-brand-blue/30 transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                    <div class="w-12 h-12 rounded-lg bg-brand-red/20 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-brand-text uppercase tracking-wider mb-1">ACTIVE ALERTS</div>
                        <div class="text-2xl font-bold text-brand-dark leading-none mb-1">2</div>
                        <div class="text-[10px] text-brand-text">Require immediate attention</div>
                    </div>
                </div>
                <!-- Stat Card 2 -->
                <div class="bg-brand-card border border-brand-border rounded-xl p-4 flex items-center hover:shadow-lg hover:border-brand-blue/30 transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                    <div class="w-12 h-12 rounded-lg bg-brand-orange/20 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-brand-text uppercase tracking-wider mb-1">TOTAL INCIDENTS</div>
                        <div class="text-2xl font-bold text-brand-dark leading-none mb-1">15</div>
                        <div class="text-[10px] text-brand-text">This month</div>
                    </div>
                </div>
                <!-- Stat Card 3 -->
                <div class="bg-brand-card border border-brand-border rounded-xl p-4 flex items-center hover:shadow-lg hover:border-brand-blue/30 transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                    <div class="w-12 h-12 rounded-lg bg-brand-green/20 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-brand-text uppercase tracking-wider mb-1">RESOLVED INCIDENTS</div>
                        <div class="text-2xl font-bold text-brand-dark leading-none mb-1">12</div>
                        <div class="text-[10px] text-brand-text">This month</div>
                    </div>
                </div>
                <!-- Stat Card 4 -->
                <div class="bg-brand-card border border-brand-border rounded-xl p-4 flex items-center hover:shadow-lg hover:border-brand-blue/30 transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                    <div class="w-12 h-12 rounded-lg bg-brand-blue/20 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-brand-text uppercase tracking-wider mb-1">DEVICES ONLINE</div>
                        <div class="text-2xl font-bold text-brand-dark leading-none mb-1">3 / 3</div>
                        <div class="text-[10px] text-brand-text">All devices operational</div>
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

                        // Mock Seeded Devices
                        const devices = [
                            { id: 'GYM-001', name: 'Gymnasium', lat: 7.7115556, lng: 123.2931667, status: 'normal' },
                            { id: 'ENG-001', name: 'Engineering', lat: 7.710675, lng: 123.291948, status: 'normal' },
                            { id: 'LIB-001', name: 'Library', lat: 7.708561, lng: 123.292544, status: 'normal' }
                        ];

                        devices.forEach(device => {
                            const marker = L.marker([device.lat, device.lng], { icon: iconNormal }).addTo(map);
                            
                            const popupContent = `
                                <div class="p-3 min-w-[200px]">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-10 h-10 rounded-full bg-brand-green/10 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-brand-dark text-sm leading-tight">${device.name}</h3>
                                            <div class="text-[10px] text-slate-500 font-medium">Device ID: ${device.id}</div>
                                        </div>
                                    </div>
                                    <div class="border-t border-slate-100 pt-3">
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-slate-500 font-medium">Status</span>
                                            <span class="font-bold text-brand-green flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-brand-green mr-1.5"></span>Online</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            marker.bindPopup(popupContent);
                            window.deviceMarkers[device.id] = marker;
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
                            <!-- Alert 1 -->
                            <div class="border border-brand-red/30 bg-brand-red/5 rounded-lg p-3 relative overflow-hidden group">
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-brand-red"></div>
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start">
                                        <div class="mt-1 mr-3 text-brand-red animate-pulse">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/></svg>
                                        </div>
                                        <div>
                                            <div class="text-brand-dark font-bold text-sm leading-tight mb-1">Gymnasium</div>
                                            <div class="text-brand-text text-[11px] mb-2">General Emergency</div>
                                            <div class="text-[10px] text-slate-500">Today, 10:28 AM • Device ID: GYM-001</div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <span class="bg-brand-red text-white text-[8px] font-bold px-2 py-0.5 rounded uppercase tracking-wider mb-2">CRITICAL EMERGENCY</span>
                                        <span class="text-brand-red text-[10px] font-bold uppercase tracking-wider mb-2">ACTIVE</span>
                                        <button class="border border-brand-border text-brand-text text-[10px] hover:text-brand-dark hover:bg-brand-hover px-2 py-1 rounded transition-colors">View Details</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Alert 2 -->
                            <div class="border border-brand-orange/30 bg-brand-orange/5 rounded-lg p-3 relative overflow-hidden">
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-brand-orange"></div>
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start">
                                        <div class="mt-1 mr-3 text-brand-orange">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/></svg>
                                        </div>
                                        <div>
                                            <div class="text-brand-dark font-bold text-sm leading-tight mb-1">Engineering Building</div>
                                            <div class="text-brand-text text-[11px] mb-2">Medical Emergency</div>
                                            <div class="text-[10px] text-slate-500">Today, 10:15 AM • Device ID: ENG-001</div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <span class="bg-brand-orange text-white text-[8px] font-bold px-2 py-0.5 rounded uppercase tracking-wider mb-2">MEDICAL EMERGENCY</span>
                                        <span class="text-brand-orange text-[10px] font-bold uppercase tracking-wider mb-2">ACTIVE</span>
                                        <button class="border border-brand-border text-brand-text text-[10px] hover:text-brand-dark hover:bg-brand-hover px-2 py-1 rounded transition-colors">View Details</button>
                                    </div>
                                </div>
                            </div>
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
                                <tr class="border-b border-brand-border/50 hover:bg-slate-50 hover:shadow-sm hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                                    <td class="px-5 py-3 text-brand-text">1</td>
                                    <td class="px-5 py-3">May 15, 2025 10:28 AM</td>
                                    <td class="px-5 py-3"><span class="bg-brand-red text-white text-[9px] font-bold px-2 py-0.5 rounded">General Emergency</span></td>
                                    <td class="px-5 py-3 text-brand-text">Gymnasium</td>
                                    <td class="px-5 py-3 text-brand-text">GYM-001</td>
                                    <td class="px-5 py-3 text-brand-red font-medium">Active</td>
                                    <td class="px-5 py-3 text-brand-text">-</td>
                                    <td class="px-5 py-3 text-center"><button class="text-brand-blue hover:text-blue-400"><svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button></td>
                                </tr>
                                <tr class="border-b border-brand-border/50 hover:bg-slate-50 hover:shadow-sm hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                                    <td class="px-5 py-3 text-brand-text">2</td>
                                    <td class="px-5 py-3">May 15, 2025 10:15 AM</td>
                                    <td class="px-5 py-3"><span class="bg-brand-orange text-white text-[9px] font-bold px-2 py-0.5 rounded">Medical Emergency</span></td>
                                    <td class="px-5 py-3 text-brand-text">Engineering Building</td>
                                    <td class="px-5 py-3 text-brand-text">ENG-001</td>
                                    <td class="px-5 py-3 text-brand-red font-medium">Active</td>
                                    <td class="px-5 py-3 text-brand-text">-</td>
                                    <td class="px-5 py-3 text-center"><button class="text-brand-blue hover:text-blue-400"><svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button></td>
                                </tr>
                                <tr class="border-b border-brand-border/50 hover:bg-slate-50 hover:shadow-sm hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                                    <td class="px-5 py-3 text-brand-text">3</td>
                                    <td class="px-5 py-3">May 15, 2025 09:42 AM</td>
                                    <td class="px-5 py-3"><span class="bg-brand-red text-white text-[9px] font-bold px-2 py-0.5 rounded">General Emergency</span></td>
                                    <td class="px-5 py-3 text-brand-text">Library</td>
                                    <td class="px-5 py-3 text-brand-text">LIB-001</td>
                                    <td class="px-5 py-3 text-brand-green font-medium">Resolved</td>
                                    <td class="px-5 py-3 text-brand-text">NDRRMO Staff 1</td>
                                    <td class="px-5 py-3 text-center"><button class="text-brand-blue hover:text-blue-400"><svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button></td>
                                </tr>
                                <tr class="border-b border-brand-border/50 hover:bg-slate-50 hover:shadow-sm hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                                    <td class="px-5 py-3 text-brand-text">4</td>
                                    <td class="px-5 py-3">May 15, 2025 08:31 AM</td>
                                    <td class="px-5 py-3"><span class="bg-brand-green text-white text-[9px] font-bold px-2 py-0.5 rounded">Medical Emergency</span></td>
                                    <td class="px-5 py-3 text-brand-text">Engineering Building</td>
                                    <td class="px-5 py-3 text-brand-text">ENG-001</td>
                                    <td class="px-5 py-3 text-brand-green font-medium">Resolved</td>
                                    <td class="px-5 py-3 text-brand-text">NDRRMO Staff 2</td>
                                    <td class="px-5 py-3 text-center"><button class="text-brand-blue hover:text-blue-400"><svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button></td>
                                </tr>
                                <tr class="hover:bg-slate-50 hover:shadow-sm hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                                    <td class="px-5 py-3 text-brand-text">5</td>
                                    <td class="px-5 py-3">May 15, 2025 07:54 AM</td>
                                    <td class="px-5 py-3"><span class="bg-brand-red text-white text-[9px] font-bold px-2 py-0.5 rounded">Security Threat</span></td>
                                    <td class="px-5 py-3 text-brand-text">Gymnasium</td>
                                    <td class="px-5 py-3 text-brand-text">GYM-001</td>
                                    <td class="px-5 py-3 text-brand-green font-medium">Resolved</td>
                                    <td class="px-5 py-3 text-brand-text">NDRRMO Staff 1</td>
                                    <td class="px-5 py-3 text-center"><button class="text-brand-blue hover:text-blue-400"><svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Footer -->
                    <div class="px-5 py-3 border-t border-brand-border flex items-center justify-between text-xs text-brand-text mt-auto">
                        <div>Showing 1 to 5 of 15 entries</div>
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
                        <!-- Donut Chart -->
                        <div class="relative w-24 h-24 shrink-0">
                            <!-- CSS pure donut chart hack using conic-gradient -->
                            <div class="w-full h-full rounded-full" style="background: conic-gradient(#EF4444 0% 25%, #F59E0B 25% 62.5%, #10B981 62.5% 87.5%, #EAB308 87.5% 100%);"></div>
                            <!-- Inner circle for donut -->
                            <div class="absolute inset-2 bg-brand-card rounded-full flex flex-col items-center justify-center">
                                <span class="text-xl font-bold text-brand-dark leading-none">8</span>
                                <span class="text-[10px] text-brand-text mt-1">Total</span>
                            </div>
                        </div>
                        
                        <!-- Legend -->
                        <div class="ml-4 flex-1 text-[11px]">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-brand-red mr-2"></span><span class="text-brand-text">Critical Emergency</span></div>
                                <div class="text-brand-dark font-medium">2 (25%)</div>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-brand-orange mr-2"></span><span class="text-brand-text">Medical Emergency</span></div>
                                <div class="text-brand-dark font-medium">3 (37.5%)</div>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></span><span class="text-brand-text">Public Safety Emergency</span></div>
                                <div class="text-brand-dark font-medium">1 (12.5%)</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-brand-green mr-2"></span><span class="text-brand-text">Facility & Hazard Emergency</span></div>
                                <div class="text-brand-dark font-medium">2 (25%)</div>
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
                            <span class="text-brand-orange font-bold">2</span>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center text-brand-blue">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span class="text-brand-text text-xs">Responding</span>
                            </div>
                            <span class="text-brand-blue font-bold">2</span>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center text-brand-green">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-brand-text text-xs">Resolved</span>
                            </div>
                            <span class="text-brand-green font-bold">3</span>
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
