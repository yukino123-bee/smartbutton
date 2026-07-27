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
                    <span class="font-bold text-[13px] {{ request()->routeIs('clinic.alerts') ? 'text-white' : 'text-brand-red group-hover:text-brand-blue' }}">Critical Alerts</span>
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

            <a href="{{ route('clinic.reports') }}" class="flex items-center px-3.5 py-2.5 {{ request()->routeIs('clinic.reports') ? 'bg-brand-blue text-white shadow-md shadow-blue-300/50' : 'text-black font-bold hover:bg-white hover:text-brand-blue hover:shadow-sm hover:translate-x-1 border border-transparent hover:border-slate-200' }} rounded-xl transition-all duration-200 group">
                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span class="font-bold text-[13px]">Reports</span>
            </a>

            <a href="{{ route('clinic.profile') }}" class="flex items-center px-3.5 py-2.5 {{ request()->routeIs('clinic.profile') ? 'bg-brand-blue text-white shadow-md shadow-blue-300/50' : 'text-black font-bold hover:bg-white hover:text-brand-blue hover:shadow-sm hover:translate-x-1 border border-transparent hover:border-slate-200' }} rounded-xl transition-all duration-200 group">
                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span class="font-bold text-[13px]">Profile</span>
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
                <span class="text-xs font-bold text-black">Clinic Side</span>
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
                    <button class="relative text-black hover:text-brand-blue transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span id="header-notification-badge" class="absolute -top-1 -right-1 min-w-[16px] h-4 px-1 bg-brand-red rounded-full text-[9px] font-black flex items-center justify-center text-white {{ ($activeAlertsCount ?? 0) > 0 ? '' : 'hidden' }}">{{ $activeAlertsCount ?? 0 }}</span>
                    </button>
                    
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

    <!-- Scripts -->
    {{-- Global Emergency Siren, Screen Flash Overlay & Voice Announcement Modal --}}
    <div id="global-emergency-overlay" class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-4 transition-all duration-300 select-none">
        <div id="global-emergency-backdrop" class="absolute inset-0 animate-pulse bg-red-600/60 backdrop-blur-md"></div>
        
        <div id="global-emergency-modal" class="relative z-10 w-full max-w-lg bg-white border-4 border-red-600 rounded-3xl shadow-2xl overflow-hidden p-8 text-center transform transition-all scale-100">
            <div class="w-20 h-20 rounded-full mx-auto mb-4 flex items-center justify-center animate-bounce bg-red-600 shadow-lg" id="global-emergency-icon-box">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <span id="global-emergency-category-badge" class="inline-block px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest text-white bg-red-600 mb-3 shadow-sm">
                CRITICAL MEDICAL EMERGENCY
            </span>

            <h3 id="global-emergency-title" class="text-2xl font-black text-slate-900 mb-2 uppercase tracking-wide">
                CRITICAL EMERGENCY
            </h3>

            <p id="global-emergency-location" class="text-slate-700 font-bold text-lg mb-1">
                Engineering Building
            </p>

            <p id="global-emergency-device" class="text-slate-500 text-xs font-mono mb-8">
                Device ID: ENG-001 • Active Alarm
            </p>

            <button id="global-emergency-ack-btn" type="button" onclick="acknowledgeClinicEmergency()"
                    class="w-full py-4 px-6 rounded-2xl font-extrabold text-white text-base shadow-xl bg-red-600 hover:bg-red-700 active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span>ACKNOWLEDGE & STOP ALARM</span>
            </button>
        </div>
    </div>

    <script>
        let currentClinicEmergency = null;
        let webAudioCtx = null;
        let sirenOscillator = null;
        let sirenGainNode = null;
        let sirenInterval = null;
        let isSirenActive = false;

        function triggerClinicFlashAndAlarm(incident) {
            if (!incident || !incident.id) return;
            if (currentClinicEmergency && currentClinicEmergency.id === incident.id) return;

            currentClinicEmergency = incident;
            const type = incident.emergency_type || 'Critical Emergency';
            const location = (incident.device && incident.device.building) ? incident.device.building : 'Campus Location';
            const deviceCode = (incident.device && incident.device.device_code) ? incident.device.device_code : 'N/A';

            const overlay = document.getElementById('global-emergency-overlay');
            const backdrop = document.getElementById('global-emergency-backdrop');
            const modal = document.getElementById('global-emergency-modal');
            const iconBox = document.getElementById('global-emergency-icon-box');
            const badge = document.getElementById('global-emergency-category-badge');
            const title = document.getElementById('global-emergency-title');
            const locEl = document.getElementById('global-emergency-location');
            const devEl = document.getElementById('global-emergency-device');
            const ackBtn = document.getElementById('global-emergency-ack-btn');

            let bgClass = 'bg-red-600/70';
            let badgeClass = 'bg-red-600';
            let borderClass = 'border-red-600';

            if (type.includes('Medical')) {
                bgClass = 'bg-orange-500/70';
                badgeClass = 'bg-orange-500';
                borderClass = 'border-orange-500';
            }

            if (backdrop) backdrop.className = `absolute inset-0 animate-pulse ${bgClass} backdrop-blur-md`;
            if (modal) modal.className = `relative z-10 w-full max-w-lg bg-white border-4 ${borderClass} rounded-3xl shadow-2xl overflow-hidden p-8 text-center transform transition-all scale-100`;
            if (iconBox) iconBox.className = `w-20 h-20 rounded-full mx-auto mb-4 flex items-center justify-center animate-bounce ${badgeClass} shadow-lg`;
            if (badge) { badge.className = `inline-block px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest text-white ${badgeClass} mb-3 shadow-sm`; badge.textContent = type; }
            if (title) title.textContent = type.toUpperCase();
            if (locEl) locEl.textContent = location;
            if (devEl) devEl.textContent = `Device ID: ${deviceCode} • Active Alarm`;
            if (ackBtn) ackBtn.className = `w-full py-4 px-6 rounded-2xl font-extrabold text-white text-base shadow-xl ${badgeClass} hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer`;

            if (overlay) {
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
            }

            startClinicSirenAudio();
            speakClinicAnnouncement(`Urgent Medical Emergency! ${type} detected at ${location}! Clinic medical team respond immediately!`);
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

        function startClinicSirenAudio() {
            if (isSirenActive) return;
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
                sirenGainNode.gain.setValueAtTime(0.4, webAudioCtx.currentTime);

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
            if (!('speechSynthesis' in window)) return;
            window.speechSynthesis.cancel();
            const msg = new SpeechSynthesisUtterance(text);
            msg.rate = 1.0;
            msg.pitch = 1.1;
            msg.volume = 1.0;

            msg.onend = function() {
                if (currentClinicEmergency) {
                    setTimeout(() => {
                        if (currentClinicEmergency) window.speechSynthesis.speak(msg);
                    }, 800);
                }
            };

            window.speechSynthesis.speak(msg);
        }

        function acknowledgeClinicEmergency() {
            stopClinicSirenAudio();
            if ('speechSynthesis' in window) window.speechSynthesis.cancel();

            const overlay = document.getElementById('global-emergency-overlay');
            if (overlay) {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
            }

            if (currentClinicEmergency && currentClinicEmergency.id) {
                const targetId = currentClinicEmergency.id;
                currentClinicEmergency = null;

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
                    window.location.reload();
                })
                .catch(err => {
                    console.error("Ack error:", err);
                    window.location.reload();
                });
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

                    if (data.latest_pending && window.triggerClinicFlashAndAlarm) {
                        window.triggerClinicFlashAndAlarm(data.latest_pending);
                    }
                })
                .catch(err => console.error(err));
        }
        pollClinicStats();
        setInterval(pollClinicStats, 1500);
    </script>
</body>
</html>
