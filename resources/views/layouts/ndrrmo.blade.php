<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DRRMO Dashboard</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.0/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>
<body class="app-body h-screen flex overflow-hidden text-sm p-3 gap-3 text-slate-900">

    <!-- Sidebar -->
    <aside class="w-64 glass-sidebar flex flex-col z-20 shrink-0 rounded-2xl overflow-hidden shadow-sm">
        <div class="h-20 flex items-center px-4 border-b border-slate-200/80 shrink-0 gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/90 p-1 shadow-sm border border-slate-200 flex items-center justify-center shrink-0">
                <img src="/images/logo.png" alt="JHCSC Logo" class="w-full h-full object-contain">
            </div>
            <div class="flex flex-col justify-center min-w-0">
                <span class="text-[12px] font-extrabold text-black leading-snug tracking-tight truncate">Smart Panic Button</span>
                <span class="text-[10px] font-bold text-slate-700 leading-tight truncate">Emergency Response System</span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar p-3 flex flex-col gap-1">
            <p class="nav-section">Navigation</p>
            
            <a id="nav-ndrrmo-dashboard" href="{{ route('ndrrmo.dashboard') }}" class="nav-link {{ request()->routeIs('ndrrmo.dashboard') ? 'nav-link-active' : '' }} group">
                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="font-bold text-[13px]">Dashboard</span>
            </a>
            
            <a id="nav-ndrrmo-alerts" href="{{ route('ndrrmo.alerts') }}" class="nav-link justify-between {{ request()->routeIs('ndrrmo.alerts') ? 'nav-link-active' : '' }} group {{ ($activeAlertsCount ?? 0) > 0 ? 'nav-alert-blinking' : '' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('ndrrmo.alerts') ? 'text-white' : 'text-brand-red group-hover:text-brand-blue' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="font-bold text-[13px] {{ request()->routeIs('ndrrmo.alerts') ? 'text-white' : 'text-brand-red group-hover:text-brand-blue' }}">Emergency Alerts</span>
                </div>
                <span id="ndrrmo-sidebar-alert-badge" class="bg-brand-red text-white text-[9px] font-extrabold px-1.5 py-0.5 rounded-full animate-pulse {{ ($activeAlertsCount ?? 0) > 0 ? '' : 'hidden' }}">{{ $activeAlertsCount ?? 0 }}</span>
            </a>

            <a id="nav-ndrrmo-logs" href="{{ route('ndrrmo.logs') }}" class="nav-link {{ request()->routeIs('ndrrmo.logs') ? 'nav-link-active' : '' }} group">
                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span class="font-bold text-[13px]">Incident Logs</span>
            </a>

            <a id="nav-ndrrmo-map" href="{{ route('ndrrmo.map') }}" class="nav-link {{ request()->routeIs('ndrrmo.map') ? 'nav-link-active' : '' }} group {{ ($activeAlertsCount ?? 0) > 0 ? 'nav-alert-blinking' : '' }}">
                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="font-bold text-[13px]">Campus Incident Map</span>
            </a>

            <a id="nav-ndrrmo-devices" href="{{ route('ndrrmo.devices') }}" class="nav-link {{ request()->routeIs('ndrrmo.devices') ? 'nav-link-active' : '' }} group">
                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m14-6h2m-2 6h2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                <span class="font-bold text-[13px]">Device Status</span>
            </a>

            <a id="nav-ndrrmo-sms" href="{{ route('ndrrmo.sms') }}" class="nav-link {{ request()->routeIs('ndrrmo.sms') ? 'nav-link-active' : '' }} group">
                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                <span class="font-bold text-[13px]">SMS Logs</span>
            </a>

            @if(config('app.simulation_enabled'))
            <a id="nav-ndrrmo-simulation" href="{{ route('ndrrmo.simulation') }}" class="nav-link {{ request()->routeIs('ndrrmo.simulation') ? 'nav-link-active' : '' }} group">
                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-bold text-[13px]">Device Simulation</span>
            </a>
            @endif

            <a id="nav-ndrrmo-reports" href="{{ route('ndrrmo.reports') }}" class="nav-link {{ request()->routeIs('ndrrmo.reports') ? 'nav-link-active' : '' }} group">
                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span class="font-bold text-[13px]">Incident Reports</span>
            </a>
        </div>

        <div class="p-3 border-t border-slate-200/80 mt-auto">
            <form method="POST" action="{{ route('logout') }}" onsubmit="return confirmAction(event, 'Are you sure you want to log out of your DRRMO session?', 'Confirm Logout', 'Logout', 'danger')">
                @csrf
                <button type="submit" class="flex items-center w-full px-3.5 py-2.5 text-brand-red font-bold hover:text-white hover:bg-red-600 hover:shadow-md hover:shadow-red-200 hover:translate-x-1 rounded-xl transition-all duration-200 group cursor-pointer">
                    <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span class="font-bold text-[13px]">Logout</span>
                </button>
            </form>
            <p class="mt-3 text-[10px] font-bold text-black text-center">© {{ date('Y') }} JHCSC. All rights reserved.</p>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="app-main flex-1 flex flex-col min-w-0 overflow-hidden rounded-2xl text-slate-900">
        <!-- Header -->
        <header class="app-header h-16 flex items-center justify-between px-6 shrink-0 z-10 text-slate-900">
            <div class="flex items-baseline space-x-2">
                <h1 class="text-lg font-bold text-slate-900 tracking-tight">Command Center</h1>
                <span class="text-xs font-medium text-slate-500">DRRMO operations</span>
            </div>
            
            <div class="flex items-center space-x-6">
                <div class="flex items-center text-xs font-bold text-black">
                    <span class="w-2.5 h-2.5 rounded-full bg-brand-green mr-2 shadow-[0_0_8px_rgba(22,163,74,0.8)]"></span>
                    <span class="text-black font-bold">System Online</span>
                </div>
                
                <div class="flex items-center text-xs font-bold text-black border-l border-slate-300 pl-6">
                    <svg class="w-4 h-4 mr-2 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span id="ndrrmo-header-date">--</span>
                    <span class="mx-2">|</span>
                    <span id="ndrrmo-header-time" class="tabular-nums">--</span>
                </div>

                <div class="flex items-center pl-4 border-l border-slate-300 space-x-4">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" type="button" class="flex items-center cursor-pointer focus:outline-none group">
                            <div class="w-8 h-8 rounded-full bg-slate-900 overflow-hidden mr-3 ring-2 ring-transparent group-hover:ring-blue-500 transition-all">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->fullname ?? auth()->user()->username ?? 'Admin') }}&background=000000&color=fff" alt="{{ auth()->user()->fullname ?? 'Admin' }}" class="w-full h-full object-cover">
                            </div>
                            <div class="hidden md:block text-left">
                                <div class="text-xs font-bold text-black leading-none mb-1">{{ auth()->user()->fullname ?? 'DRRMO Admin' }}</div>
                                <div class="text-[11px] font-semibold text-slate-600 leading-none">{{ auth()->user()->role ?? 'Administrator' }}</div>
                            </div>
                            <svg class="w-4 h-4 ml-2 text-black transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak style="display: none;" class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-50">
                            <a href="{{ route('ndrrmo.profile') }}" class="flex items-center px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-blue-600 transition-colors">
                                <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                My Profile
                            </a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}" onsubmit="return confirmAction(event, 'Are you sure you want to log out of your DRRMO session?', 'Confirm Logout', 'Logout', 'danger')">
                                @csrf
                                <button type="submit" class="flex items-center w-full px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="app-content flex-1 overflow-y-auto custom-scrollbar p-6 text-slate-800 flex flex-col">
