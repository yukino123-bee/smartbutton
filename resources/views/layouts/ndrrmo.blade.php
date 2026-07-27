<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NDRRMO Dashboard</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            <p class="text-[10px] font-black text-black uppercase tracking-widest px-3 mb-1 mt-2">Navigation</p>
            
            <a href="{{ route('ndrrmo.dashboard') }}" class="flex items-center px-3.5 py-2.5 {{ request()->routeIs('ndrrmo.dashboard') ? 'bg-brand-blue text-white shadow-md shadow-blue-300/50' : 'text-black font-bold hover:bg-white hover:text-brand-blue hover:shadow-sm hover:translate-x-1 border border-transparent hover:border-slate-200' }} rounded-xl transition-all duration-200 group">
                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="font-bold text-[13px]">Dashboard</span>
            </a>
            
            <a href="{{ route('ndrrmo.alerts') }}" class="flex items-center justify-between px-3.5 py-2.5 {{ request()->routeIs('ndrrmo.alerts') ? 'bg-brand-blue text-white shadow-md shadow-blue-300/50' : 'text-black font-bold hover:bg-white hover:text-brand-blue hover:shadow-sm hover:translate-x-1 border border-transparent hover:border-slate-200' }} rounded-xl transition-all duration-200 group">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110 {{ request()->routeIs('ndrrmo.alerts') ? 'text-white' : 'text-brand-red group-hover:text-brand-blue' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="font-bold text-[13px] {{ request()->routeIs('ndrrmo.alerts') ? 'text-white' : 'text-brand-red group-hover:text-brand-blue' }}">Live Alerts</span>
                </div>
                <span id="ndrrmo-sidebar-alert-badge" class="bg-brand-red text-white text-[9px] font-extrabold px-1.5 py-0.5 rounded-full animate-pulse {{ ($activeAlertsCount ?? 0) > 0 ? '' : 'hidden' }}">{{ $activeAlertsCount ?? 0 }}</span>
            </a>

            <a href="{{ route('ndrrmo.logs') }}" class="flex items-center px-3.5 py-2.5 {{ request()->routeIs('ndrrmo.logs') ? 'bg-brand-blue text-white shadow-md shadow-blue-300/50' : 'text-black font-bold hover:bg-white hover:text-brand-blue hover:shadow-sm hover:translate-x-1 border border-transparent hover:border-slate-200' }} rounded-xl transition-all duration-200 group">
                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span class="font-bold text-[13px]">Incident Logs</span>
            </a>

            <a href="{{ route('ndrrmo.map') }}" class="flex items-center px-3.5 py-2.5 {{ request()->routeIs('ndrrmo.map') ? 'bg-brand-blue text-white shadow-md shadow-blue-300/50' : 'text-black font-bold hover:bg-white hover:text-brand-blue hover:shadow-sm hover:translate-x-1 border border-transparent hover:border-slate-200' }} rounded-xl transition-all duration-200 group">
                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="font-bold text-[13px]">Campus Map</span>
            </a>

            <a href="{{ route('ndrrmo.devices') }}" class="flex items-center px-3.5 py-2.5 {{ request()->routeIs('ndrrmo.devices') ? 'bg-brand-blue text-white shadow-md shadow-blue-300/50' : 'text-black font-bold hover:bg-white hover:text-brand-blue hover:shadow-sm hover:translate-x-1 border border-transparent hover:border-slate-200' }} rounded-xl transition-all duration-200 group">
                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m14-6h2m-2 6h2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                <span class="font-bold text-[13px]">Devices</span>
            </a>

            <a href="{{ route('ndrrmo.reports') }}" class="flex items-center px-3.5 py-2.5 {{ request()->routeIs('ndrrmo.reports') ? 'bg-brand-blue text-white shadow-md shadow-blue-300/50' : 'text-black font-bold hover:bg-white hover:text-brand-blue hover:shadow-sm hover:translate-x-1 border border-transparent hover:border-slate-200' }} rounded-xl transition-all duration-200 group">
                <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span class="font-bold text-[13px]">Reports</span>
            </a>

        </div>

        <div class="p-3 border-t border-slate-200/80 mt-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center w-full px-3.5 py-2.5 text-brand-red font-bold hover:text-white hover:bg-red-600 hover:shadow-md hover:shadow-red-200 hover:translate-x-1 rounded-xl transition-all duration-200 group">
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
                <h1 class="text-lg font-black text-black uppercase tracking-wider">NDRRMO DASHBOARD</h1>
                <span class="text-xs font-bold text-black">NDRRMO Side</span>
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
                    <button class="relative text-black hover:text-brand-blue transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span id="ndrrmo-header-notification-badge" class="absolute -top-1 -right-1 min-w-[16px] h-4 px-1 bg-brand-red rounded-full text-[9px] font-black flex items-center justify-center text-white {{ ($activeAlertsCount ?? 0) > 0 ? '' : 'hidden' }}">{{ $activeAlertsCount ?? 0 }}</span>
                    </button>
                    
                    <div class="flex items-center cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-slate-900 overflow-hidden mr-3">
                            <img src="https://ui-avatars.com/api/?name=Admin&background=000000&color=fff" alt="Admin" class="w-full h-full object-cover">
                        </div>
                        <div class="hidden md:block">
                            <div class="text-xs font-bold text-black leading-none mb-1">NDRRMO Admin</div>
                            <div class="text-[11px] font-semibold text-black leading-none">Administrator</div>
                        </div>
                        <svg class="w-4 h-4 ml-2 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-6 bg-slate-50/50 text-slate-800 flex flex-col">
@yield("content")
        </div>
    </main>

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
                CRITICAL EMERGENCY
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

            <button id="global-emergency-ack-btn" type="button" onclick="acknowledgeActiveEmergency()"
                    class="w-full py-4 px-6 rounded-2xl font-extrabold text-white text-base shadow-xl bg-red-600 hover:bg-red-700 active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span>ACKNOWLEDGE & STOP ALARM</span>
            </button>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        let currentActiveEmergency = null;
        let webAudioCtx = null;
        let sirenOscillator = null;
        let sirenGainNode = null;
        let sirenInterval = null;
        let isSirenActive = false;

        function triggerScreenFlashAndAlarm(incident) {
            if (!incident || !incident.id) return;
            if (currentActiveEmergency && currentActiveEmergency.id === incident.id) return; // already active

            currentActiveEmergency = incident;
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
            } else if (type.includes('Public Safety') || type.includes('Facility')) {
                bgClass = 'bg-amber-500/70';
                badgeClass = 'bg-amber-500';
                borderClass = 'border-amber-500';
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

            // Start Audio Siren & Voice Speech
            startEmergencySirenAudio();
            speakEmergencyAnnouncement(`Urgent Alert! ${type} detected at ${location}! Please respond immediately!`);
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
            if (!('speechSynthesis' in window)) return;
            window.speechSynthesis.cancel();
            const msg = new SpeechSynthesisUtterance(text);
            msg.rate = 1.0;
            msg.pitch = 1.1;
            msg.volume = 1.0;

            msg.onend = function() {
                if (currentActiveEmergency) {
                    setTimeout(() => {
                        if (currentActiveEmergency) window.speechSynthesis.speak(msg);
                    }, 800);
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
            stopEmergencySirenAudio();
            stopVoiceSpeech();

            const overlay = document.getElementById('global-emergency-overlay');
            if (overlay) {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
            }

            if (currentActiveEmergency && currentActiveEmergency.id) {
                const targetId = currentActiveEmergency.id;
                currentActiveEmergency = null;

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
                    console.log("[ACKNOWLEDGED] Alert acknowledged via dashboard:", data);
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
        window.updateNDRRMOAlertBadges = function(count) {
            const sidebarBadge = document.getElementById('ndrrmo-sidebar-alert-badge');
            const headerBadge = document.getElementById('ndrrmo-header-notification-badge');

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
                    triggerScreenFlashAndAlarm(e.incident);
                }
            });
    </script>
    <script>
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

                    // Auto-trigger emergency modal & audio siren if any pending incident exists
                    if (data.latest_pending && window.triggerScreenFlashAndAlarm) {
                        window.triggerScreenFlashAndAlarm(data.latest_pending);
                    }
                })
                .catch(err => console.error(err));
        }
        pollNDRRMOStats();
        setInterval(pollNDRRMOStats, 1500);
    </script>
</body>
</html>
