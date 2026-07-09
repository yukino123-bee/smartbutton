<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Dashboard</title>
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
                            teal: '#14B8A6',
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
        
        .pulse-ring {
            animation: pulse-ring 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 0.5; }
            80%, 100% { transform: scale(1.5); opacity: 0; }
        }
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
            <a href="{{ route('clinic.dashboard') }}" class="flex items-center px-3 py-2.5 {{ request()->routeIs('clinic.dashboard') ? 'bg-brand-blue text-white' : 'text-brand-text hover:text-white hover:bg-white/5' }} rounded-lg group">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="font-medium">Dashboard</span>
            </a>
            
            <a href="{{ route('clinic.alerts') }}" class="flex items-center justify-between px-3 py-2.5 {{ request()->routeIs('clinic.alerts') ? 'bg-brand-blue text-white' : 'text-brand-text hover:text-white hover:bg-white/5' }} rounded-lg transition-colors">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span class="font-medium text-brand-red">Critical Alerts</span>
                </div>
                <span class="bg-brand-red text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full animate-pulse">2</span>
            </a>

            <a href="{{ route('clinic.incoming') }}" class="flex items-center px-3 py-2.5 {{ request()->routeIs('clinic.incoming') ? 'bg-brand-blue text-white' : 'text-brand-text hover:text-white hover:bg-white/5' }} rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                <span class="font-medium">Incoming Patients</span>
            </a>

            <a href="{{ route('clinic.logs') }}" class="flex items-center px-3 py-2.5 {{ request()->routeIs('clinic.logs') ? 'bg-brand-blue text-white' : 'text-brand-text hover:text-white hover:bg-white/5' }} rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span class="font-medium">Incident Logs</span>
            </a>

            <a href="{{ route('clinic.patients') }}" class="flex items-center px-3 py-2.5 {{ request()->routeIs('clinic.patients') ? 'bg-brand-blue text-white' : 'text-brand-text hover:text-white hover:bg-white/5' }} rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span class="font-medium">Patients Record</span>
            </a>

            <a href="#" class="flex items-center px-3 py-2.5 text-brand-text hover:text-white hover:bg-white/5 rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                <span class="font-medium">Equipment Status</span>
            </a>

            <a href="{{ route('clinic.reports') }}" class="flex items-center px-3 py-2.5 {{ request()->routeIs('clinic.reports') ? 'bg-brand-blue text-white' : 'text-brand-text hover:text-white hover:bg-white/5' }} rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span class="font-medium">Reports</span>
            </a>

            <a href="{{ route('clinic.users') }}" class="flex items-center px-3 py-2.5 {{ request()->routeIs('clinic.users') ? 'bg-brand-blue text-white' : 'text-brand-text hover:text-white hover:bg-white/5' }} rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span class="font-medium">Users</span>
            </a>

            <a href="{{ route('clinic.settings') }}" class="flex items-center px-3 py-2.5 {{ request()->routeIs('clinic.settings') ? 'bg-brand-blue text-white' : 'text-brand-text hover:text-white hover:bg-white/5' }} rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="font-medium">Settings</span>
            </a>

            <div class="mt-8 border-t border-brand-border pt-6">
                <div class="text-[10px] font-bold text-brand-text mb-4 tracking-wider">SYSTEM STATUS</div>
                
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

                <div class="flex items-center px-3 py-2 mt-1">
                    <div class="w-8 h-8 rounded bg-white/5 flex items-center justify-center mr-3 shrink-0">
                        <svg class="w-4 h-4 text-brand-text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <div>
                        <div class="text-white font-medium text-xs">Clinic Alarm System</div>
                        <div class="text-brand-green text-[10px] flex items-center mt-0.5"><span class="w-1.5 h-1.5 rounded-full bg-brand-green mr-1.5"></span>Online</div>
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
            <div class="flex items-baseline space-x-2">
                <h1 class="text-lg font-bold text-white uppercase tracking-wider">CLINIC DASHBOARD</h1>
                <span class="text-xs text-brand-text">Clinic Side</span>
            </div>
            
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
                            <div class="text-xs font-semibold text-white leading-none mb-1">Clinic Admin</div>
                            <div class="text-[10px] text-brand-text leading-none">Clinic Staff</div>
                        </div>
                        <svg class="w-4 h-4 ml-2 text-brand-text" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-6 bg-brand-bg">
@yield("content")
        </div>
    </main>

    <!-- Scripts -->
    <script type="module">
        window.Echo.channel('emergencies')
            .listen('EmergencyReported', (e) => {
                if (e.incident.emergency_type === 'Critical Emergency') {
                    console.log('CRITICAL Emergency:', e);
                    
                    // Play Alarm Sound
                    const audio = new Audio('https://actions.google.com/sounds/v1/alarms/alarm_clock.ogg');
                    audio.play().catch(e => console.log("Audio play failed: ", e));

                    // Show Flashing Banner/Alert
                    const alertHtml = `
                    <div class="bg-brand-red text-white p-4 rounded-xl mb-4 animate-pulse flex justify-between items-center shadow-[0_0_20px_rgba(239,68,68,0.6)]">
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
</body>
</html>
