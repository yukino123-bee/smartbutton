@extends('layouts.ndrrmo')

@section('content')
            
            <!-- Top Stats Row -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Stat Card 1 -->
                <div class="bg-brand-card border border-brand-border rounded-xl p-4 flex items-center">
                    <div class="w-12 h-12 rounded-lg bg-brand-red/20 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-brand-text uppercase tracking-wider mb-1">ACTIVE ALERTS</div>
                        <div class="text-2xl font-bold text-white leading-none mb-1">2</div>
                        <div class="text-[10px] text-brand-text">Require immediate attention</div>
                    </div>
                </div>
                <!-- Stat Card 2 -->
                <div class="bg-brand-card border border-brand-border rounded-xl p-4 flex items-center">
                    <div class="w-12 h-12 rounded-lg bg-brand-orange/20 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-brand-text uppercase tracking-wider mb-1">TOTAL INCIDENTS</div>
                        <div class="text-2xl font-bold text-white leading-none mb-1">15</div>
                        <div class="text-[10px] text-brand-text">This month</div>
                    </div>
                </div>
                <!-- Stat Card 3 -->
                <div class="bg-brand-card border border-brand-border rounded-xl p-4 flex items-center">
                    <div class="w-12 h-12 rounded-lg bg-brand-green/20 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-brand-text uppercase tracking-wider mb-1">RESOLVED INCIDENTS</div>
                        <div class="text-2xl font-bold text-white leading-none mb-1">12</div>
                        <div class="text-[10px] text-brand-text">This month</div>
                    </div>
                </div>
                <!-- Stat Card 4 -->
                <div class="bg-brand-card border border-brand-border rounded-xl p-4 flex items-center">
                    <div class="w-12 h-12 rounded-lg bg-brand-blue/20 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-brand-text uppercase tracking-wider mb-1">DEVICES ONLINE</div>
                        <div class="text-2xl font-bold text-white leading-none mb-1">3 / 3</div>
                        <div class="text-[10px] text-brand-text">All devices operational</div>
                    </div>
                </div>
            </div>

            <!-- Middle Row: Map and Alerts/Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Map Section -->
                <div class="lg:col-span-2 bg-brand-card border border-brand-border rounded-xl flex flex-col overflow-hidden">
                    <div class="px-5 py-4 border-b border-brand-border">
                        <h2 class="text-xs font-bold text-white uppercase tracking-wider">CAMPUS INCIDENT MAP</h2>
                    </div>
                    <div id="campus-map" class="relative flex-1 min-h-[360px] bg-[#C5E1A5] z-0">
                        <!-- Leaflet Map will be injected here -->
                    </div>
                </div>

                <!-- Leaflet CSS & JS -->
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Initialize Map
                        const map = L.map('campus-map').setView([8.1234567, 123.1234567], 18);
                        
                        // Add OpenStreetMap tiles
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors'
                        }).addTo(map);

                        // Store markers to update them later
                        window.deviceMarkers = {};

                        // Define Marker Icons
                        const iconNormal = L.divIcon({ className: 'custom-div-icon', html: "<div class='w-4 h-4 bg-brand-green rounded-full border-2 border-white shadow-[0_0_10px_rgba(16,185,129,0.8)] animate-pulse'></div>", iconSize: [16, 16], iconAnchor: [8, 8] });
                        const iconCritical = L.divIcon({ className: 'custom-div-icon', html: "<div class='w-4 h-4 bg-brand-red rounded-full border-2 border-white shadow-[0_0_15px_rgba(239,68,68,1)] animate-ping'></div>", iconSize: [16, 16], iconAnchor: [8, 8] });
                        const iconWarning = L.divIcon({ className: 'custom-div-icon', html: "<div class='w-4 h-4 bg-brand-orange rounded-full border-2 border-white shadow-[0_0_10px_rgba(245,158,11,0.8)] animate-pulse'></div>", iconSize: [16, 16], iconAnchor: [8, 8] });

                        // Mock Seeded Devices
                        const devices = [
                            { id: 'GYM-001', name: 'Gymnasium', lat: 8.1234567, lng: 123.1234567, status: 'normal' },
                            { id: 'ENG-001', name: 'Engineering', lat: 8.1235567, lng: 123.1236567, status: 'normal' },
                            { id: 'LIB-001', name: 'Library', lat: 8.1233567, lng: 123.1232567, status: 'normal' }
                        ];

                        // Add markers to map
                        devices.forEach(device => {
                            const marker = L.marker([device.lat, device.lng], { icon: iconNormal })
                                .addTo(map)
                                .bindPopup(`<b>${device.name}</b><br>ID: ${device.id}`);
                            
                            window.deviceMarkers[device.id] = marker;
                        });

                        // Function to update marker color via WebSockets
                        window.updateMarkerStatus = function(deviceId, type) {
                            if (window.deviceMarkers[deviceId]) {
                                let newIcon = iconWarning;
                                if (type === 'Critical Emergency') newIcon = iconCritical;
                                window.deviceMarkers[deviceId].setIcon(newIcon);
                            }
                        };
                    });
                </script>
                </div>

                <!-- Active Alerts & Actions -->
                <div class="flex flex-col gap-6">
                    <!-- Active Alerts -->
                    <div class="bg-brand-card border border-brand-border rounded-xl flex flex-col h-[280px]">
                        <div class="px-5 py-4 flex items-center justify-between border-b border-brand-border">
                            <h2 class="text-xs font-bold text-white uppercase tracking-wider">ACTIVE ALERTS</h2>
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
                                            <div class="text-white font-bold text-sm leading-tight mb-1">Gymnasium</div>
                                            <div class="text-brand-text text-[11px] mb-2">General Emergency</div>
                                            <div class="text-[10px] text-slate-500">Today, 10:28 AM • Device ID: GYM-001</div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <span class="bg-brand-red text-white text-[8px] font-bold px-2 py-0.5 rounded uppercase tracking-wider mb-2">CRITICAL EMERGENCY</span>
                                        <span class="text-brand-red text-[10px] font-bold uppercase tracking-wider mb-2">ACTIVE</span>
                                        <button class="border border-brand-border text-brand-text text-[10px] hover:text-white hover:bg-white/5 px-2 py-1 rounded transition-colors">View Details</button>
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
                                            <div class="text-white font-bold text-sm leading-tight mb-1">Engineering Building</div>
                                            <div class="text-brand-text text-[11px] mb-2">Medical Emergency</div>
                                            <div class="text-[10px] text-slate-500">Today, 10:15 AM • Device ID: ENG-001</div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <span class="bg-brand-orange text-white text-[8px] font-bold px-2 py-0.5 rounded uppercase tracking-wider mb-2">MEDICAL EMERGENCY</span>
                                        <span class="text-brand-orange text-[10px] font-bold uppercase tracking-wider mb-2">ACTIVE</span>
                                        <button class="border border-brand-border text-brand-text text-[10px] hover:text-white hover:bg-white/5 px-2 py-1 rounded transition-colors">View Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-brand-card border border-brand-border rounded-xl flex flex-col flex-1">
                        <div class="px-5 py-4 border-b border-brand-border">
                            <h2 class="text-xs font-bold text-white uppercase tracking-wider">QUICK ACTIONS</h2>
                        </div>
                        <div class="p-4 grid grid-cols-2 gap-3 h-full content-center">
                            <button class="bg-[#5B21B6] hover:bg-[#4C1D95] text-white rounded-lg flex items-center justify-center p-3 transition-colors shadow-lg shadow-purple-900/20 border border-purple-500/30">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                                <span class="font-medium text-sm">Sound Alarm</span>
                            </button>
                            <button class="bg-brand-blue hover:bg-blue-700 text-white rounded-lg flex items-center justify-center p-3 transition-colors shadow-lg shadow-blue-900/20 border border-blue-500/30">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                <span class="font-medium text-sm">Send SMS Now</span>
                            </button>
                            <button class="bg-brand-bg hover:bg-brand-border border border-brand-border text-white rounded-lg flex items-center justify-center p-3 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                <span class="font-medium text-sm">Export Report</span>
                            </button>
                            <button class="bg-brand-bg hover:bg-brand-border border border-brand-border text-white rounded-lg flex items-center justify-center p-3 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span class="font-medium text-sm">Refresh Data</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Row: Table and Summary -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Incident Logs -->
                <div class="lg:col-span-2 bg-brand-card border border-brand-border rounded-xl flex flex-col">
                    <div class="px-5 py-4 flex items-center justify-between border-b border-brand-border">
                        <h2 class="text-xs font-bold text-white uppercase tracking-wider">RECENT INCIDENT LOGS</h2>
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
                            <tbody class="text-xs text-brand-white">
                                <tr class="border-b border-brand-border/50 hover:bg-white/5 transition-colors">
                                    <td class="px-5 py-3 text-brand-text">1</td>
                                    <td class="px-5 py-3">May 15, 2025 10:28 AM</td>
                                    <td class="px-5 py-3"><span class="bg-brand-red text-white text-[9px] font-bold px-2 py-0.5 rounded">General Emergency</span></td>
                                    <td class="px-5 py-3 text-brand-text">Gymnasium</td>
                                    <td class="px-5 py-3 text-brand-text">GYM-001</td>
                                    <td class="px-5 py-3 text-brand-red font-medium">Active</td>
                                    <td class="px-5 py-3 text-brand-text">-</td>
                                    <td class="px-5 py-3 text-center"><button class="text-brand-blue hover:text-blue-400"><svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button></td>
                                </tr>
                                <tr class="border-b border-brand-border/50 hover:bg-white/5 transition-colors">
                                    <td class="px-5 py-3 text-brand-text">2</td>
                                    <td class="px-5 py-3">May 15, 2025 10:15 AM</td>
                                    <td class="px-5 py-3"><span class="bg-brand-orange text-white text-[9px] font-bold px-2 py-0.5 rounded">Medical Emergency</span></td>
                                    <td class="px-5 py-3 text-brand-text">Engineering Building</td>
                                    <td class="px-5 py-3 text-brand-text">ENG-001</td>
                                    <td class="px-5 py-3 text-brand-red font-medium">Active</td>
                                    <td class="px-5 py-3 text-brand-text">-</td>
                                    <td class="px-5 py-3 text-center"><button class="text-brand-blue hover:text-blue-400"><svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button></td>
                                </tr>
                                <tr class="border-b border-brand-border/50 hover:bg-white/5 transition-colors">
                                    <td class="px-5 py-3 text-brand-text">3</td>
                                    <td class="px-5 py-3">May 15, 2025 09:42 AM</td>
                                    <td class="px-5 py-3"><span class="bg-brand-red text-white text-[9px] font-bold px-2 py-0.5 rounded">General Emergency</span></td>
                                    <td class="px-5 py-3 text-brand-text">Library</td>
                                    <td class="px-5 py-3 text-brand-text">LIB-001</td>
                                    <td class="px-5 py-3 text-brand-green font-medium">Resolved</td>
                                    <td class="px-5 py-3 text-brand-text">NDRRMO Staff 1</td>
                                    <td class="px-5 py-3 text-center"><button class="text-brand-blue hover:text-blue-400"><svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button></td>
                                </tr>
                                <tr class="border-b border-brand-border/50 hover:bg-white/5 transition-colors">
                                    <td class="px-5 py-3 text-brand-text">4</td>
                                    <td class="px-5 py-3">May 15, 2025 08:31 AM</td>
                                    <td class="px-5 py-3"><span class="bg-brand-green text-white text-[9px] font-bold px-2 py-0.5 rounded">Medical Emergency</span></td>
                                    <td class="px-5 py-3 text-brand-text">Engineering Building</td>
                                    <td class="px-5 py-3 text-brand-text">ENG-001</td>
                                    <td class="px-5 py-3 text-brand-green font-medium">Resolved</td>
                                    <td class="px-5 py-3 text-brand-text">NDRRMO Staff 2</td>
                                    <td class="px-5 py-3 text-center"><button class="text-brand-blue hover:text-blue-400"><svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button></td>
                                </tr>
                                <tr class="hover:bg-white/5 transition-colors">
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
                            <button class="w-6 h-6 rounded flex items-center justify-center hover:bg-white/10 text-brand-text">&lt;</button>
                            <button class="w-6 h-6 rounded flex items-center justify-center bg-brand-blue text-white">1</button>
                            <button class="w-6 h-6 rounded flex items-center justify-center hover:bg-white/10">2</button>
                            <button class="w-6 h-6 rounded flex items-center justify-center hover:bg-white/10">3</button>
                            <button class="w-6 h-6 rounded flex items-center justify-center hover:bg-white/10 text-brand-text">&gt;</button>
                        </div>
                    </div>
                </div>

                <!-- Incident Summary & Response Status -->
                <div class="bg-brand-card border border-brand-border rounded-xl flex flex-col">
                    <div class="px-5 py-4 border-b border-brand-border">
                        <h2 class="text-xs font-bold text-white uppercase tracking-wider">INCIDENT SUMMARY (TODAY)</h2>
                    </div>
                    <div class="p-5 flex items-center justify-between border-b border-brand-border border-dashed">
                        <!-- Donut Chart -->
                        <div class="relative w-24 h-24 shrink-0">
                            <!-- CSS pure donut chart hack using conic-gradient -->
                            <div class="w-full h-full rounded-full" style="background: conic-gradient(#EF4444 0% 25%, #F59E0B 25% 62.5%, #10B981 62.5% 87.5%, #EAB308 87.5% 100%);"></div>
                            <!-- Inner circle for donut -->
                            <div class="absolute inset-2 bg-brand-card rounded-full flex flex-col items-center justify-center">
                                <span class="text-xl font-bold text-white leading-none">8</span>
                                <span class="text-[10px] text-brand-text mt-1">Total</span>
                            </div>
                        </div>
                        
                        <!-- Legend -->
                        <div class="ml-4 flex-1 text-[11px]">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-brand-red mr-2"></span><span class="text-brand-text">Critical Emergency</span></div>
                                <div class="text-white font-medium">2 (25%)</div>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-brand-orange mr-2"></span><span class="text-brand-text">Medical Emergency</span></div>
                                <div class="text-white font-medium">3 (37.5%)</div>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></span><span class="text-brand-text">Public Safety Emergency</span></div>
                                <div class="text-white font-medium">1 (12.5%)</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-brand-green mr-2"></span><span class="text-brand-text">Facility & Hazard Emergency</span></div>
                                <div class="text-white font-medium">2 (25%)</div>
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
                            <span class="text-white font-bold">1</span>
                        </div>
                    </div>
                </div>
            </div>
            
@endsection
