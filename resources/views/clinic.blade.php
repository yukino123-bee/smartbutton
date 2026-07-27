@extends('layouts.clinic')

@section('content')
            
            <!-- Top Stats Row -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <!-- Stat Card 1 -->
                <div class="bg-[#2D1619] border border-brand-red/30 rounded-xl p-4 flex items-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-brand-red/5"></div>
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-brand-red"></div>
                    <div class="w-12 h-12 rounded-full bg-brand-red flex items-center justify-center mr-4 z-10 shadow-[0_0_15px_rgba(239,68,68,0.5)]">
                        <svg class="w-6 h-6 text-brand-dark" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/></svg>
                    </div>
                    <div class="z-10">
                        <div class="text-[10px] font-bold text-brand-dark uppercase tracking-wider mb-1">ACTIVE CRITICAL ALERTS</div>
                        <div class="text-3xl font-bold text-brand-dark leading-none mb-1">1</div>
                        <div class="text-[10px] text-brand-red">Needs immediate attention</div>
                    </div>
                </div>
                <!-- Stat Card 2 -->
                <div class="bg-brand-card border border-brand-border rounded-xl p-4 flex items-center">
                    <div class="w-12 h-12 rounded-full bg-brand-blue flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-brand-text uppercase tracking-wider mb-1">INCOMING PATIENTS</div>
                        <div class="text-2xl font-bold text-brand-dark leading-none mb-1">1</div>
                        <div class="text-[10px] text-brand-text">For today</div>
                    </div>
                </div>
                <!-- Stat Card 3 -->
                <div class="bg-brand-card border border-brand-border rounded-xl p-4 flex items-center">
                    <div class="w-12 h-12 rounded-full bg-brand-green flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-brand-text uppercase tracking-wider mb-1">PATIENTS TREATED (TODAY)</div>
                        <div class="text-2xl font-bold text-brand-dark leading-none mb-1">3</div>
                        <div class="text-[10px] text-brand-text">Total</div>
                    </div>
                </div>
                <!-- Stat Card 4 -->
                <div class="bg-brand-card border border-brand-border rounded-xl p-4 flex items-center">
                    <div class="w-12 h-12 rounded-full bg-brand-teal flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-brand-text uppercase tracking-wider mb-1">RESOLVED (TODAY)</div>
                        <div class="text-2xl font-bold text-brand-dark leading-none mb-1">2</div>
                        <div class="text-[10px] text-brand-text">Total</div>
                    </div>
                </div>
            </div>

            <!-- Big Alert Section -->
            <div class="bg-gradient-to-r from-[#2a1318] to-brand-card border border-brand-red/40 rounded-xl mb-6 flex items-stretch overflow-hidden relative shadow-[0_0_30px_rgba(239,68,68,0.1)]">
                <!-- Red side border -->
                <div class="w-1.5 bg-brand-red shrink-0 z-10"></div>
                
                <!-- Left: Siren Icon -->
                <div class="w-1/4 p-6 flex flex-col items-center justify-center border-r border-brand-border/30 relative z-10">
                    <div class="relative w-32 h-32 flex items-center justify-center">
                        <div class="absolute inset-0 border-4 border-brand-red/20 rounded-full pulse-ring"></div>
                        <div class="absolute inset-2 border-4 border-brand-red/40 rounded-full pulse-ring" style="animation-delay: 0.5s;"></div>
                        <div class="w-20 h-20 bg-brand-red rounded-full flex items-center justify-center shadow-[0_0_30px_rgba(239,68,68,0.8)] z-10">
                            <svg class="w-10 h-10 text-brand-dark" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c-4.97 0-9 4.03-9 9 0 3.86 2.43 7.15 5.91 8.46l.09.04V22h6v-2.5l.09-.04C18.57 18.15 21 14.86 21 11c0-4.97-4.03-9-9-9zm0 2c3.86 0 7 3.14 7 7 0 2.89-1.74 5.38-4.26 6.45L14 17.75V19h-4v-1.25l-.74-.3C6.74 16.38 5 13.89 5 11c0-3.86 3.14-7 7-7zm-1 3v5h2V7h-2zm0 7v2h2v-2h-2z"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Middle: Alert Info -->
                <div class="flex-1 p-8 z-10">
                    <div class="inline-block bg-brand-red text-white text-xs font-bold px-3 py-1 rounded mb-4 uppercase tracking-wider">CRITICAL EMERGENCY</div>
                    <h2 class="text-3xl font-bold text-brand-dark mb-6">Emergency Incoming!</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center text-brand-text">
                            <svg class="w-5 h-5 mr-3 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="text-base text-brand-dark">Engineering Building</span>
                        </div>
                        <div class="flex items-center text-brand-text">
                            <svg class="w-5 h-5 mr-3 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-base">May 15, 2025 <span class="mx-2">|</span> 10:28 AM</span>
                        </div>
                        <div class="flex items-center text-brand-text">
                            <svg class="w-5 h-5 mr-3 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                            <span class="text-base">Device ID: GYM-001 <span class="text-slate-500 text-sm ml-1">(Gymnasium)</span></span>
                        </div>
                    </div>
                </div>

                <!-- Right: Instructions & Timer -->
                <div class="w-1/3 flex border-l border-brand-border/30 z-10">
                    <div class="flex-1 p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-xs font-bold text-brand-red uppercase tracking-wider mb-4">WHAT TO DO</h3>
                            <ul class="space-y-3">
                                <li class="flex items-start text-sm text-brand-text">
                                    <svg class="w-5 h-5 mr-2 text-brand-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Prepare emergency equipment</span>
                                </li>
                                <li class="flex items-start text-sm text-brand-text">
                                    <svg class="w-5 h-5 mr-2 text-brand-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Prepare medical staff</span>
                                </li>
                                <li class="flex items-start text-sm text-brand-text">
                                    <svg class="w-5 h-5 mr-2 text-brand-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Standby for incoming patient</span>
                                </li>
                                <li class="flex items-start text-sm text-brand-text">
                                    <svg class="w-5 h-5 mr-2 text-brand-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Coordinate with NDRRMO</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="w-[200px] bg-black/20 p-6 flex flex-col items-center justify-center border-l border-brand-border/30 text-center">
                        <div class="text-[10px] font-bold text-brand-text uppercase tracking-wider mb-2">ESTIMATED ARRIVAL</div>
                        <div class="text-4xl font-bold text-brand-red mb-1">03:45</div>
                        <div class="text-xs text-brand-text mb-6">minutes</div>
                        <button class="w-full bg-brand-red hover:bg-red-600 text-white font-bold py-3 rounded shadow-[0_0_15px_rgba(239,68,68,0.4)] flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            PATIENT ARRIVED
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bottom Row: Tables and Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column: Tables -->
                <div class="lg:col-span-2 flex flex-col gap-6">
                    <!-- Active Critical Alerts -->
                    <div class="bg-brand-card border border-brand-border rounded-xl flex flex-col">
                        <div class="px-5 py-4 flex items-center justify-between border-b border-brand-border">
                            <h2 class="text-xs font-bold text-brand-dark uppercase tracking-wider">ACTIVE CRITICAL ALERTS</h2>
                            <a href="#" class="text-[10px] text-brand-blue hover:text-blue-400">View All</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-brand-border text-[10px] text-brand-text uppercase tracking-wider">
                                        <th class="px-5 py-3 font-medium">#</th>
                                        <th class="px-5 py-3 font-medium">Time</th>
                                        <th class="px-5 py-3 font-medium">Location</th>
                                        <th class="px-5 py-3 font-medium">Device ID</th>
                                        <th class="px-5 py-3 font-medium">Type</th>
                                        <th class="px-5 py-3 font-medium">Status</th>
                                        <th class="px-5 py-3 font-medium text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-xs text-brand-dark">
                                    <tr class="hover:bg-brand-hover transition-colors bg-brand-red/5">
                                        <td class="px-5 py-3 text-brand-text">1</td>
                                        <td class="px-5 py-3 font-medium">10:28 AM</td>
                                        <td class="px-5 py-3 text-brand-dark">Engineering Building</td>
                                        <td class="px-5 py-3 text-brand-text">GYM-001</td>
                                        <td class="px-5 py-3"><span class="bg-brand-red text-white text-[9px] font-bold px-2 py-0.5 rounded">Critical Emergency</span></td>
                                        <td class="px-5 py-3 text-brand-red font-bold">Incoming</td>
                                        <td class="px-5 py-3 text-center"><button class="text-brand-blue hover:text-blue-400"><svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Alert History -->
                    <div class="bg-brand-card border border-brand-border rounded-xl flex flex-col flex-1">
                        <div class="px-5 py-4 flex items-center justify-between border-b border-brand-border">
                            <h2 class="text-xs font-bold text-brand-dark uppercase tracking-wider">ALERT HISTORY (TODAY)</h2>
                            <a href="#" class="text-[10px] text-brand-blue hover:text-blue-400">View All</a>
                        </div>
                        <div class="overflow-x-auto flex-1">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-brand-border text-[10px] text-brand-text uppercase tracking-wider">
                                        <th class="px-5 py-3 font-medium">#</th>
                                        <th class="px-5 py-3 font-medium">Time</th>
                                        <th class="px-5 py-3 font-medium">Location</th>
                                        <th class="px-5 py-3 font-medium">Device ID</th>
                                        <th class="px-5 py-3 font-medium">Type</th>
                                        <th class="px-5 py-3 font-medium">Status</th>
                                        <th class="px-5 py-3 font-medium">Patient</th>
                                        <th class="px-5 py-3 font-medium text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-xs text-brand-dark">
                                    <tr class="border-b border-brand-border/50 hover:bg-brand-hover transition-colors">
                                        <td class="px-5 py-3 text-brand-text">1</td>
                                        <td class="px-5 py-3">09:15 AM</td>
                                        <td class="px-5 py-3 text-brand-text">Gymnasium</td>
                                        <td class="px-5 py-3 text-brand-text">GYM-001</td>
                                        <td class="px-5 py-3"><span class="bg-brand-red text-white text-[9px] font-bold px-2 py-0.5 rounded">Critical Emergency</span></td>
                                        <td class="px-5 py-3 text-brand-green font-medium">Resolved</td>
                                        <td class="px-5 py-3 text-brand-dark">Yes</td>
                                        <td class="px-5 py-3 text-center"><button class="text-brand-blue hover:text-blue-400"><svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button></td>
                                    </tr>
                                    <tr class="border-b border-brand-border/50 hover:bg-brand-hover transition-colors">
                                        <td class="px-5 py-3 text-brand-text">2</td>
                                        <td class="px-5 py-3">08:42 AM</td>
                                        <td class="px-5 py-3 text-brand-text">Library</td>
                                        <td class="px-5 py-3 text-brand-text">LIB-001</td>
                                        <td class="px-5 py-3"><span class="bg-brand-red text-white text-[9px] font-bold px-2 py-0.5 rounded">Critical Emergency</span></td>
                                        <td class="px-5 py-3 text-brand-green font-medium">Resolved</td>
                                        <td class="px-5 py-3 text-brand-dark">Yes</td>
                                        <td class="px-5 py-3 text-center"><button class="text-brand-blue hover:text-blue-400"><svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button></td>
                                    </tr>
                                    <tr class="border-b border-brand-border/50 hover:bg-brand-hover transition-colors">
                                        <td class="px-5 py-3 text-brand-text">3</td>
                                        <td class="px-5 py-3">07:55 AM</td>
                                        <td class="px-5 py-3 text-brand-text">Engineering Building</td>
                                        <td class="px-5 py-3 text-brand-text">ENG-001</td>
                                        <td class="px-5 py-3"><span class="bg-brand-red text-white text-[9px] font-bold px-2 py-0.5 rounded">Critical Emergency</span></td>
                                        <td class="px-5 py-3 text-brand-green font-medium">Resolved</td>
                                        <td class="px-5 py-3 text-brand-text">No</td>
                                        <td class="px-5 py-3 text-center"><button class="text-brand-blue hover:text-blue-400"><svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button></td>
                                    </tr>
                                    <tr class="hover:bg-brand-hover transition-colors">
                                        <td class="px-5 py-3 text-brand-text">4</td>
                                        <td class="px-5 py-3">06:30 AM</td>
                                        <td class="px-5 py-3 text-brand-text">Gymnasium</td>
                                        <td class="px-5 py-3 text-brand-text">GYM-001</td>
                                        <td class="px-5 py-3"><span class="bg-brand-red text-white text-[9px] font-bold px-2 py-0.5 rounded">Critical Emergency</span></td>
                                        <td class="px-5 py-3 text-brand-red font-medium">Cancelled</td>
                                        <td class="px-5 py-3 text-brand-text">No</td>
                                        <td class="px-5 py-3 text-center"><button class="text-brand-blue hover:text-blue-400"><svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Cards -->
                <div class="flex flex-col gap-6">
                    <!-- Clinic Alarm Status -->
                    <div class="bg-brand-card border border-brand-border rounded-xl flex flex-col">
                        <div class="px-5 py-4 flex items-center justify-between border-b border-brand-border">
                            <h2 class="text-xs font-bold text-brand-dark uppercase tracking-wider">CLINIC ALARM STATUS</h2>
                            <span class="bg-brand-green/20 text-brand-green text-[9px] font-bold px-2 py-1 rounded border border-brand-green/30">ONLINE</span>
                        </div>
                        <div class="p-5 flex items-center">
                            <div class="w-14 h-14 rounded-full bg-brand-green/20 flex items-center justify-center mr-4 border border-brand-green/30 relative">
                                <div class="absolute inset-0 rounded-full border border-brand-green/50 animate-ping opacity-20"></div>
                                <svg class="w-7 h-7 text-brand-green" fill="currentColor" viewBox="0 0 24 24"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>
                            </div>
                            <div>
                                <div class="text-brand-dark font-bold text-sm mb-1">Alarm System Active</div>
                                <div class="text-brand-text text-[11px] leading-snug">You will be alerted for<br>critical emergencies only.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Equipment Readiness -->
                    <div class="bg-brand-card border border-brand-border rounded-xl flex flex-col">
                        <div class="px-5 py-4 flex items-center justify-between border-b border-brand-border">
                            <h2 class="text-xs font-bold text-brand-dark uppercase tracking-wider">EQUIPMENT READINESS</h2>
                            <a href="#" class="text-[10px] text-brand-blue hover:text-blue-400">View All</a>
                        </div>
                        <div class="p-5 flex flex-col gap-3 flex-1">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-brand-text text-xs">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    First Aid Kits
                                </div>
                                <span class="text-brand-green font-medium text-xs">Ready</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-brand-text text-xs">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                    Stretcher
                                </div>
                                <span class="text-brand-green font-medium text-xs">Ready</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-brand-text text-xs">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    Wheelchair
                                </div>
                                <span class="text-brand-green font-medium text-xs">Ready</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-brand-text text-xs">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                    Oxygen Tank
                                </div>
                                <span class="text-brand-green font-medium text-xs">Ready</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-brand-text text-xs">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                    AED (Defibrillator)
                                </div>
                                <span class="text-brand-green font-medium text-xs">Ready</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-brand-card border border-brand-border rounded-xl flex flex-col">
                        <div class="px-5 py-4 border-b border-brand-border">
                            <h2 class="text-xs font-bold text-brand-dark uppercase tracking-wider">QUICK ACTIONS</h2>
                        </div>
                        <div class="p-4 grid grid-cols-2 gap-3">
                            <button class="bg-[#31165A] border border-[#5B21B6]/50 hover:bg-[#4C1D95] text-brand-dark rounded-lg flex items-center justify-center py-3 px-2 transition-colors">
                                <svg class="w-5 h-5 mr-2 text-[#A78BFA]" fill="currentColor" viewBox="0 0 24 24"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>
                                <span class="font-medium text-sm">Test Alarm</span>
                            </button>
                            <button class="bg-brand-blue hover:bg-blue-700 text-white rounded-lg flex items-center justify-center py-3 px-2 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                <span class="font-medium text-sm">Notify NDRRMO</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
@endsection