@yield("content")
        </div>
    </main>

    {{-- Viewport Perimeter Warning Border (Non-blocking: operator can see & click everything) --}}
    <div id="global-emergency-perimeter" class="fixed inset-0 pointer-events-none z-[9980] border-4 border-red-600 shadow-[inset_0_0_45px_rgba(220,38,38,0.45)] animate-pulse hidden"></div>

    {{-- Non-Blocking Top Floating Emergency Command HUD --}}
    <div id="global-emergency-overlay" class="fixed top-3 left-1/2 -translate-x-1/2 z-[9990] w-full max-w-5xl px-3 pointer-events-none hidden transition-all duration-300">
        <div id="global-emergency-modal" class="pointer-events-auto bg-white/95 backdrop-blur-md border-2 border-red-600 rounded-2xl shadow-2xl overflow-hidden transition-all flex flex-col">
            
            {{-- Top Ribbon Bar --}}
            <div id="hud-header" class="bg-red-600 px-4 py-2.5 flex items-center justify-between text-white shrink-0">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-white animate-ping"></span>
                    <span id="global-emergency-category-badge" class="font-black text-xs uppercase tracking-wider">🚨 EMERGENCY ALERT</span>
                    <span id="hud-multi-counter" class="hidden bg-black/30 text-white text-[10px] font-black px-2 py-0.5 rounded-full">Alert 1 of 1</span>
                </div>
                
                <div class="flex items-center gap-2">
                    <button type="button" id="hud-ackall-ribbon-btn" onclick="acknowledgeAllActiveEmergencies()" class="hidden px-2.5 py-1 bg-slate-900 hover:bg-black text-white text-[11px] font-black rounded-lg transition-all cursor-pointer active:scale-95">ACK ALL</button>
                    {{-- Mute Siren Toggle --}}
                    <button type="button" id="hud-mute-btn" onclick="toggleMuteSiren()" title="Mute/Unmute Siren Audio" class="px-2.5 py-1 bg-black/25 hover:bg-black/45 text-white text-[11px] font-bold rounded-lg transition-all flex items-center gap-1 cursor-pointer">
                        <span id="hud-mute-icon">🔊</span>
                        <span id="hud-mute-text">Mute Siren</span>
                    </button>
                    {{-- Fly to Map button --}}
                    <button type="button" id="hud-fly-map-btn" onclick="flyToCurrentIncidentMap()" title="Locate on Map" class="px-2.5 py-1 bg-white hover:bg-slate-100 text-slate-800 text-[11px] font-black rounded-lg transition-all flex items-center gap-1 cursor-pointer">
                        <span>🗺️</span>
                        <span>Fly to Map</span>
                    </button>
                    {{-- Minimize/Expand toggle --}}
                    <button type="button" id="hud-toggle-minimize" onclick="toggleHudMinimized()" title="Minimize / Expand HUD" class="w-7 h-7 flex items-center justify-center bg-black/25 hover:bg-black/40 text-white rounded-lg transition-all cursor-pointer">
                        <span id="hud-minimize-icon">▲</span>
                    </button>
                </div>
            </div>

            {{-- Collapsible HUD Body --}}
            <div id="hud-body" class="p-4 flex flex-col gap-3">
                {{-- Multi-alert header banner (shown when > 1 unhandled alerts) --}}
                {{-- Alert Summary Row --}}
                <div id="hud-single-summary" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 border border-slate-200 rounded-xl p-3">
                    <div class="flex items-center gap-3">
                        <div id="global-emergency-icon-box" class="w-11 h-11 rounded-xl bg-red-100 border border-red-300 text-red-600 flex items-center justify-center font-black text-lg shrink-0">
                            🚨
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-0.5">
                                <h3 id="global-emergency-title" class="font-black text-slate-900 text-sm uppercase">EMERGENCY ALERT</h3>
                                <span id="hud-status-badge" class="px-2 py-0.2 rounded-full text-[10px] font-black bg-red-100 text-red-700 animate-pulse">PENDING RESPONSE</span>
                            </div>
                            <p id="global-emergency-location" class="font-extrabold text-slate-900 text-sm">Location loading...</p>
                            <p id="global-emergency-device" class="text-[11px] text-slate-500 font-mono">Device ID loading...</p>
                        </div>
                    </div>

                    {{-- Actions on HUD --}}
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <button id="global-emergency-ack-btn" type="button" onclick="acknowledgeActiveEmergency()"
                                class="py-2 px-3.5 rounded-xl font-extrabold text-white text-xs shadow-md bg-red-600 hover:bg-red-700 active:scale-95 transition-all flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span id="global-emergency-ack-btn-text">ACKNOWLEDGE</span>
                        </button>

                        <button id="global-emergency-dispatch-btn" type="button" onclick="quickDispatchCurrentIncident()"
                                class="py-2 px-3.5 rounded-xl font-extrabold text-white text-xs shadow-md bg-blue-600 hover:bg-blue-700 active:scale-95 transition-all flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>DISPATCH</span>
                        </button>

                        <button id="global-emergency-false-btn" type="button" onclick="quickFalseAlarmCurrentIncident()"
                                class="py-2 px-3 rounded-xl font-bold text-slate-700 text-xs bg-white border border-slate-300 hover:bg-slate-100 active:scale-95 transition-all cursor-pointer">
                            <span>⚠️ False Alarm</span>
                        </button>

                        <button id="global-emergency-ack-all-btn" type="button" onclick="acknowledgeAllActiveEmergencies()"
                                class="hidden py-2 px-3 rounded-xl font-bold text-slate-800 text-xs bg-slate-200 hover:bg-slate-300 active:scale-95 transition-all cursor-pointer">
                            <span id="global-emergency-ack-all-text">ACK ALL</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- One separate container per unhandled alert, stacked vertically (shown when more than one) --}}
        <div id="hud-alert-grid" class="hidden"></div>
    </div>

    <!-- Scripts -->
    <script>
        window.activeEmergencyIncidents = [];
        window.currentEmergencyIndex = 0;
        let isAckInProgress = false;
        let webAudioCtx = null;
        let sirenOscillator = null;
        let sirenGainNode = null;
        let sirenInterval = null;
        let isSirenActive = false;

        function triggerScreenFlashAndAlarm(incident) {
            if (!incident || !incident.id) return;

            if (incident.status === 'Resolved' || incident.status !== 'Pending') {
                window.activeEmergencyIncidents = window.activeEmergencyIncidents.filter(i => i.id !== incident.id);
                if (window.activeEmergencyIncidents.length === 0) {
                    closeEmergencyModal();
                } else {
                    if (window.currentEmergencyIndex >= window.activeEmergencyIncidents.length) {
                        window.currentEmergencyIndex = 0;
                    }
                    renderActiveEmergencyModal();
                }

                // If on NDRRMO dashboard or alerts, reload smoothly so stepper and badges reflect latest step
                if (window.location.pathname.startsWith('/ndrrmo')) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 600);
                }
                return;
            }

            const existingIdx = window.activeEmergencyIncidents.findIndex(i => i.id === incident.id);
            if (existingIdx === -1) {
                window.activeEmergencyIncidents.push(incident);
                const type = incident.emergency_type || 'Emergency';
                const loc = (incident.device && incident.device.building) ? incident.device.building : 'Campus';
                speakEmergencyAnnouncement(`Urgent Alert! ${type} detected at ${loc}! Respond immediately!`);
            } else {
                window.activeEmergencyIncidents[existingIdx] = incident;
            }
            renderActiveEmergencyModal();
        }

        function syncEmergencyIncidents(pendingIncidents) {
            if (!Array.isArray(pendingIncidents)) return;
            if (pendingIncidents.length === 0) {
                if (window.activeEmergencyIncidents.length > 0) {
                    window.activeEmergencyIncidents = [];
                    closeEmergencyModal();
                }
                return;
            }

            const currentIds = window.activeEmergencyIncidents.map(i => i.id).sort().join(',');
            const newIds = pendingIncidents.map(i => i.id).sort().join(',');

            if (currentIds !== newIds) {
                const hadZero = window.activeEmergencyIncidents.length === 0;
                window.activeEmergencyIncidents = [...pendingIncidents];
                if (window.currentEmergencyIndex >= window.activeEmergencyIncidents.length) {
                    window.currentEmergencyIndex = Math.max(0, window.activeEmergencyIncidents.length - 1);
                }
                renderActiveEmergencyModal();

                if (hadZero && window.activeEmergencyIncidents.length > 0) {
                    const first = window.activeEmergencyIncidents[0];
                    const type = first.emergency_type || 'Emergency';
                    const loc = (first.device && first.device.building) ? first.device.building : 'Campus';
                    speakEmergencyAnnouncement(`Urgent Alert! ${type} detected at ${loc}! Respond immediately!`);
                }
            }
        }

        function navigateEmergency(step) {
            if (window.activeEmergencyIncidents.length <= 1) return;
            window.currentEmergencyIndex = (window.currentEmergencyIndex + step + window.activeEmergencyIncidents.length) % window.activeEmergencyIncidents.length;
            renderActiveEmergencyModal();
        }

        function selectEmergencyIndex(idx) {
            if (idx >= 0 && idx < window.activeEmergencyIncidents.length) {
                window.currentEmergencyIndex = idx;
                renderActiveEmergencyModal();
            }
        }

        function emergencyCardTheme(type) {
            if (type.includes('Medical')) return { border: 'border-orange-500', badge: 'bg-orange-600' };
            if (type.includes('Public Safety') || type.includes('Facility')) return { border: 'border-amber-500', badge: 'bg-amber-600' };
            return { border: 'border-red-600', badge: 'bg-red-600' };
        }

        function hudCardAction(idx, action) {
            if (idx < 0 || idx >= window.activeEmergencyIncidents.length) return;
            window.currentEmergencyIndex = idx;
            if (action === 'ack') acknowledgeActiveEmergency();
            else if (action === 'dispatch') quickDispatchCurrentIncident();
            else if (action === 'false') quickFalseAlarmCurrentIncident();
            else if (action === 'map') flyToCurrentIncidentMap();
        }

        function renderEmergencyGrid(stack) {
            if (!stack) return;
            const list = window.activeEmergencyIncidents;
            stack.className = `pointer-events-auto mt-3 flex flex-row items-stretch gap-3 max-h-[calc(100vh-7rem)] overflow-x-auto overflow-y-auto custom-scrollbar ${window.isHudMinimized ? 'hidden' : ''}`;
            stack.innerHTML = '';

            list.forEach((inc, idx) => {
                const type = inc.emergency_type || 'Emergency Alert';
                const theme = emergencyCardTheme(type);
                const loc = (inc.device && inc.device.building) ? inc.device.building : 'Location not recorded';
                const code = (inc.device && inc.device.device_code) ? inc.device.device_code : 'Not recorded';
                const time = inc.reported_at ? new Date(inc.reported_at).toLocaleTimeString() : 'Just now';

                const box = document.createElement('div');
                box.className = `flex-1 min-w-[250px] bg-white/95 backdrop-blur-md border-2 ${theme.border} rounded-2xl shadow-2xl overflow-hidden flex flex-col`;

                const head = document.createElement('div');
                head.className = `${theme.badge} px-4 py-2 flex items-center justify-between text-white`;
                const title = document.createElement('span');
                title.className = 'font-black text-xs uppercase tracking-wider';
                title.textContent = `🚨 ${type}`;
                const status = document.createElement('span');
                status.className = 'bg-black/30 text-[10px] font-black px-2 py-0.5 rounded-full animate-pulse';
                status.textContent = (inc.status || 'Pending').toUpperCase();
                head.append(title, status);

                const body = document.createElement('div');
                body.className = 'p-3 flex flex-col gap-2.5';
                const where = document.createElement('div');
                const locEl = document.createElement('p');
                locEl.className = 'font-extrabold text-slate-900 text-sm';
                locEl.textContent = loc;
                const meta = document.createElement('p');
                meta.className = 'text-[11px] text-slate-500 font-mono';
                meta.textContent = `Device ID: ${code} • Reported ${time}`;
                where.append(locEl, meta);

                const actions = document.createElement('div');
                actions.className = 'grid grid-cols-2 gap-2 mt-auto';
                [
                    ['ack', 'ACKNOWLEDGE', 'bg-red-600 hover:bg-red-700 text-white'],
                    ['dispatch', 'DISPATCH', 'bg-blue-600 hover:bg-blue-700 text-white'],
                    ['false', '⚠️ False Alarm', 'bg-white border border-slate-300 hover:bg-slate-100 text-slate-700'],
                    ['map', '🗺️ Map', 'bg-white border border-slate-300 hover:bg-slate-100 text-slate-700'],
                ].forEach(([action, label, cls]) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = `${cls} py-2 rounded-xl text-xs font-extrabold active:scale-95 transition-all cursor-pointer`;
                    btn.textContent = label;
                    btn.addEventListener('click', () => hudCardAction(idx, action));
                    actions.appendChild(btn);
                });

                body.append(where, actions);
                box.append(head, body);
                stack.appendChild(box);
            });
        }

        function renderActiveEmergencyModal() {
            if (!window.activeEmergencyIncidents || window.activeEmergencyIncidents.length === 0) {
                closeEmergencyModal();
                return;
            }

            if (window.currentEmergencyIndex >= window.activeEmergencyIncidents.length) {
                window.currentEmergencyIndex = Math.max(0, window.activeEmergencyIncidents.length - 1);
            }

            const total = window.activeEmergencyIncidents.length;
            const currentIdx = window.currentEmergencyIndex;
            const incident = window.activeEmergencyIncidents[currentIdx];
            if (!incident) return;

            const type = incident.emergency_type || 'Emergency Alert';
            const location = (incident.device && incident.device.building) ? incident.device.building : 'Location not recorded';
            const deviceCode = (incident.device && incident.device.device_code) ? incident.device.device_code : 'Not recorded';
            const hasCoordinates = Boolean(incident.device && incident.device.latitude && incident.device.longitude);
            const lat = hasCoordinates ? parseFloat(incident.device.latitude) : null;
            const lng = hasCoordinates ? parseFloat(incident.device.longitude) : null;

            const overlay = document.getElementById('global-emergency-overlay');
            const backdrop = document.getElementById('global-emergency-backdrop');
            const modal = document.getElementById('global-emergency-modal');
            const iconBox = document.getElementById('global-emergency-icon-box');
            const badge = document.getElementById('global-emergency-category-badge');
            const title = document.getElementById('global-emergency-title');
            const locEl = document.getElementById('global-emergency-location');
            const devEl = document.getElementById('global-emergency-device');
            const ackBtn = document.getElementById('global-emergency-ack-btn');
            const ackBtnText = document.getElementById('global-emergency-ack-btn-text');
            const ackAllBtn = document.getElementById('global-emergency-ack-all-btn');
            const ackAllText = document.getElementById('global-emergency-ack-all-text');
            const coordsText = document.getElementById('modal-map-coords-text');
            const mapContainer = document.getElementById('modal-emergency-map-container');

            const multiHeader = document.getElementById('multi-alert-header');
            const multiCountText = document.getElementById('multi-alert-count-text');
            const multiStepText = document.getElementById('multi-alert-step-text');
            const multiTabs = document.getElementById('multi-alert-tabs');
            const perimeter = document.getElementById('global-emergency-perimeter');
            const hudCounter = document.getElementById('hud-multi-counter');
            const hudHeader = document.getElementById('hud-header');
            const statusBadge = document.getElementById('hud-status-badge');

            let headerBg = 'bg-red-600';
            let badgeBg = 'bg-red-600';
            let borderCls = 'border-red-600';

            if (type.includes('Medical')) {
                headerBg = 'bg-orange-600';
                badgeBg = 'bg-orange-600';
                borderCls = 'border-orange-500';
            } else if (type.includes('Public Safety') || type.includes('Facility')) {
                headerBg = 'bg-amber-600';
                badgeBg = 'bg-amber-600';
                borderCls = 'border-amber-500';
            }

            if (perimeter) {
                perimeter.classList.remove('hidden');
            }

            if (modal) {
                modal.className = `pointer-events-auto bg-white/95 backdrop-blur-md border-2 ${borderCls} rounded-2xl shadow-2xl overflow-hidden transition-all flex flex-col`;
            }
            if (hudHeader) {
                hudHeader.className = `${headerBg} px-4 py-2.5 flex items-center justify-between text-white shrink-0`;
            }
            if (badge) {
                badge.textContent = `🚨 ${type.toUpperCase()}`;
            }
            if (title) title.textContent = type.toUpperCase();
            if (locEl) locEl.textContent = location;
            if (devEl) devEl.textContent = `Device ID: ${deviceCode} • Reported ${incident.reported_at ? new Date(incident.reported_at).toLocaleTimeString() : 'Just now'}`;
            if (statusBadge) {
                statusBadge.textContent = (incident.status || 'PENDING').toUpperCase();
            }

            // Multi-alert UI: every unhandled alert gets its own container, stacked vertically
            const hudBody = document.getElementById('hud-body');
            const alertGrid = document.getElementById('hud-alert-grid');
            const ackAllRibbon = document.getElementById('hud-ackall-ribbon-btn');
            if (overlay) {
                overlay.classList.toggle('max-w-5xl', total <= 1 || total === 2);
                overlay.classList.toggle('max-w-7xl', total > 2);
            }
            if (total > 1) {
                if (badge) badge.textContent = `🚨 ${total} UNHANDLED ALERTS`;
                if (hudCounter) hudCounter.classList.add('hidden');
                if (ackAllRibbon) ackAllRibbon.classList.remove('hidden');
                if (hudBody) hudBody.classList.add('hidden');
                renderEmergencyGrid(alertGrid);
            } else {
                if (ackAllRibbon) ackAllRibbon.classList.add('hidden');
                if (hudBody) hudBody.classList.toggle('hidden', Boolean(window.isHudMinimized));
                if (alertGrid) { alertGrid.classList.add('hidden'); alertGrid.innerHTML = ''; }
                if (ackBtnText) ackBtnText.textContent = 'ACKNOWLEDGE';
                if (ackAllBtn) ackAllBtn.classList.add('hidden');
            }

            if (overlay) {
                overlay.classList.remove('hidden');
                overlay.classList.add('block');
            }

            startEmergencySirenAudio();
        }

        // Toggle Mute Siren Audio
        window.isSirenMuted = false;
        function toggleMuteSiren() {
            window.isSirenMuted = !window.isSirenMuted;
            const muteIcon = document.getElementById('hud-mute-icon');
            const muteText = document.getElementById('hud-mute-text');
            if (window.isSirenMuted) {
                stopEmergencySirenAudio();
                stopVoiceSpeech();
                if (muteIcon) muteIcon.textContent = '🔇';
                if (muteText) muteText.textContent = 'Unmute';
            } else {
                startEmergencySirenAudio();
                if (muteIcon) muteIcon.textContent = '🔊';
                if (muteText) muteText.textContent = 'Mute Siren';
            }
        }

        // Toggle HUD Minimized (Slim Ribbon vs Full Card)
        window.isHudMinimized = false;
        function toggleHudMinimized() {
            window.isHudMinimized = !window.isHudMinimized;
            const body = document.getElementById('hud-body');
            const icon = document.getElementById('hud-minimize-icon');
            const stack = document.getElementById('hud-alert-grid');
            if (stack && window.activeEmergencyIncidents.length > 1) stack.classList.toggle('hidden', window.isHudMinimized);
            if (window.activeEmergencyIncidents.length > 1) {
                if (icon) icon.textContent = window.isHudMinimized ? '▼' : '▲';
                return;
            }
            if (window.isHudMinimized) {
                if (body) body.classList.add('hidden');
                if (icon) icon.textContent = '▼';
            } else {
                if (body) body.classList.remove('hidden');
                if (icon) icon.textContent = '▲';
            }
        }

        // Fly to Map Location
        function flyToCurrentIncidentMap() {
            const incident = window.activeEmergencyIncidents[window.currentEmergencyIndex];
            if (!incident || !incident.device) return;

            const lat = incident.device.latitude ? parseFloat(incident.device.latitude) : null;
            const lng = incident.device.longitude ? parseFloat(incident.device.longitude) : null;

            if (lat && lng && window.ndrrmoCampusMap) {
                // On the map page: show exactly this alert's device, closing any other open popup
                const mapEl = document.getElementById('campus-map') || document.getElementById('campusMap');
                if (mapEl) {
                    mapEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                const marker = window.deviceMarkers && window.deviceMarkers[incident.device_id];
                window.ndrrmoCampusMap.closePopup();
                window.ndrrmoCampusMap.flyTo([lat, lng], 17, { animate: true, duration: 1.2 });
                if (marker) {
                    window.ndrrmoCampusMap.once('moveend', () => marker.openPopup());
                }
            } else {
                // If on another page, open the map page focused on this alert
                window.location.href = `/ndrrmo/map?incident_id=${incident.id}`;
            }
        }

        // Quick Dispatch Current Incident from HUD
        function quickDispatchCurrentIncident() {
            const incident = window.activeEmergencyIncidents[window.currentEmergencyIndex];
            if (!incident) return;

            const card = document.getElementById(`incident-card-${incident.id}`);
            if (card) {
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                card.classList.add('ring-4', 'ring-blue-500');
                setTimeout(() => card.classList.remove('ring-4', 'ring-blue-500'), 2500);
                // Also trigger dispatch modal if available
                const dispatchModalBtn = document.getElementById(`open-dispatch-modal-${incident.id}`);
                if (dispatchModalBtn) dispatchModalBtn.click();
                return;
            }

            // Direct fallback dispatch
            window.showConfirmDialog({
                title: 'Dispatch Responders',
                message: 'Enter the responder details for this incident.',
                confirmText: 'Dispatch',
                type: 'warning',
                fields: [
                    { name: 'responder', label: 'Responder Name / Unit', value: 'DRRMO Responders Team' },
                    { name: 'eta', label: 'Estimated Time of Arrival (minutes)', type: 'number', min: 1, value: '3' }
                ]
            }).then(values => {
                if (!values) return;
                fetch(`/ndrrmo/incidents/${incident.id}/dispatch`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        responder_name: values.responder || 'DRRMO Responders Team',
                        eta_minutes: parseInt(values.eta) || 3
                    })
                })
                .then(res => res.json())
                .then(() => window.location.reload())
                .catch(() => window.location.reload());
            });
        }

        // Quick False Alarm handling from HUD
        function quickFalseAlarmCurrentIncident() {
            const incident = window.activeEmergencyIncidents[window.currentEmergencyIndex];
            if (!incident) return;

            const card = document.getElementById(`incident-card-${incident.id}`);
            if (card) {
                const falseBtn = document.getElementById(`open-false-alarm-modal-${incident.id}`);
                if (falseBtn) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    falseBtn.click();
                    return;
                }
            }

            window.showConfirmDialog({
                title: 'Mark as False Alarm',
                message: 'Classify this incident as a False Alarm / Accidental Trigger.',
                confirmText: 'Mark False Alarm',
                type: 'warning',
                fields: [
                    { name: 'reason', label: 'Reason (e.g. Accidental Push, Sensor Glitch, Campus Drill)', value: 'Accidental Push' }
                ]
            }).then(values => {
                if (!values) return;
                fetch(`/ndrrmo/incidents/${incident.id}/resolve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        resolution_type: 'False Alarm',
                        false_alarm_reason: values.reason || 'Accidental Push',
                        remarks: 'Marked as false alarm via Command HUD'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    window.activeEmergencyIncidents = window.activeEmergencyIncidents.filter(i => i.id !== incident.id);
                    if (window.activeEmergencyIncidents.length === 0) {
                        closeEmergencyModal();
                    } else {
                        renderActiveEmergencyModal();
                    }
                    window.location.reload();
                })
                .catch(() => window.location.reload());
            });
        }

        // Auto unlock AudioContext & SpeechSynthesis on any user click/tap
        function unlockAudioContext() {
            if (webAudioCtx && webAudioCtx.state === 'suspended') {
                webAudioCtx.resume();
            }
            if ('speechSynthesis' in window && window.speechSynthesis.paused) {
                window.speechSynthesis.resume();
            }
        }
        ['click', 'pointerdown', 'keydown', 'touchstart'].forEach(evt => {
            document.addEventListener(evt, unlockAudioContext, { passive: true });
        });

        function startEmergencySirenAudio() {
            if (isSirenActive || window.isSirenMuted) return;
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!webAudioCtx) {
                    webAudioCtx = new AudioContext();
                }
                if (webAudioCtx.state === 'suspended') {
                    webAudioCtx.resume();
                }

                sirenOscillator = webAudioCtx.createOscillator();
                sirenGainNode = webAudioCtx.createGain();

                sirenOscillator.type = 'sawtooth';
                sirenGainNode.gain.setValueAtTime(0.35, webAudioCtx.currentTime);

                sirenOscillator.connect(sirenGainNode);
                sirenGainNode.connect(webAudioCtx.destination);

                let highPitch = true;
                sirenOscillator.frequency.setValueAtTime(900, webAudioCtx.currentTime);
                sirenOscillator.start();
                isSirenActive = true;

                sirenInterval = setInterval(() => {
                    if (!webAudioCtx || !sirenOscillator) return;
                    if (webAudioCtx.state === 'suspended') webAudioCtx.resume();
                    const freq = highPitch ? 600 : 960;
                    sirenOscillator.frequency.exponentialRampToValueAtTime(freq, webAudioCtx.currentTime + 0.2);
                    highPitch = !highPitch;
                }, 300);
            } catch (err) {
                console.error("Web Audio Siren error:", err);
            }
        }

        function stopEmergencySirenAudio() {
            isSirenActive = false;
            if (sirenInterval) { clearInterval(sirenInterval); sirenInterval = null; }
            if (sirenOscillator) {
                try { sirenOscillator.stop(); sirenOscillator.disconnect(); } catch (e) {}
                sirenOscillator = null;
            }
            if (webAudioCtx) {
                try { webAudioCtx.close(); } catch (e) {}
                webAudioCtx = null;
            }
        }

        function speakEmergencyAnnouncement(text) {
            if (window.isSirenMuted) return;
            if (!('speechSynthesis' in window)) return;
            window.speechSynthesis.cancel();
            const msg = new SpeechSynthesisUtterance(text);
            msg.rate = 1.0;
            msg.pitch = 1.1;
            msg.volume = 1.0;

            msg.onend = function() {
                if (!window.isSirenMuted && window.activeEmergencyIncidents && window.activeEmergencyIncidents.length > 0) {
                    setTimeout(() => {
                        if (!window.isSirenMuted && window.activeEmergencyIncidents && window.activeEmergencyIncidents.length > 0) {
                            window.speechSynthesis.speak(msg);
                        }
                    }, 1500);
                }
            };

            window.speechSynthesis.speak(msg);
        }

        function stopVoiceSpeech() {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
            }
        }

        function acknowledgeActiveEmergency() {
            if (isAckInProgress || !window.activeEmergencyIncidents || window.activeEmergencyIncidents.length === 0) return;
            isAckInProgress = true;

            const targetIncident = window.activeEmergencyIncidents[window.currentEmergencyIndex];
            if (!targetIncident) {
                isAckInProgress = false;
                return;
            }

            const targetId = targetIncident.id;

            fetch(`/ndrrmo/incidents/${targetId}/acknowledge`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                console.log("[ACKNOWLEDGED] Alert acknowledged:", data);
                window.activeEmergencyIncidents = window.activeEmergencyIncidents.filter(i => i.id !== targetId);

                if (window.activeEmergencyIncidents.length === 0) {
                    closeEmergencyModal();
                    window.location.reload();
                } else {
                    if (window.currentEmergencyIndex >= window.activeEmergencyIncidents.length) {
                        window.currentEmergencyIndex = 0;
                    }
                    renderActiveEmergencyModal();
                    speakEmergencyAnnouncement(`Alert acknowledged. Warning! ${window.activeEmergencyIncidents.length} unhandled emergency alerts remain!`);
                }
            })
            .catch(err => {
                console.error("Ack error:", err);
                window.location.reload();
            })
            .finally(() => {
                isAckInProgress = false;
            });
        }

        function acknowledgeAllActiveEmergencies() {
            if (isAckInProgress) return;
            isAckInProgress = true;

            fetch('/ndrrmo/alerts/acknowledge-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                console.log("[ACKNOWLEDGED ALL] All alerts acknowledged:", data);
                window.activeEmergencyIncidents = [];
                closeEmergencyModal();
                window.location.reload();
            })
            .catch(err => {
                console.error("Ack all error:", err);
                window.location.reload();
            })
            .finally(() => {
                isAckInProgress = false;
            });
        }

        function closeEmergencyModal() {
            stopEmergencySirenAudio();
            stopVoiceSpeech();

            const perimeter = document.getElementById('global-emergency-perimeter');
            if (perimeter) perimeter.classList.add('hidden');

            const overlay = document.getElementById('global-emergency-overlay');
            if (overlay) {
                overlay.classList.add('hidden');
                overlay.classList.remove('block');
            }
        }
    </script>

    <script type="module">
        window.updateNDRRMOAlertBadges = function(count) {
            const sidebarBadge = document.getElementById('ndrrmo-sidebar-alert-badge');
            const headerBadge = document.getElementById('ndrrmo-header-notification-badge');

            const navAlerts = document.getElementById('nav-ndrrmo-alerts');
            const navMap = document.getElementById('nav-ndrrmo-map');

            [sidebarBadge, headerBadge].forEach(badge => {
                if (badge) {
                    badge.textContent = count;
                    if (count > 0) {
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            });

            [navAlerts, navMap].forEach(nav => {
                if (nav) {
                    if (count > 0) {
                        nav.classList.add('nav-alert-blinking');
                    } else {
                        nav.classList.remove('nav-alert-blinking');
                    }
                }
            });
        };

        window.Echo?.channel('emergencies')
            .listen('EmergencyReported', (e) => {
                if (e && e.incident) {
                    triggerScreenFlashAndAlarm(e.incident);

                    if (e.incident.status === 'Pending') {
                        const sidebarBadge = document.getElementById('ndrrmo-sidebar-alert-badge');
                        let currentCount = parseInt(sidebarBadge ? sidebarBadge.textContent || '0' : '0');
                        if (isNaN(currentCount)) currentCount = 0;
                        window.updateNDRRMOAlertBadges(currentCount + 1);
                    }
                }
            });
    </script>
    {{-- Custom Action Confirmation Dialog Modal --}}
    <div id="custom-confirm-modal" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-200 select-none">
        <div class="relative w-full max-w-sm bg-white border border-slate-200 rounded-3xl shadow-2xl overflow-hidden p-6 text-center transform transition-all scale-100">
            <div id="confirm-icon-wrapper" class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center bg-red-100 text-red-600 shadow-sm border border-red-200">
                <svg id="confirm-modal-icon" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <h3 id="confirm-modal-title" class="text-lg font-black text-slate-900 mb-1">
                Confirm Action
            </h3>

            <p id="confirm-modal-message" class="text-slate-600 font-medium text-xs mb-6 leading-relaxed">
                Are you sure you want to proceed with this action?
            </p>
            <div id="confirm-modal-fields" class="hidden text-left space-y-3 mb-6"></div>

            <div class="flex items-center gap-3">
                <button id="confirm-modal-cancel-btn" type="button"
                        class="flex-1 py-3 px-4 rounded-xl font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 active:scale-95 transition-all text-xs cursor-pointer border border-slate-200">
                    Cancel
                </button>
                <button id="confirm-modal-submit-btn" type="button"
                        class="flex-1 py-3 px-4 rounded-xl font-extrabold text-white bg-red-600 hover:bg-red-700 active:scale-95 transition-all text-xs shadow-md shadow-red-200 cursor-pointer">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <script>
        window.showConfirmDialog = function({
            title = 'Confirm Action',
            message = 'Are you sure you want to proceed?',
            confirmText = 'Confirm',
            cancelText = 'Cancel',
            type = 'danger',
            fields = []
        } = {}) {
            return new Promise((resolve) => {
                const modal = document.getElementById('custom-confirm-modal');
                const titleEl = document.getElementById('confirm-modal-title');
                const msgEl = document.getElementById('confirm-modal-message');
                const submitBtn = document.getElementById('confirm-modal-submit-btn');
                const cancelBtn = document.getElementById('confirm-modal-cancel-btn');
                const iconWrapper = document.getElementById('confirm-icon-wrapper');

                if (!modal) {
                    resolve(true);
                    return;
                }

                if (titleEl) titleEl.textContent = title;
                if (msgEl) msgEl.textContent = message;
                if (submitBtn) submitBtn.textContent = confirmText;
                if (cancelBtn) cancelBtn.textContent = cancelText;

                if (type === 'danger') {
                    if (iconWrapper) iconWrapper.className = 'w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center bg-red-100 text-red-600 shadow-sm border border-red-200';
                    if (submitBtn) submitBtn.className = 'flex-1 py-3 px-4 rounded-xl font-extrabold text-white bg-red-600 hover:bg-red-700 active:scale-95 transition-all text-xs shadow-md shadow-red-200 cursor-pointer';
                } else if (type === 'warning') {
                    if (iconWrapper) iconWrapper.className = 'w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center bg-amber-100 text-amber-600 shadow-sm border border-amber-200';
                    if (submitBtn) submitBtn.className = 'flex-1 py-3 px-4 rounded-xl font-extrabold text-white bg-amber-600 hover:bg-amber-700 active:scale-95 transition-all text-xs shadow-md shadow-amber-200 cursor-pointer';
                } else {
                    if (iconWrapper) iconWrapper.className = 'w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center bg-blue-100 text-blue-600 shadow-sm border border-blue-200';
                    if (submitBtn) submitBtn.className = 'flex-1 py-3 px-4 rounded-xl font-extrabold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 transition-all text-xs shadow-md shadow-blue-200 cursor-pointer';
                }

                const fieldsEl = document.getElementById('confirm-modal-fields');
                if (fieldsEl) {
                    fieldsEl.innerHTML = '';
                    fieldsEl.classList.toggle('hidden', fields.length === 0);
                    fields.forEach(f => {
                        const wrap = document.createElement('label');
                        wrap.className = 'block text-[11px] font-bold text-slate-600';
                        wrap.textContent = f.label;
                        const input = document.createElement('input');
                        input.type = f.type || 'text';
                        input.name = f.name;
                        input.value = f.value ?? '';
                        if (f.min !== undefined) input.min = f.min;
                        input.className = 'mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500';
                        wrap.appendChild(input);
                        fieldsEl.appendChild(wrap);
                    });
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                const firstInput = fieldsEl && fieldsEl.querySelector('input');
                if (firstInput) { firstInput.focus(); firstInput.select(); }

                function cleanup() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    submitBtn.removeEventListener('click', onConfirm);
                    cancelBtn.removeEventListener('click', onCancel);
                }

                function onConfirm() {
                    const values = {};
                    if (fieldsEl) fieldsEl.querySelectorAll('input').forEach(i => values[i.name] = i.value.trim());
                    cleanup();
                    resolve(fields.length ? values : true);
                }

                function onCancel() {
                    cleanup();
                    resolve(false);
                }

                submitBtn.addEventListener('click', onConfirm);
                cancelBtn.addEventListener('click', onCancel);
            });
        };

        window.confirmAction = function(event, message, title = 'Confirm Action', confirmText = 'Confirm', type = 'danger') {
            event.preventDefault();
            const target = event.currentTarget || event.target;
            const form = target.closest('form') || target;
            window.showConfirmDialog({ title, message, confirmText, type }).then(confirmed => {
                if (confirmed) {
                    form.submit();
                }
            });
            return false;
        };

        function updateLiveNDRRMOClock() {
            const now = new Date();
            const dateEl = document.getElementById('ndrrmo-header-date');
            const timeEl = document.getElementById('ndrrmo-header-time');
            if (dateEl) {
                dateEl.textContent = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            }
            if (timeEl) {
                timeEl.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
            }
        }
        updateLiveNDRRMOClock();
        setInterval(updateLiveNDRRMOClock, 1000);

        function pollNDRRMOStats() {
            fetch('/ndrrmo/stats-json')
                .then(res => res.json())
                .then(data => {
                    const activeEl = document.getElementById('ndrrmo-stat-active');
                    const totalEl = document.getElementById('ndrrmo-stat-total');
                    const resolvedEl = document.getElementById('ndrrmo-stat-resolved');
                    const devicesEl = document.getElementById('ndrrmo-stat-devices');
                    const subtitleEl = document.getElementById('ndrrmo-stat-devices-subtitle');
                    
                    if (activeEl && data.active_alerts !== undefined) activeEl.textContent = data.active_alerts;
                    if (totalEl && data.total_incidents !== undefined) totalEl.textContent = data.total_incidents;
                    if (resolvedEl && data.resolved_incidents !== undefined) resolvedEl.textContent = data.resolved_incidents;
                    if (devicesEl && data.devices_online !== undefined && data.total_devices !== undefined) {
                        devicesEl.textContent = `${data.devices_online} / ${data.total_devices}`;
                        if (subtitleEl) {
                            subtitleEl.textContent = data.devices_online > 0 ? 'Devices operational' : 'No devices online';
                        }
                    }

                    if (window.updateNDRRMOAlertBadges && data.active_alerts !== undefined) {
                        window.updateNDRRMOAlertBadges(data.active_alerts);
                    }

                    // Auto-sync emergency modal & audio siren with all pending incidents
                    if (data.pending_incidents && window.syncEmergencyIncidents) {
                        window.syncEmergencyIncidents(data.pending_incidents);
                    } else if (data.latest_pending && window.triggerScreenFlashAndAlarm) {
                        window.triggerScreenFlashAndAlarm(data.latest_pending);
                    } else if (data.active_alerts === 0 && window.activeEmergencyIncidents && window.activeEmergencyIncidents.length > 0) {
                        window.syncEmergencyIncidents([]);
                    }
                })
                .catch(err => console.error(err));
        }
        pollNDRRMOStats();
        setInterval(pollNDRRMOStats, 1500);
    </script>
</body>
</html>
