<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Dashboard</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: {
                            bg: '#F8FAFC',
                            sidebar: '#FFFFFF',
                            card: '#FFFFFF',
                            border: '#CBD5E1',
                            blue: '#2563EB',
                            red: '#DC2626',
                            orange: '#D97706',
                            green: '#16A34A',
                            teal: '#0D9488',
                            text: '#000000',
                            dark: '#000000',
                            hover: '#F1F5F9'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body {
            background: linear-gradient(135deg, #dbeafe 0%, #f0f4ff 40%, #e0f2fe 100%);
            color: #000000;
            font-family: 'Inter', sans-serif;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #93c5fd; border-radius: 4px; }

        /* Glass sidebar */
        .glass-sidebar {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: 6px 0 32px rgba(59, 130, 246, 0.1), inset 0 0 0 1px rgba(255,255,255,0.6);
        }

        .pulse-ring {
            animation: pulse-ring 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 0.5; }
            80%, 100% { transform: scale(1.5); opacity: 0; }
        }
    </style>
</head>
<body class="h-screen flex overflow-hidden text-sm p-3 gap-3 text-black">

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

            {{-- Nav label --}}
            <p class="text-[10px] font-black text-black uppercase tracking-widest px-3 mb-1 mt-2">Navigation</p>

            <a href="{{ route('clinic.dashboard') }}" class="flex items-center px-3.5 py-2.5 {{ request()->routeIs('clinic.dashboard') ? 'bg-brand-blue text-white shadow-md shadow-blue-300/50' : 'text-black font-bold hover:bg-white hover:text-brand-blue hover:shadow-sm hover:translate-x-1 border border-transparent hover:border-slate-200' }} rounded-xl transition-all duration-200 group">
                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="font-bold text-[13px]">Dashboard</span>
            </a>

            <a href="{{ route('clinic.alerts') }}" class="flex items-center justify-between px-3.5 py-2.5 {{ request()->routeIs('clinic.alerts') ? 'bg-brand-blue text-white shadow-md shadow-blue-300/50' : 'text-black font-bold hover:bg-white hover:text-brand-blue hover:shadow-sm hover:translate-x-1 border border-transparent hover:border-slate-200' }} rounded-xl transition-all duration-200 group">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('clinic.alerts') ? 'text-white' : 'text-brand-red group-hover:text-brand-blue' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="font-bold text-[13px] {{ request()->routeIs('clinic.alerts') ? 'text-white' : 'text-brand-red group-hover:text-brand-blue' }}">Emergency Alerts</span>
                </div>
                <span id="sidebar-alert-badge" class="bg-brand-red text-white text-[9px] font-extrabold px-1.5 py-0.5 rounded-full animate-pulse {{ ($activeAlertsCount ?? 0) > 0 ? '' : 'hidden' }}">{{ $activeAlertsCount ?? 0 }}</span>
            </a>

            <a href="{{ route('clinic.incoming') }}" class="flex items-center px-3.5 py-2.5 {{ request()->routeIs('clinic.incoming') ? 'bg-brand-blue text-white shadow-md shadow-blue-300/50' : 'text-black font-bold hover:bg-white hover:text-brand-blue hover:shadow-sm hover:translate-x-1 border border-transparent hover:border-slate-200' }} rounded-xl transition-all duration-200 group">
                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                <span class="font-bold text-[13px]">Incoming Patients</span>
            </a>

            <a href="{{ route('clinic.logs') }}" class="flex items-center px-3.5 py-2.5 {{ request()->routeIs('clinic.logs') ? 'bg-brand-blue text-white shadow-md shadow-blue-300/50' : 'text-black font-bold hover:bg-white hover:text-brand-blue hover:shadow-sm hover:translate-x-1 border border-transparent hover:border-slate-200' }} rounded-xl transition-all duration-200 group">
                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span class="font-bold text-[13px]">Incident Logs</span>
            </a>

            <a href="{{ route('clinic.patients') }}" class="flex items-center px-3.5 py-2.5 {{ request()->routeIs('clinic.patients') ? 'bg-brand-blue text-white shadow-md shadow-blue-300/50' : 'text-black font-bold hover:bg-white hover:text-brand-blue hover:shadow-sm hover:translate-x-1 border border-transparent hover:border-slate-200' }} rounded-xl transition-all duration-200 group">
                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="font-bold text-[13px]">Patient Directory</span>
            </a>

            <a href="{{ route('clinic.reports') }}" class="flex items-center px-3.5 py-2.5 {{ request()->routeIs('clinic.reports') ? 'bg-brand-blue text-white shadow-md shadow-blue-300/50' : 'text-black font-bold hover:bg-white hover:text-brand-blue hover:shadow-sm hover:translate-x-1 border border-transparent hover:border-slate-200' }} rounded-xl transition-all duration-200 group">
                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span class="font-bold text-[13px]">Clinic Reports</span>
            </a>

            <p class="text-[10px] font-black text-black uppercase tracking-widest px-3 mb-1 mt-3">Testing & Tools</p>
            <a href="{{ route('simulator') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2.5 text-slate-800 font-bold hover:bg-white hover:text-amber-600 hover:shadow-sm hover:translate-x-1 border border-transparent hover:border-slate-200 rounded-xl transition-all duration-200 group">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-amber-500 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span class="font-bold text-[13px]">ESP32 Simulator</span>
                </div>
                <span class="bg-amber-100 text-amber-800 border border-amber-300 text-[9px] font-black px-1.5 py-0.5 rounded-md">LIVE</span>
            </a>
        </div>

        <div class="p-3 border-t border-slate-200/80 mt-auto">
            <form method="POST" action="{{ route('logout') }}" onsubmit="return confirmAction(event, 'Are you sure you want to log out of your Clinic session?', 'Confirm Logout', 'Logout', 'danger')">
                @csrf
                <button type="submit" class="flex items-center w-full px-3.5 py-2.5 text-brand-red font-bold hover:text-white hover:bg-red-600 hover:shadow-md hover:shadow-red-200 hover:translate-x-1 rounded-xl transition-all duration-200 group cursor-pointer">
                    <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span class="font-bold text-[13px]">Logout</span>
                </button>
            </form>
            <p class="mt-3 text-[10px] font-bold text-black text-center">© 2025 JHCSC. All rights reserved.</p>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0 overflow-hidden rounded-2xl bg-white/60 backdrop-blur-sm border border-white/80 shadow-sm text-black">
        <!-- Header -->
        <header class="h-16 bg-white/80 border-b border-slate-200 flex items-center justify-between px-6 shrink-0 z-10 text-black">
            <div class="flex items-baseline space-x-2">
                <h1 class="text-lg font-black text-black uppercase tracking-wider">CLINIC DASHBOARD</h1>

            </div>
            
            <div class="flex items-center space-x-6">
                <div class="flex items-center text-xs font-bold text-black">
                    <span class="w-2.5 h-2.5 rounded-full bg-brand-green mr-2 shadow-[0_0_8px_rgba(22,163,74,0.8)]"></span>
                    <span class="text-black font-bold">System Online</span>
                </div>
                
                <div class="flex items-center text-xs font-bold text-black border-l border-slate-300 pl-6">
                    <svg class="w-4 h-4 mr-2 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span id="header-date">--</span>
                    <span class="mx-2">|</span>
                    <span id="header-time" class="tabular-nums">--</span>
                </div>

                <div class="flex items-center pl-4 border-l border-slate-300 space-x-4">
                    {{-- Header Emergency Notifications Bell with Dropdown --}}
                    <div class="relative" x-data="{ notifOpen: false }">
                        <button @click="notifOpen = !notifOpen" type="button" class="relative p-2 rounded-xl text-slate-700 hover:text-brand-blue hover:bg-slate-100 transition-colors cursor-pointer" title="Emergency Notifications">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span id="header-notification-badge" class="absolute top-1 right-1 min-w-[18px] h-4 px-1 bg-brand-red rounded-full text-[9px] font-black flex items-center justify-center text-white {{ ($activeAlertsCount ?? 0) > 0 ? '' : 'hidden' }}">{{ $activeAlertsCount ?? 0 }}</span>
                        </button>

                        <div x-show="notifOpen" @click.away="notifOpen = false" x-cloak style="display: none;" class="absolute right-0 mt-2 w-80 sm:w-96 bg-white border border-slate-200 rounded-2xl shadow-2xl py-3 z-50">
                            <div class="px-4 py-2 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full {{ ($activeAlertsCount ?? 0) > 0 ? 'bg-red-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                                    <span class="text-xs font-black text-slate-900 uppercase tracking-wider">Emergency Notifications</span>
                                </div>
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full {{ ($activeAlertsCount ?? 0) > 0 ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $activeAlertsCount ?? 0 }} Pending
                                </span>
                            </div>

                            <div class="p-3">
                                @if(($activeAlertsCount ?? 0) > 0)
                                    <div class="p-3 bg-red-50 border border-red-200 rounded-xl mb-2 flex items-center justify-between">
                                        <div>
                                            <p class="text-xs font-black text-red-700 uppercase">Active Medical Alerts</p>
                                            <p class="text-[11px] text-red-600 font-semibold mt-0.5">{{ $activeAlertsCount }} incident(s) require attention</p>
                                        </div>
                                        <a href="{{ route('clinic.alerts') }}" class="px-2.5 py-1 bg-red-600 hover:bg-red-700 text-white text-[11px] font-extrabold rounded-lg shadow-xs transition-colors">
                                            View Alerts
                                        </a>
                                    </div>
                                @else
                                    <div class="py-6 text-center text-slate-500">
                                        <div class="w-10 h-10 mx-auto mb-2 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center font-black">✓</div>
                                        <p class="text-xs font-bold text-slate-700">All clear</p>
                                        <p class="text-[11px] text-slate-500">No active medical emergencies</p>
                                    </div>
                                @endif
                            </div>

                            <div class="border-t border-slate-100 px-4 pt-2 flex items-center justify-between text-[11px]">
                                <a href="{{ route('clinic.alerts') }}" class="text-brand-blue hover:underline font-extrabold">Emergency Alerts Feed →</a>
                                <a href="{{ route('clinic.logs') }}" class="text-slate-500 hover:text-slate-700 font-semibold">Incident Logs</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" type="button" class="flex items-center cursor-pointer focus:outline-none group">
                            <div class="w-8 h-8 rounded-full bg-slate-900 overflow-hidden mr-3 ring-2 ring-transparent group-hover:ring-blue-500 transition-all">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->fullname ?? auth()->user()->username ?? 'Admin') }}&background=000000&color=fff" alt="{{ auth()->user()->fullname ?? 'Admin' }}" class="w-full h-full object-cover">
                            </div>
                            <div class="hidden md:block text-left">
                                <div class="text-xs font-bold text-black leading-none mb-1">{{ auth()->user()->fullname ?? 'Clinic Admin' }}</div>
                                <div class="text-[11px] font-semibold text-slate-600 leading-none">{{ auth()->user()->role ?? 'Administrator' }}</div>
                            </div>
                            <svg class="w-4 h-4 ml-2 text-black transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak style="display: none;" class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-50">
                            <a href="{{ route('clinic.profile') }}" class="flex items-center px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-blue-600 transition-colors">
                                <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                My Profile
                            </a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}" onsubmit="return confirmAction(event, 'Are you sure you want to log out of your Clinic session?', 'Confirm Logout', 'Logout', 'danger')">
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
        <div class="flex-1 overflow-y-auto custom-scrollbar p-6 bg-slate-50/50 text-slate-800 flex flex-col">
