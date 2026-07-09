<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NDRRMO Dashboard</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: {
                            bg: '#0F1523',
                            sidebar: '#0A0F1A',
                            card: '#182235',
                            border: '#2A364E',
                            blue: '#2563EB',
                            red: '#EF4444',
                            orange: '#F59E0B',
                            green: '#10B981',
                            text: '#94A3B8',
                            white: '#F8FAFC'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: theme('colors.brand.bg'); color: theme('colors.brand.white'); font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="h-screen flex overflow-hidden text-sm">

    <!-- Sidebar -->
    <aside class="w-64 bg-brand-sidebar flex flex-col border-r border-brand-border z-20 shrink-0">
        <div class="h-16 flex items-center px-4 border-b border-brand-border shrink-0">
            <div class="w-8 h-8 rounded-full bg-yellow-500 flex items-center justify-center shrink-0 overflow-hidden border-2 border-brand-sidebar">
                <div class="w-full h-full bg-yellow-400 rounded-full flex items-center justify-center">
                    <span class="text-[10px] font-bold text-black">JHCSC</span>
                </div>
            </div>
            <div class="ml-3 text-[11px] font-bold leading-tight text-white tracking-wide">
                Smart Student Panic Button &<br>Emergency Response System
            </div>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar p-4 flex flex-col gap-1">
            <div class="text-[10px] font-bold text-brand-text mb-2 tracking-wider">MAIN NAVIGATION</div>
            
            <a href="#" class="flex items-center px-3 py-2.5 bg-brand-blue text-white rounded-lg group">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="font-medium">Dashboard</span>
            </a>
            
            <a href="#" class="flex items-center justify-between px-3 py-2.5 text-brand-text hover:text-white hover:bg-white/5 rounded-lg transition-colors">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span class="font-medium">Live Alerts</span>
                </div>
                <span class="bg-brand-red text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">2</span>
            </a>

            <a href="#" class="flex items-center px-3 py-2.5 text-brand-text hover:text-white hover:bg-white/5 rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span class="font-medium">Incident Logs</span>
            </a>

            <a href="#" class="flex items-center px-3 py-2.5 text-brand-text hover:text-white hover:bg-white/5 rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="font-medium">Campus Map</span>
            </a>

            <a href="#" class="flex items-center px-3 py-2.5 text-brand-text hover:text-white hover:bg-white/5 rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m14-6h2m-2 6h2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                <span class="font-medium">Devices</span>
            </a>

            <a href="#" class="flex items-center px-3 py-2.5 text-brand-text hover:text-white hover:bg-white/5 rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                <span class="font-medium">SMS Logs</span>
            </a>

            <a href="#" class="flex items-center px-3 py-2.5 text-brand-text hover:text-white hover:bg-white/5 rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span class="font-medium">Reports</span>
            </a>

            <a href="#" class="flex items-center px-3 py-2.5 text-brand-text hover:text-white hover:bg-white/5 rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span class="font-medium">Users</span>
            </a>

            <a href="#" class="flex items-center px-3 py-2.5 text-brand-text hover:text-white hover:bg-white/5 rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="font-medium">Settings</span>
            </a>

            <div class="mt-8 border-t border-brand-border pt-6">
                <div class="text-[10px] font-bold text-brand-text mb-4 tracking-wider">SYSTEM STATUS</div>
                
                <div class="flex items-center px-3 py-2">
                    <div class="w-8 h-8 rounded bg-white/5 flex items-center justify-center mr-3 shrink-0">
                        <svg class="w-4 h-4 text-brand-text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m14-6h2m-2 6h2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                    </div>
                    <div>
                        <div class="text-white font-medium text-xs">ESP32 Devices</div>
                        <div class="text-brand-green text-[10px] flex items-center mt-0.5"><span class="w-1.5 h-1.5 rounded-full bg-brand-green mr-1.5"></span>3 / 3 Online</div>
                    </div>
                </div>

                <div class="flex items-center px-3 py-2 mt-1">
                    <div class="w-8 h-8 rounded bg-white/5 flex items-center justify-center mr-3 shrink-0">
                        <svg class="w-4 h-4 text-brand-text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                    </div>
                    <div>
                        <div class="text-white font-medium text-xs">Internet Connection</div>
                        <div class="text-brand-green text-[10px] flex items-center mt-0.5"><span class="w-1.5 h-1.5 rounded-full bg-brand-green mr-1.5"></span>Connected</div>
                    </div>
                </div>

                <div class="flex items-center px-3 py-2 mt-1">
                    <div class="w-8 h-8 rounded bg-white/5 flex items-center justify-center mr-3 shrink-0">
                        <svg class="w-4 h-4 text-brand-text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    </div>
                    <div>
                        <div class="text-white font-medium text-xs">GSM Module</div>
                        <div class="text-brand-green text-[10px] flex items-center mt-0.5"><span class="w-1.5 h-1.5 rounded-full bg-brand-green mr-1.5"></span>Ready</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 border-t border-brand-border text-[10px] text-brand-text shrink-0">
            © 2025 JHCSC. All rights reserved.
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Header -->
        <header class="h-16 bg-brand-bg border-b border-brand-border flex items-center justify-between px-6 shrink-0 z-10">
            <h1 class="text-lg font-medium text-white">NDRRMO Dashboard</h1>
            
            <div class="flex items-center space-x-6">
                <div class="flex items-center text-xs">
                    <span class="w-2 h-2 rounded-full bg-brand-green mr-2 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></span>
                    <span class="text-brand-text">System Online</span>
                </div>
                
                <div class="flex items-center text-xs text-brand-text border-l border-brand-border pl-6">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    May 15, 2025
                    <span class="mx-2">|</span>
                    10:30:45 AM
                </div>

                <div class="flex items-center pl-4 border-l border-brand-border space-x-4">
                    <button class="relative text-brand-text hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-brand-red rounded-full text-[9px] font-bold flex items-center justify-center text-white border border-brand-bg">3</span>
                    </button>
                    
                    <div class="flex items-center cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-slate-700 overflow-hidden mr-3">
                            <img src="https://ui-avatars.com/api/?name=Admin&background=475569&color=fff" alt="Admin" class="w-full h-full object-cover">
                        </div>
                        <div class="hidden md:block">
                            <div class="text-xs font-semibold text-white leading-none mb-1">NDRRMO Admin</div>
                            <div class="text-[10px] text-brand-text leading-none">Administrator</div>
                        </div>
                        <svg class="w-4 h-4 ml-2 text-brand-text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-6 bg-brand-bg">
            
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
            
        </div>
    </main>

    <!-- Scripts -->
    <script type="module">
        window.Echo.channel('emergencies')
            .listen('EmergencyReported', (e) => {
                console.log('New Emergency:', e);
                // Here we dynamically add the row to the active alerts and table
                const alertHtml = `
                    <div class="border border-brand-red/30 bg-brand-red/5 rounded-lg p-3 relative overflow-hidden group mb-3 animate-pulse">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-brand-red"></div>
                        <div class="flex items-start justify-between">
                            <div class="flex items-start">
                                <div class="mt-1 mr-3 text-brand-red">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/></svg>
                                </div>
                                <div>
                                    <div class="text-white font-bold text-sm leading-tight mb-1">${e.incident.device.building}</div>
                                    <div class="text-brand-text text-[11px] mb-2">${e.incident.emergency_type}</div>
                                    <div class="text-[10px] text-slate-500">Just now • Device ID: ${e.incident.device.device_code}</div>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="bg-brand-red text-white text-[8px] font-bold px-2 py-0.5 rounded uppercase tracking-wider mb-2">NEW ALARM</span>
                                <span class="text-brand-red text-[10px] font-bold uppercase tracking-wider mb-2">ACTIVE</span>
                            </div>
                        </div>
                    </div>
                `;
                // Just a basic prepending for visual effect
                document.querySelector('.custom-scrollbar').insertAdjacentHTML('afterbegin', alertHtml);
                alert('New Emergency Alert Received: ' + e.incident.emergency_type);
                
                // Update Map Marker
                if (window.updateMarkerStatus) {
                    window.updateMarkerStatus(e.incident.device.device_code, e.incident.emergency_type);
                }
            });
    </script>
</body>
</html>
