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
                            bg: '#F8FAFC',
                            sidebar: '#FFFFFF',
                            card: '#FFFFFF',
                            border: '#E2E8F0',
                            blue: '#3B82F6',
                            red: '#EF4444',
                            orange: '#F59E0B',
                            green: '#15803D',
                            text: '#64748B',
                            dark: '#0F172A',
                            hover: '#F1F5F9'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: theme('colors.brand.bg'); color: theme('colors.brand.dark'); font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="h-screen flex overflow-hidden text-sm">

    <!-- Sidebar -->
    <aside class="w-64 bg-brand-sidebar flex flex-col border-r border-brand-border z-20 shrink-0">
        <div class="h-16 flex items-center px-4 border-b border-brand-border shrink-0">
            <img src="/images/logo.png" alt="JHCSC Logo" class="w-10 h-10 object-contain shrink-0">
            <div class="ml-3 text-[11px] font-bold leading-tight text-brand-dark tracking-wide">
                Smart Student Panic Button &<br>Emergency Response System
            </div>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar p-4 flex flex-col gap-1">
            <div class="text-[10px] font-bold text-brand-text mb-2 tracking-wider">MAIN NAVIGATION</div>
            
            <a href="{{ route('ndrrmo.dashboard') }}" class="flex items-center px-3 py-2.5 {{ request()->routeIs('ndrrmo.dashboard') ? 'bg-brand-blue text-white' : 'text-brand-text hover:text-white hover:bg-brand-hover' }} rounded-lg group">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="font-medium">Dashboard</span>
            </a>
            
            <a href="{{ route('ndrrmo.alerts') }}" class="flex items-center justify-between px-3 py-2.5 {{ request()->routeIs('ndrrmo.alerts') ? 'bg-brand-blue text-white' : 'text-brand-text hover:text-white hover:bg-brand-hover' }} rounded-lg transition-colors">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span class="font-medium">Live Alerts</span>
                </div>
                <span class="bg-brand-red text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">2</span>
            </a>

            <a href="{{ route('ndrrmo.logs') }}" class="flex items-center px-3 py-2.5 {{ request()->routeIs('ndrrmo.logs') ? 'bg-brand-blue text-white' : 'text-brand-text hover:text-white hover:bg-brand-hover' }} rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span class="font-medium">Incident Logs</span>
            </a>

            <a href="{{ route('ndrrmo.map') }}" class="flex items-center px-3 py-2.5 {{ request()->routeIs('ndrrmo.map') ? 'bg-brand-blue text-white' : 'text-brand-text hover:text-white hover:bg-brand-hover' }} rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="font-medium">Campus Map</span>
            </a>

            <a href="{{ route('ndrrmo.devices') }}" class="flex items-center px-3 py-2.5 {{ request()->routeIs('ndrrmo.devices') ? 'bg-brand-blue text-white' : 'text-brand-text hover:text-white hover:bg-brand-hover' }} rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m14-6h2m-2 6h2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                <span class="font-medium">Devices</span>
            </a>


            <a href="{{ route('ndrrmo.reports') }}" class="flex items-center px-3 py-2.5 {{ request()->routeIs('ndrrmo.reports') ? 'bg-brand-blue text-white' : 'text-brand-text hover:text-white hover:bg-brand-hover' }} rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span class="font-medium">Reports</span>
            </a>


        </div>

        <div class="p-4 border-t border-brand-border text-[10px] text-brand-text shrink-0">
            © 2025 JHCSC. All rights reserved.
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Header -->
        <header class="h-16 bg-brand-bg border-b border-brand-border flex items-center justify-between px-6 shrink-0 z-10">
            <h1 class="text-lg font-medium text-brand-dark">NDRRMO Dashboard</h1>
            
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
                    <button class="relative text-brand-text hover:text-brand-dark transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-brand-red rounded-full text-[9px] font-bold flex items-center justify-center text-white border border-brand-bg">3</span>
                    </button>
                    
                    <div class="flex items-center cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-slate-700 overflow-hidden mr-3">
                            <img src="https://ui-avatars.com/api/?name=Admin&background=475569&color=fff" alt="Admin" class="w-full h-full object-cover">
                        </div>
                        <div class="hidden md:block">
                            <div class="text-xs font-semibold text-brand-dark leading-none mb-1">NDRRMO Admin</div>
                            <div class="text-[10px] text-brand-text leading-none">Administrator</div>
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
                                    <div class="text-brand-dark font-bold text-sm leading-tight mb-1">${e.incident.device.building}</div>
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