@yield("content")
        </div>
    </main>

    <!-- Leaflet CSS & JS for Clinic Emergency Modal Location Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Scripts -->
    {{-- Viewport Perimeter Warning (Ambient Soft Vignette - Non-blocking) --}}
    <div id="global-emergency-perimeter" class="fixed inset-0 pointer-events-none z-[9980] shadow-[inset_0_0_80px_rgba(220,38,38,0.35)] ring-2 ring-inset ring-red-500/40 animate-pulse hidden transition-all"></div>

    {{-- Non-Blocking Top-Right Sleek Emergency Broadcast Toast --}}
    <div id="global-emergency-overlay" class="fixed top-4 right-4 z-[9990] w-full max-w-md sm:max-w-lg px-2 pointer-events-none hidden transition-all duration-300">
        <div id="global-emergency-modal" class="pointer-events-auto bg-white/95 backdrop-blur-md border border-red-500/80 rounded-2xl shadow-2xl overflow-hidden transition-all flex flex-col">
            
            {{-- Top Ribbon Bar --}}
            <div id="hud-header" class="bg-red-600 px-4 py-2.5 flex items-center justify-between text-white shrink-0">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-white animate-ping shrink-0"></span>
                    <span id="global-emergency-category-badge" class="font-black text-xs uppercase tracking-wider truncate">🚑 CLINIC MEDICAL ALERT</span>
                    <span id="clinic-hud-multi-counter" class="hidden bg-black/30 text-white text-[10px] font-black px-2 py-0.5 rounded-full shrink-0">Alert 1 of 1</span>
                </div>
                
                <div class="flex items-center gap-1.5 shrink-0">
                    {{-- Mute Siren Toggle --}}
                    <button type="button" id="clinic-hud-mute-btn" onclick="toggleClinicMuteSiren()" title="Mute/Unmute Siren Audio" class="px-2 py-1 bg-black/25 hover:bg-black/45 text-white text-[10px] font-bold rounded-lg transition-all flex items-center gap-1 cursor-pointer">
                        <span id="clinic-hud-mute-icon">🔊</span>
                        <span id="clinic-hud-mute-text" class="hidden sm:inline">Mute Siren</span>
                    </button>
                    {{-- Fly to Map button --}}
                    <button type="button" id="clinic-hud-fly-map-btn" onclick="flyToCurrentClinicIncidentMap()" title="Locate on Dashboard" class="px-2 py-1 bg-white hover:bg-slate-100 text-slate-800 text-[10px] font-black rounded-lg transition-all flex items-center gap-1 cursor-pointer shadow-xs">
                        <span>📍</span>
                        <span class="hidden sm:inline">Locate</span>
                    </button>
                    {{-- Minimize/Expand toggle --}}
                    <button type="button" id="clinic-hud-toggle-minimize" onclick="toggleClinicHudMinimized()" title="Minimize / Expand Toast" class="w-6 h-6 flex items-center justify-center bg-black/25 hover:bg-black/40 text-white rounded-lg transition-all cursor-pointer text-xs">
                        <span id="clinic-hud-minimize-icon">▲</span>
                    </button>
                    {{-- Dismiss/Close button --}}
                    <button type="button" onclick="closeClinicModal()" title="Close Notification" class="w-6 h-6 flex items-center justify-center bg-black/25 hover:bg-black/40 text-white rounded-lg transition-all cursor-pointer text-xs">
                        <span>✕</span>
                    </button>
                </div>
            </div>

            {{-- Collapsible HUD Body --}}
            <div id="clinic-hud-body" class="p-3.5 flex flex-col gap-2.5">
                {{-- Multi-alert header banner (shown when > 1 unhandled alerts) --}}
                <div id="clinic-multi-alert-header" class="hidden p-2 bg-red-50 border border-red-200 rounded-xl flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5 min-w-0">
                        <span class="text-[11px] font-black text-red-700 uppercase truncate" id="clinic-multi-alert-count-text">2 UNHANDLED MEDICAL ALERTS</span>
                        <div id="clinic-multi-alert-tabs" class="flex items-center gap-1 overflow-x-auto custom-scrollbar"></div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" onclick="navigateClinicEmergency(-1)" class="px-2 py-0.5 bg-white hover:bg-slate-100 text-slate-800 text-[10px] font-black rounded border border-slate-300 cursor-pointer">◀</button>
                        <span id="clinic-multi-alert-step-text" class="text-[10px] font-black text-slate-700 px-1">1/2</span>
                        <button type="button" onclick="navigateClinicEmergency(1)" class="px-2 py-0.5 bg-white hover:bg-slate-100 text-slate-800 text-[10px] font-black rounded border border-slate-300 cursor-pointer">▶</button>
                    </div>
                </div>

                {{-- Alert Summary Row --}}
                <div class="bg-slate-50/90 border border-slate-200 rounded-xl p-3 flex items-start gap-3">
                    <div id="global-emergency-icon-box" class="w-10 h-10 rounded-xl bg-red-100 border border-red-300 text-red-600 flex items-center justify-center font-black text-lg shrink-0">
                        🚑
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <h3 id="global-emergency-title" class="font-black text-slate-900 text-xs uppercase truncate">MEDICAL EMERGENCY</h3>
                            <span id="clinic-hud-status-badge" class="px-1.5 py-0.5 rounded-full text-[9px] font-black bg-red-100 text-red-700 animate-pulse shrink-0">PENDING</span>
                        </div>
                        <p id="global-emergency-location" class="font-extrabold text-slate-900 text-xs truncate">Location loading...</p>
                        <p id="global-emergency-device" class="text-[10px] text-slate-500 font-mono truncate">Device ID loading...</p>
                    </div>
                </div>

                {{-- Actions on HUD --}}
                <div class="flex flex-wrap items-center justify-end gap-1.5 pt-1">
                    <button id="global-emergency-ack-btn" type="button" onclick="acknowledgeClinicEmergency()"
                            class="py-1.5 px-3 rounded-xl font-extrabold text-white text-xs shadow-xs bg-red-600 hover:bg-red-700 active:scale-95 transition-all flex items-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span id="clinic-emergency-ack-btn-text">ACKNOWLEDGE</span>
                    </button>

                    <button id="clinic-emergency-dispatch-btn" type="button" onclick="quickDispatchClinicIncident()"
                            class="py-1.5 px-3 rounded-xl font-extrabold text-white text-xs shadow-xs bg-blue-600 hover:bg-blue-700 active:scale-95 transition-all flex items-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>DISPATCH</span>
                    </button>

                    <button id="clinic-emergency-false-btn" type="button" onclick="quickFalseAlarmClinicIncident()"
                            class="py-1.5 px-2.5 rounded-xl font-bold text-slate-700 text-xs bg-white border border-slate-300 hover:bg-slate-100 active:scale-95 transition-all cursor-pointer">
                        <span>⚠️ False</span>
                    </button>

                    <button id="clinic-emergency-ack-all-btn" type="button" onclick="acknowledgeAllClinicEmergencies()"
                            class="hidden py-1.5 px-2.5 rounded-xl font-bold text-slate-800 text-xs bg-slate-200 hover:bg-slate-300 active:scale-95 transition-all cursor-pointer">
                        <span id="clinic-emergency-ack-all-text">ACK ALL</span>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script>
        window.activeClinicIncidents = [];
        window.currentClinicIndex = 0;
        let isClinicAckInProgress = false;
        let webAudioCtx = null;
        let sirenOscillator = null;
        let sirenGainNode = null;
        let sirenInterval = null;
        let isSirenActive = false;
        window.clinicModalMap = null;
        window.clinicModalMarker = null;
        window.clinicModalCircle = null;

        function triggerClinicFlashAndAlarm(incident) {
            if (!incident || !incident.id) return;
            const type = incident.emergency_type;
            if (!['Critical Emergency', 'Medical Emergency'].includes(type)) return;

            if (incident.status === 'Resolved' || incident.status !== 'Pending') {
                window.activeClinicIncidents = window.activeClinicIncidents.filter(i => i.id !== incident.id);
                if (window.activeClinicIncidents.length === 0) {
                    closeClinicModal();
                } else {
                    if (window.currentClinicIndex >= window.activeClinicIncidents.length) {
                        window.currentClinicIndex = 0;
                    }
                    renderActiveClinicModal();
                }

                // If on Clinic dashboard or incidents page, reload smoothly so stepper and badges reflect latest step
                if (window.location.pathname.startsWith('/clinic')) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 600);
                }
                return;
            }

            const existingIdx = window.activeClinicIncidents.findIndex(i => i.id === incident.id);
            if (existingIdx === -1) {
                window.activeClinicIncidents.push(incident);
                const loc = (incident.device && incident.device.building) ? incident.device.building : 'Campus';
                speakClinicAnnouncement(`Urgent Medical Emergency! ${type} detected at ${loc}! Clinic medical team respond immediately!`);
            } else {
                window.activeClinicIncidents[existingIdx] = incident;
            }
            renderActiveClinicModal();
        }

        function syncClinicIncidents(pendingIncidents) {
            if (!Array.isArray(pendingIncidents)) return;
            const clinicRelevant = pendingIncidents.filter(inc => ['Critical Emergency', 'Medical Emergency'].includes(inc.emergency_type));

            if (clinicRelevant.length === 0) {
                if (window.activeClinicIncidents.length > 0) {
                    window.activeClinicIncidents = [];
                    closeClinicModal();
                }
                return;
            }

            const currentIds = window.activeClinicIncidents.map(i => i.id).sort().join(',');
            const newIds = clinicRelevant.map(i => i.id).sort().join(',');

            if (currentIds !== newIds) {
                const hadZero = window.activeClinicIncidents.length === 0;
                window.activeClinicIncidents = [...clinicRelevant];
                if (window.currentClinicIndex >= window.activeClinicIncidents.length) {
                    window.currentClinicIndex = Math.max(0, window.activeClinicIncidents.length - 1);
                }
                renderActiveClinicModal();

                if (hadZero && window.activeClinicIncidents.length > 0) {
                    const first = window.activeClinicIncidents[0];
                    const type = first.emergency_type || 'Medical Emergency';
                    const loc = (first.device && first.device.building) ? first.device.building : 'Campus';
                    speakClinicAnnouncement(`Urgent Medical Emergency! ${type} detected at ${loc}! Clinic medical team respond immediately!`);
                }
            }
        }

        function navigateClinicEmergency(step) {
            if (window.activeClinicIncidents.length <= 1) return;
            window.currentClinicIndex = (window.currentClinicIndex + step + window.activeClinicIncidents.length) % window.activeClinicIncidents.length;
            renderActiveClinicModal();
        }

        function selectClinicEmergencyIndex(idx) {
            if (idx >= 0 && idx < window.activeClinicIncidents.length) {
                window.currentClinicIndex = idx;
                renderActiveClinicModal();
            }
        }

        function renderActiveClinicModal() {
            if (!window.activeClinicIncidents || window.activeClinicIncidents.length === 0) {
                closeClinicModal();
                return;
            }

            if (window.currentClinicIndex >= window.activeClinicIncidents.length) {
                window.currentClinicIndex = Math.max(0, window.activeClinicIncidents.length - 1);
            }

            const total = window.activeClinicIncidents.length;
            const currentIdx = window.currentClinicIndex;
            const incident = window.activeClinicIncidents[currentIdx];
            if (!incident) return;

            const type = incident.emergency_type || 'Medical Emergency';
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
            const ackBtnText = document.getElementById('clinic-emergency-ack-btn-text');
            const ackAllBtn = document.getElementById('clinic-emergency-ack-all-btn');
            const ackAllText = document.getElementById('clinic-emergency-ack-all-text');
            const mapContainer = document.getElementById('clinic-modal-map-container');
            const coordsText = document.getElementById('clinic-modal-map-coords-text');

            const multiHeader = document.getElementById('clinic-multi-alert-header');
            const multiCountText = document.getElementById('clinic-multi-alert-count-text');
            const multiStepText = document.getElementById('clinic-multi-alert-step-text');
            const multiTabs = document.getElementById('clinic-multi-alert-tabs');
            const perimeter = document.getElementById('global-emergency-perimeter');
            const hudCounter = document.getElementById('clinic-hud-multi-counter');
            const hudHeader = document.getElementById('hud-header');
            const statusBadge = document.getElementById('clinic-hud-status-badge');

            let headerBg = 'bg-orange-600';
            let badgeBg = 'bg-orange-600';
            let borderCls = 'border-orange-500';

            if (type.includes('Critical')) {
                headerBg = 'bg-red-600';
                badgeBg = 'bg-red-600';
                borderCls = 'border-red-600';
            }

            if (perimeter) perimeter.classList.remove('hidden');

            if (modal) {
                modal.className = `pointer-events-auto bg-white/95 backdrop-blur-md border-2 ${borderCls} rounded-2xl shadow-2xl overflow-hidden transition-all flex flex-col`;
            }
            if (hudHeader) {
                hudHeader.className = `${headerBg} px-4 py-2.5 flex items-center justify-between text-white shrink-0`;
            }
            if (badge) {
                badge.textContent = `🚑 ${type.toUpperCase()}`;
            }
            if (title) title.textContent = type.toUpperCase();
            if (locEl) locEl.textContent = location;
            if (devEl) devEl.textContent = `Device ID: ${deviceCode} • Reported ${incident.reported_at ? new Date(incident.reported_at).toLocaleTimeString() : 'Just now'}`;
            if (statusBadge) {
                statusBadge.textContent = (incident.status || 'PENDING').toUpperCase();
            }

            // Multi-alert UI controls
            if (total > 1) {
                if (multiHeader) multiHeader.classList.remove('hidden');
                if (hudCounter) {
                    hudCounter.classList.remove('hidden');
                    hudCounter.textContent = `Alert ${currentIdx + 1} of ${total}`;
                }
                if (multiCountText) multiCountText.textContent = `${total} UNHANDLED MEDICAL ALERTS`;
                if (multiStepText) multiStepText.textContent = `${currentIdx + 1}/${total}`;

                if (multiTabs) {
                    multiTabs.innerHTML = '';
                    window.activeClinicIncidents.forEach((inc, idx) => {
                        const tabBtn = document.createElement('button');
                        tabBtn.type = 'button';
                        tabBtn.onclick = () => selectClinicEmergencyIndex(idx);
                        const bld = (inc.device && inc.device.building) ? inc.device.building : 'Alert';
                        const isCurrent = idx === currentIdx;
                        tabBtn.className = isCurrent
                            ? `px-2.5 py-0.5 rounded-lg text-[11px] font-black text-white ${badgeBg} shadow-xs shrink-0 ring-1 ring-white cursor-pointer`
                            : 'px-2.5 py-0.5 rounded-lg text-[11px] font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 shrink-0 cursor-pointer';
                        tabBtn.textContent = `#${idx + 1}: ${bld}`;
                        multiTabs.appendChild(tabBtn);
                    });
                }

                if (ackBtnText) ackBtnText.textContent = `ACK THIS (#${currentIdx + 1})`;
                if (ackAllBtn) ackAllBtn.classList.remove('hidden');
                if (ackAllText) ackAllText.textContent = `ACK ALL (${total})`;
            } else {
                if (multiHeader) multiHeader.classList.add('hidden');
                if (hudCounter) hudCounter.classList.add('hidden');
                if (ackBtnText) ackBtnText.textContent = 'ACKNOWLEDGE';
                if (ackAllBtn) ackAllBtn.classList.add('hidden');
            }

            if (overlay) {
                overlay.classList.remove('hidden');
                overlay.classList.add('block');
            }

            // Render / Re-center Leaflet Map
            if (hasCoordinates && typeof L !== 'undefined') {
                setTimeout(() => {
                    const container = document.getElementById('clinic-modal-emergency-map');
                    if (!container) return;

                    if (!window.clinicModalMap) {
                        container.innerHTML = '';
                        const modalMap = L.map('clinic-modal-emergency-map', { zoomControl: false, attributionControl: false }).setView([lat, lng], 18);
                        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                            maxZoom: 19
                        }).addTo(modalMap);

                        const emergencyIcon = L.divIcon({
                            className: 'custom-marker',
                            html: `
                                <div class="relative flex items-center justify-center w-10 h-10">
                                    <span class="absolute w-full h-full rounded-full bg-red-600 animate-ping opacity-85"></span>
                                    <div class="relative z-10 w-9 h-9 rounded-full bg-red-600 text-white flex items-center justify-center shadow-lg border-2 border-white font-black text-sm">
                                        🚨
                                    </div>
                                </div>
                            `,
                            iconSize: [40, 40],
                            iconAnchor: [20, 20],
                            popupAnchor: [0, -20]
                        });

                        window.clinicModalMarker = L.marker([lat, lng], { icon: emergencyIcon }).addTo(modalMap);
                        window.clinicModalMarker.bindPopup(`<div class="p-2 font-black text-xs text-red-600">🚨 EMERGENCY ACTIVE<br><span class="text-slate-900 font-bold">${location} (${deviceCode})</span></div>`).openPopup();

                        window.clinicModalCircle = L.circle([lat, lng], {
                            color: '#dc2626',
                            fillColor: '#ef4444',
                            fillOpacity: 0.4,
                            radius: 30
                        }).addTo(modalMap);

                        window.clinicModalMap = modalMap;
                    } else {
                        window.clinicModalMap.setView([lat, lng], 18);
                        if (window.clinicModalMarker) {
                            window.clinicModalMarker.setLatLng([lat, lng]);
                            window.clinicModalMarker.setPopupContent(`<div class="p-2 font-black text-xs text-red-600">🚨 EMERGENCY ACTIVE<br><span class="text-slate-900 font-bold">${location} (${deviceCode})</span></div>`).openPopup();
                        }
                        if (window.clinicModalCircle) {
                            window.clinicModalCircle.setLatLng([lat, lng]);
                        }
                    }
                    setTimeout(() => {
                        if (window.clinicModalMap) window.clinicModalMap.invalidateSize();
                    }, 300);
                }, 100);
            }

            startClinicSirenAudio();
        }

        function unlockClinicAudio() {
            if (webAudioCtx && webAudioCtx.state === 'suspended') {
                webAudioCtx.resume();
            }
            if ('speechSynthesis' in window && window.speechSynthesis.paused) {
                window.speechSynthesis.resume();
            }
        }
        ['click', 'pointerdown', 'keydown', 'touchstart'].forEach(evt => {
            document.addEventListener(evt, unlockClinicAudio, { passive: true });
        });

        // Toggle Mute Siren Audio
        window.isClinicSirenMuted = false;
        function toggleClinicMuteSiren() {
            window.isClinicSirenMuted = !window.isClinicSirenMuted;
            const muteIcon = document.getElementById('clinic-hud-mute-icon');
            const muteText = document.getElementById('clinic-hud-mute-text');
            if (window.isClinicSirenMuted) {
                stopClinicSirenAudio();
                if ('speechSynthesis' in window) window.speechSynthesis.cancel();
                if (muteIcon) muteIcon.textContent = '🔇';
                if (muteText) muteText.textContent = 'Unmute';
            } else {
                startClinicSirenAudio();
                if (muteIcon) muteIcon.textContent = '🔊';
                if (muteText) muteText.textContent = 'Mute Siren';
            }
        }

        // Toggle HUD Minimized (Slim Ribbon vs Full Card)
        window.isClinicHudMinimized = false;
        function toggleClinicHudMinimized() {
            window.isClinicHudMinimized = !window.isClinicHudMinimized;
            const body = document.getElementById('clinic-hud-body');
            const icon = document.getElementById('clinic-hud-minimize-icon');
            if (window.isClinicHudMinimized) {
                if (body) body.classList.add('hidden');
                if (icon) icon.textContent = '▼';
            } else {
                if (body) body.classList.remove('hidden');
                if (icon) icon.textContent = '▲';
            }
        }

        // Fly to Map / Locate Incident
        function flyToCurrentClinicIncidentMap() {
            const incident = window.activeClinicIncidents[window.currentClinicIndex];
            if (!incident) return;
            const card = document.getElementById(`clinic-incident-card-${incident.id}`);
            if (card) {
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                card.classList.add('ring-4', 'ring-orange-500');
                setTimeout(() => card.classList.remove('ring-4', 'ring-orange-500'), 2500);
            }
        }

        // Quick Dispatch Current Incident from HUD
        function quickDispatchClinicIncident() {
            const incident = window.activeClinicIncidents[window.currentClinicIndex];
            if (!incident) return;

            const card = document.getElementById(`clinic-incident-card-${incident.id}`);
            if (card) {
                const openBtn = document.getElementById(`open-clinic-dispatch-modal-${incident.id}`);
                if (openBtn) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    openBtn.click();
                    return;
                }
            }

            const responder = prompt("Enter Medical Responder Name / Unit:", "Clinic Medical Response Team");
            if (responder === null) return;
            const eta = prompt("Enter Estimated Time of Arrival (minutes):", "3");

            fetch(`/clinic/incidents/${incident.id}/dispatch`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    responder_name: responder || 'Clinic Medical Response Team',
                    eta_minutes: parseInt(eta) || 3
                })
            })
            .then(res => res.json())
            .then(data => {
                window.location.reload();
            })
            .catch(() => window.location.reload());
        }

        // Quick False Alarm handling from HUD
        function quickFalseAlarmClinicIncident() {
            const incident = window.activeClinicIncidents[window.currentClinicIndex];
            if (!incident) return;

            const card = document.getElementById(`clinic-incident-card-${incident.id}`);
            if (card) {
                const falseBtn = document.getElementById(`open-clinic-false-alarm-modal-${incident.id}`);
                if (falseBtn) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    falseBtn.click();
                    return;
                }
            }

            const reason = prompt("Classify as False Alarm / Accidental Trigger?\nEnter Reason (e.g. Accidental Push, Sensor Glitch, Campus Drill):", "Accidental Push");
            if (reason === null) return;

            fetch(`/clinic/incidents/${incident.id}/resolve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    resolution_type: 'False Alarm',
                    false_alarm_reason: reason || 'Accidental Push',
                    remarks: 'Marked as false alarm via Clinic Command HUD'
                })
            })
            .then(res => res.json())
            .then(data => {
                window.activeClinicIncidents = window.activeClinicIncidents.filter(i => i.id !== incident.id);
                if (window.activeClinicIncidents.length === 0) {
                    closeClinicModal();
                } else {
                    renderActiveClinicModal();
                }
                window.location.reload();
            })
            .catch(() => window.location.reload());
        }

        function startClinicSirenAudio() {
            if (isSirenActive || window.isClinicSirenMuted) return;
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
                sirenOscillator.frequency.setValueAtTime(850, webAudioCtx.currentTime);
                sirenOscillator.start();
                isSirenActive = true;

                sirenInterval = setInterval(() => {
                    if (!webAudioCtx || !sirenOscillator) return;
                    if (webAudioCtx.state === 'suspended') webAudioCtx.resume();
                    const freq = highPitch ? 650 : 920;
                    sirenOscillator.frequency.exponentialRampToValueAtTime(freq, webAudioCtx.currentTime + 0.2);
                    highPitch = !highPitch;
                }, 300);
            } catch (err) {
                console.error("Clinic Web Audio Siren error:", err);
            }
        }

        function stopClinicSirenAudio() {
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

        function speakClinicAnnouncement(text) {
            if (window.isClinicSirenMuted) return;
            if (!('speechSynthesis' in window)) return;
            window.speechSynthesis.cancel();
            const msg = new SpeechSynthesisUtterance(text);
            msg.rate = 1.0;
            msg.pitch = 1.1;
            msg.volume = 1.0;

            msg.onend = function() {
                if (!window.isClinicSirenMuted && window.activeClinicIncidents && window.activeClinicIncidents.length > 0) {
                    setTimeout(() => {
                        if (!window.isClinicSirenMuted && window.activeClinicIncidents && window.activeClinicIncidents.length > 0) {
                            window.speechSynthesis.speak(msg);
                        }
                    }, 1500);
                }
            };

            window.speechSynthesis.speak(msg);
        }

        function acknowledgeClinicEmergency() {
            if (isClinicAckInProgress || !window.activeClinicIncidents || window.activeClinicIncidents.length === 0) return;
            isClinicAckInProgress = true;

            const targetIncident = window.activeClinicIncidents[window.currentClinicIndex];
            if (!targetIncident) {
                isClinicAckInProgress = false;
                return;
            }

            const targetId = targetIncident.id;

            fetch(`/clinic/incidents/${targetId}/acknowledge`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                console.log("[ACKNOWLEDGED] Alert acknowledged by Clinic:", data);
                window.activeClinicIncidents = window.activeClinicIncidents.filter(i => i.id !== targetId);

                if (window.activeClinicIncidents.length === 0) {
                    closeClinicModal();
                    window.location.reload();
                } else {
                    if (window.currentClinicIndex >= window.activeClinicIncidents.length) {
                        window.currentClinicIndex = 0;
                    }
                    renderActiveClinicModal();
                    speakClinicAnnouncement(`Alert acknowledged. Warning! ${window.activeClinicIncidents.length} unhandled medical alerts remain!`);
                }
            })
            .catch(err => {
                console.error("Ack error:", err);
                window.location.reload();
            })
            .finally(() => {
                isClinicAckInProgress = false;
            });
        }

        function acknowledgeAllClinicEmergencies() {
            if (isClinicAckInProgress) return;
            isClinicAckInProgress = true;

            fetch('/clinic/alerts/acknowledge-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                console.log("[ACKNOWLEDGED ALL] All clinic alerts acknowledged:", data);
                window.activeClinicIncidents = [];
                closeClinicModal();
                window.location.reload();
            })
            .catch(err => {
                console.error("Ack all error:", err);
                window.location.reload();
            })
            .finally(() => {
                isClinicAckInProgress = false;
            });
        }

        function closeClinicModal() {
            stopClinicSirenAudio();
            if ('speechSynthesis' in window) window.speechSynthesis.cancel();

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
        window.updateAlertBadges = function(count) {
            const sidebarBadge = document.getElementById('sidebar-alert-badge');
            const headerBadge = document.getElementById('header-notification-badge');

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
        };

        window.Echo.channel('emergencies')
            .listen('EmergencyReported', (e) => {
                if (e && e.incident) {
                    triggerClinicFlashAndAlarm(e.incident);

                    if (e.incident.status === 'Pending') {
                        // Update badge numbers
                        const sidebarBadge = document.getElementById('sidebar-alert-badge');
                        let currentCount = parseInt(sidebarBadge ? sidebarBadge.textContent || '0' : '0');
                        if (isNaN(currentCount)) currentCount = 0;
                        window.updateAlertBadges(currentCount + 1);

                        // Update Stat Cards Grid
                        const activeAlertsEl = document.getElementById('stat-active-alerts');
                        const incomingEl = document.getElementById('stat-incoming');
                        if (activeAlertsEl) {
                            let num = parseInt(activeAlertsEl.textContent || '0');
                            activeAlertsEl.textContent = isNaN(num) ? 1 : num + 1;
                        }
                        if (incomingEl) {
                            let num = parseInt(incomingEl.textContent || '0');
                            incomingEl.textContent = isNaN(num) ? 1 : num + 1;
                        }
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
            type = 'danger'
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

                modal.classList.remove('hidden');
                modal.classList.add('flex');

                function cleanup() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    submitBtn.removeEventListener('click', onConfirm);
                    cancelBtn.removeEventListener('click', onCancel);
                }

                function onConfirm() {
                    cleanup();
                    resolve(true);
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

        function updateLiveClock() {
            const now = new Date();
            const dateEl = document.getElementById('header-date');
            const timeEl = document.getElementById('header-time');
            if (dateEl) {
                dateEl.textContent = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            }
            if (timeEl) {
                timeEl.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
            }
        }
        updateLiveClock();
        setInterval(updateLiveClock, 1000);

        function pollClinicStats() {
            fetch('/clinic/stats-json')
                .then(res => res.json())
                .then(data => {
                    const activeEl = document.getElementById('stat-active-alerts');
                    const incomingEl = document.getElementById('stat-incoming');
                    const treatedEl = document.getElementById('stat-treated');
                    const resolvedEl = document.getElementById('stat-resolved');

                    if (activeEl && data.active_alerts !== undefined) activeEl.textContent = data.active_alerts;
                    if (incomingEl && data.incoming !== undefined) incomingEl.textContent = data.incoming;
                    if (treatedEl && data.treated_today !== undefined) treatedEl.textContent = data.treated_today;
                    if (resolvedEl && data.resolved_today !== undefined) resolvedEl.textContent = data.resolved_today;

                    if (window.updateAlertBadges && data.active_alerts !== undefined) {
                        window.updateAlertBadges(data.active_alerts);
                    }

                    if (data.pending_incidents && window.syncClinicIncidents) {
                        window.syncClinicIncidents(data.pending_incidents);
                    } else if (data.latest_pending && window.triggerClinicFlashAndAlarm) {
                        window.triggerClinicFlashAndAlarm(data.latest_pending);
                    } else if (data.active_alerts === 0 && window.activeClinicIncidents && window.activeClinicIncidents.length > 0) {
                        window.syncClinicIncidents([]);
                    }
                })
                .catch(err => console.error(err));
        }
        pollClinicStats();
        setInterval(pollClinicStats, 1500);
    </script>
</body>
</html>
