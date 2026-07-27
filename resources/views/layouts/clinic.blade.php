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

            <a href="{{ route('clinic.dashboard') }}" class="flex items-center px-3 py-2.5 {{ request()->routeIs('clinic.dashboard') ? 'bg-brand-blue text-white shadow-md shadow-blue-200' : 'text-black font-bold hover:bg-white/80 hover:text-brand-blue' }} rounded-xl transition-all group">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="font-bold text-[13px]">Dashboard</span>
            </a>

            <a href="{{ route('clinic.alerts') }}" class="flex items-center justify-between px-3 py-2.5 {{ request()->routeIs('clinic.alerts') ? 'bg-brand-blue text-white shadow-md shadow-blue-200' : 'text-black font-bold hover:bg-white/80 hover:text-brand-blue' }} rounded-xl transition-all">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('clinic.alerts') ? 'text-white' : 'text-brand-red' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="font-bold text-[13px] {{ request()->routeIs('clinic.alerts') ? 'text-white' : 'text-brand-red' }}">Critical Alerts</span>
                </div>
                <span id="sidebar-alert-badge" class="bg-brand-red text-white text-[9px] font-extrabold px-1.5 py-0.5 rounded-full animate-pulse {{ ($activeAlertsCount ?? 0) > 0 ? '' : 'hidden' }}">{{ $activeAlertsCount ?? 0 }}</span>
            </a>

            <a href="{{ route('clinic.incoming') }}" class="flex items-center px-3 py-2.5 {{ request()->routeIs('clinic.incoming') ? 'bg-brand-blue text-white shadow-md shadow-blue-200' : 'text-black font-bold hover:bg-white/80 hover:text-brand-blue' }} rounded-xl transition-all">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                <span class="font-bold text-[13px]">Incoming Patients</span>
            </a>

            <a href="{{ route('clinic.logs') }}" class="flex items-center px-3 py-2.5 {{ request()->routeIs('clinic.logs') ? 'bg-brand-blue text-white shadow-md shadow-blue-200' : 'text-black font-bold hover:bg-white/80 hover:text-brand-blue' }} rounded-xl transition-all">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span class="font-bold text-[13px]">Incident Logs</span>
            </a>

            <a href="{{ route('clinic.reports') }}" class="flex items-center px-3 py-2.5 {{ request()->routeIs('clinic.reports') ? 'bg-brand-blue text-white shadow-md shadow-blue-200' : 'text-black font-bold hover:bg-white/80 hover:text-brand-blue' }} rounded-xl transition-all">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span class="font-bold text-[13px]">Reports</span>
            </a>

        </div>

        <div class="p-3 border-t border-slate-200 mt-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center w-full px-3 py-2.5 text-brand-red font-bold hover:text-white hover:bg-brand-red rounded-xl transition-all">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
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
                    
                    <div class="flex items-center cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-slate-900 overflow-hidden mr-3">
                            <img src="https://ui-avatars.com/api/?name=Admin&background=000000&color=fff" alt="Admin" class="w-full h-full object-cover">
                        </div>
                        <div class="hidden md:block">
                            <div class="text-xs font-bold text-black leading-none mb-1">Clinic Admin</div>
                            <div class="text-[11px] font-semibold text-black leading-none">Clinic Staff</div>
                        </div>
                        <svg class="w-4 h-4 ml-2 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-6 bg-slate-50/50 text-black">
@yield("content")
        </div>
    </main>

    <!-- Scripts -->
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
                if (e.incident.emergency_type === 'Critical Emergency') {
                    console.log('CRITICAL Emergency:', e);
                    
                    // Increment badges
                    const sidebarBadge = document.getElementById('sidebar-alert-badge');
                    let currentCount = parseInt(sidebarBadge ? sidebarBadge.textContent || '0' : '0');
                    if (isNaN(currentCount)) currentCount = 0;
                    window.updateAlertBadges(currentCount + 1);

                    // Play Alarm Sound
                    const audio = new Audio('https://actions.google.com/sounds/v1/alarms/alarm_clock.ogg');
                    audio.play().catch(e => console.log("Audio play failed: ", e));

                    // Show Flashing Banner/Alert
                    const alertHtml = `
                    <div class="bg-brand-red text-white p-4 rounded-xl mb-4 animate-pulse flex justify-between items-center shadow-[0_0_20px_rgba(220,38,38,0.6)]">
                        <div>
                            <div class="font-bold text-lg">CRITICAL EMERGENCY INCOMING</div>
                            <div class="text-sm">${e.incident.device.building} • Device: ${e.incident.device.device_code}</div>
                        </div>
                        <button onclick="this.parentElement.remove(); audio.pause();" class="bg-white text-brand-red font-bold px-4 py-2 rounded">ACKNOWLEDGE</button>
                    </div>
                    `;
                    document.querySelector('main .flex-1').insertAdjacentHTML('afterbegin', alertHtml);
                }
            });
    </script>
    <script>
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
    </script>
</body>
</html>
