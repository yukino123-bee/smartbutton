<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ESP32 Emergency Call Box Simulator | Smart Panic Button</title>

    <link rel="icon" href="/favicon.ico">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800|jet-brains-mono:400,600,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-mono-code {
            font-family: 'JetBrains Mono', monospace, ui-monospace;
        }

        /* Physical Box Styling */
        .callbox-enclosure {
            background: linear-gradient(145deg, #1e293b, #0f172a);
            border: 4px solid #334155;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7), inset 0 2px 4px rgba(255, 255, 255, 0.1);
        }

        .side-panel {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            border-left: 3px solid #475569;
            box-shadow: inset 4px 0 8px rgba(0, 0, 0, 0.4);
        }

        /* LED Lights */
        .led-green-off {
            background-color: #14532d;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.8);
        }
        .led-green-on {
            background-color: #22c55e;
            box-shadow: 0 0 16px #22c55e, 0 0 30px #22c55e, inset 0 -2px 4px rgba(0,0,0,0.3);
        }

        .led-red-off {
            background-color: #450a0a;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.8);
        }
        .led-red-on {
            background-color: #ef4444;
            box-shadow: 0 0 18px #ef4444, 0 0 35px #dc2626, inset 0 -2px 4px rgba(0,0,0,0.3);
        }

        /* Piezo Buzzer Grill */
        .buzzer-grill {
            background: radial-gradient(circle, #0f172a 30%, #334155 70%, #1e293b 100%);
            box-shadow: inset 0 3px 6px rgba(0, 0, 0, 0.8), 0 2px 4px rgba(255, 255, 255, 0.1);
        }

        /* Big Red Critical Button */
        .big-critical-btn {
            background: radial-gradient(circle at 35% 35%, #ef4444 0%, #b91c1c 65%, #7f1d1d 100%);
            box-shadow: 0 10px 20px -3px rgba(239, 68, 68, 0.6), inset 0 3px 6px rgba(255, 255, 255, 0.4), inset 0 -4px 8px rgba(0, 0, 0, 0.6);
            transition: all 0.08s ease;
        }
        .big-critical-btn:hover {
            background: radial-gradient(circle at 35% 35%, #f87171 0%, #dc2626 65%, #991b1b 100%);
            box-shadow: 0 12px 24px -2px rgba(239, 68, 68, 0.8), inset 0 3px 6px rgba(255, 255, 255, 0.5);
        }
        .big-critical-btn:active {
            transform: translateY(4px) scale(0.97);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.8), inset 0 2px 6px rgba(0, 0, 0, 0.7);
        }

        /* Small Buttons */
        .small-medical-btn {
            background: radial-gradient(circle at 35% 35%, #3b82f6 0%, #1d4ed8 70%, #1e3a8a 100%);
            box-shadow: 0 6px 14px -2px rgba(59, 130, 246, 0.5), inset 0 2px 4px rgba(255, 255, 255, 0.3), inset 0 -3px 6px rgba(0, 0, 0, 0.5);
        }
        .small-medical-btn:hover {
            background: radial-gradient(circle at 35% 35%, #60a5fa 0%, #2563eb 70%, #1d4ed8 100%);
        }
        .small-medical-btn:active {
            transform: translateY(2px) scale(0.97);
        }

        .small-safety-btn {
            background: radial-gradient(circle at 35% 35%, #f59e0b 0%, #d97706 70%, #78350f 100%);
            box-shadow: 0 6px 14px -2px rgba(245, 158, 11, 0.5), inset 0 2px 4px rgba(255, 255, 255, 0.3), inset 0 -3px 6px rgba(0, 0, 0, 0.5);
        }
        .small-safety-btn:hover {
            background: radial-gradient(circle at 35% 35%, #fbbf24 0%, #d97706 70%, #92400e 100%);
        }
        .small-safety-btn:active {
            transform: translateY(2px) scale(0.97);
        }

        /* Side Buttons */
        .side-btn-yes {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3), inset 0 1px 2px rgba(255, 255, 255, 0.3);
        }
        .side-btn-yes:hover {
            background: linear-gradient(135deg, #34d399 0%, #059669 100%);
        }
        .side-btn-no {
            background: linear-gradient(135deg, #64748b 0%, #334155 100%);
            box-shadow: 0 4px 10px rgba(100, 116, 139, 0.3), inset 0 1px 2px rgba(255, 255, 255, 0.2);
        }
        .side-btn-no:hover {
            background: linear-gradient(135deg, #94a3b8 0%, #475569 100%);
        }

        /* Soundwave animation */
        @keyframes soundwave {
            0% { transform: scale(1); opacity: 0.8; }
            100% { transform: scale(2.2); opacity: 0; }
        }
        .soundwave-active {
            animation: soundwave 0.6s infinite ease-out;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans select-none">

    <!-- Top Bar: Location Switcher & Console Links -->
    <header class="bg-slate-900/90 border-b border-slate-800 backdrop-blur-md px-4 sm:px-6 py-3 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto flex flex-wrap items-center justify-between gap-4">
            <!-- Left: Location Dropdown -->
            <div class="flex items-center gap-3">
                <span class="text-xl">📍</span>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Device Location:</span>
                    <select id="device-selector" onchange="onLocationChange()" class="bg-slate-950 border border-slate-700 text-white font-bold text-xs rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-blue-500">
                        @foreach($devices as $device)
                            <option value="{{ $device->device_code }}" 
                                    data-building="{{ $device->building }}"
                                    data-room="{{ $device->room }}"
                                    data-code="{{ $device->device_code }}"
                                    {{ $loop->first ? 'selected' : '' }}>
                                {{ $device->building }} ({{ $device->device_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Right: Live Links to Dashboards -->
            <div class="flex items-center gap-2">
                <a href="{{ route('ndrrmo.dashboard') }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 transition-all flex items-center gap-1.5 shadow-sm">
                    <span>Open NDRRMO</span>
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
                <a href="{{ route('clinic.dashboard') }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 transition-all flex items-center gap-1.5 shadow-sm">
                    <span>Open Clinic</span>
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </div>
    </header>

    <!-- Main View: The Physical Call Box -->
    <main class="flex-1 flex flex-col items-center justify-center p-4 sm:p-6">

        <!-- Witness Feedback / System Status Banner -->
        <div id="witness-status-card" class="w-full max-w-lg mb-6 rounded-2xl p-4 text-center transition-all duration-300 border bg-slate-900 border-slate-800 shadow-lg">
            <div id="status-title-row" class="flex items-center justify-center gap-2 mb-1">
                <span id="status-indicator-dot" class="w-3 h-3 rounded-full bg-emerald-500"></span>
                <span id="status-headline" class="text-sm font-black uppercase tracking-wider text-emerald-400">
                    CALL BOX READY — STANDBY MODE
                </span>
            </div>
            <p id="status-description" class="text-xs text-slate-400 leading-relaxed font-medium">
                Push button in case of emergency. The buzzer will beep continuously until the DRRMO command center acknowledges the alert.
            </p>
        </div>

        <!-- The Physical Box (with Side Panel attached) -->
        <div class="relative flex items-stretch max-w-xl w-full justify-center">

            <!-- MAIN CALL BOX ENCLOSURE -->
            <div class="callbox-enclosure w-full max-w-md rounded-[2.5rem] p-6 sm:p-8 flex flex-col items-center relative z-10">

                <!-- 1. TOP SECTION: Red & Green Light Indicators + Center Piezo Buzzer -->
                <div class="w-full flex items-center justify-between px-4 mb-8">
                    <!-- Left: Green Light Indicator -->
                    <div class="flex flex-col items-center gap-1.5">
                        <div id="light-green" class="w-7 h-7 rounded-full led-green-on border-2 border-emerald-900 transition-all duration-200"></div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">NORMAL</span>
                    </div>

                    <!-- Center: Piezo Buzzer with sound grill -->
                    <div class="relative flex flex-col items-center">
                        <div id="buzzer-pulse-ring" class="absolute -inset-3 rounded-full border-2 border-red-500 opacity-0 pointer-events-none"></div>
                        <div class="buzzer-grill w-14 h-14 rounded-full flex items-center justify-center border-2 border-slate-600 shadow-inner relative">
                            <!-- Speaker grill perforations -->
                            <div class="grid grid-cols-3 gap-1.5 p-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-900 shadow-xs"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-900 shadow-xs"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-900 shadow-xs"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-900 shadow-xs"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-900 shadow-xs"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-900 shadow-xs"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-900 shadow-xs"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-900 shadow-xs"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-900 shadow-xs"></span>
                            </div>
                        </div>
                        <span id="buzzer-label" class="text-[9px] font-black uppercase tracking-widest text-slate-400 mt-1">BUZZER</span>
                    </div>

                    <!-- Right: Red Light Indicator -->
                    <div class="flex flex-col items-center gap-1.5">
                        <div id="light-red" class="w-7 h-7 rounded-full led-red-off border-2 border-red-950 transition-all duration-200"></div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">ALARM</span>
                    </div>
                </div>

                <!-- 2. BOX CENTER: One Big Red Button for Critical Emergency -->
                <div class="flex flex-col items-center justify-center my-2">
                    <div class="w-48 h-48 rounded-full p-3.5 bg-slate-800/80 border-4 border-dashed border-red-600/50 flex items-center justify-center shadow-2xl">
                        <button type="button" id="btn-critical" onclick="triggerEmergency('Critical Emergency')"
                                class="big-critical-btn w-full h-full rounded-full flex flex-col items-center justify-center text-white cursor-pointer active:scale-95 group relative select-none">
                            <span class="text-3xl mb-1 filter drop-shadow-md group-hover:scale-110 transition-transform">🚨</span>
                            <span class="text-base font-black tracking-wider uppercase drop-shadow-md">CRITICAL</span>
                            <span class="text-[9px] font-extrabold text-red-200 uppercase tracking-widest">EMERGENCY</span>
                        </button>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">PUSH FOR CRITICAL CRISIS</span>
                </div>

                <!-- 3. BELOW THE BIG RED BUTTON: Two Small Buttons for Medical & Public Safety -->
                <div class="w-full mt-6 pt-5 border-t border-slate-800">
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Small Button 1: Medical -->
                        <div class="flex flex-col items-center">
                            <button type="button" id="btn-medical" onclick="triggerMedicalAction()"
                                    class="small-medical-btn w-full py-3.5 px-3 rounded-2xl text-white font-extrabold text-xs flex flex-col items-center justify-center gap-1 cursor-pointer transition-all border border-blue-400/40">
                                <span class="text-lg">🏥</span>
                                <span class="tracking-wide">MEDICAL</span>
                            </button>
                            <span class="text-[9px] font-bold text-slate-400 mt-1 uppercase">FIRST AID / INJURY</span>
                        </div>

                        <!-- Small Button 2: Public Safety -->
                        <div class="flex flex-col items-center">
                            <button type="button" id="btn-safety" onclick="triggerEmergency('Public Safety Emergency')"
                                    class="small-safety-btn w-full py-3.5 px-3 rounded-2xl text-white font-extrabold text-xs flex flex-col items-center justify-center gap-1 cursor-pointer transition-all border border-amber-400/40">
                                <span class="text-lg">🛡️</span>
                                <span class="tracking-wide">PUBLIC SAFETY</span>
                            </button>
                            <span class="text-[9px] font-bold text-slate-400 mt-1 uppercase">SECURITY / HAZARD</span>
                        </div>
                    </div>
                </div>

                <!-- Active Location & Device Tag on Bottom -->
                <div class="mt-6 pt-3 border-t border-slate-800/80 w-full flex items-center justify-between text-[10px] font-mono text-slate-400">
                    <span>UNIT: <strong id="box-device-code" class="text-slate-200">GYM-001</strong></span>
                    <span>LOC: <strong id="box-device-name" class="text-slate-200">Gymnasium</strong></span>
                </div>
            </div>

            <!-- 4. BOX SIDE: 2 Other Buttons for Clinic Aid (When Medical Pushed) -->
            <div class="side-panel w-28 sm:w-32 rounded-r-3xl p-3 sm:p-4 flex flex-col justify-center items-center gap-3 shrink-0 relative my-4 -ml-2 z-0 border-y border-r border-slate-700">
                
                <div class="text-center pb-2 border-b border-slate-700/80 w-full">
                    <span class="text-[8px] font-black uppercase tracking-wider text-cyan-400 block leading-tight">CLINIC AID</span>
                    <span class="text-[7px] text-slate-400 font-bold block">(FOR MEDICAL)</span>
                </div>

                <!-- Side Button 1: TOP (NEED CLINIC AID) -->
                <div class="w-full flex flex-col items-center">
                    <button type="button" id="side-btn-clinic-yes" onclick="triggerMedicalWithClinic(true)"
                            class="side-btn-yes w-full py-3 px-2 rounded-xl text-white font-black text-[10px] uppercase tracking-tight flex flex-col items-center justify-center text-center cursor-pointer transition-all active:scale-95 border border-emerald-400/40">
                        <span class="text-xs mb-0.5">🏥✓</span>
                        <span class="leading-tight">NEED CLINIC AID</span>
                    </button>
                    <span class="text-[7px] text-slate-400 mt-0.5 font-bold uppercase">TOP BUTTON</span>
                </div>

                <!-- Side Button 2: BOTTOM (NO CLINIC AID NEEDED) -->
                <div class="w-full flex flex-col items-center mt-1">
                    <button type="button" id="side-btn-clinic-no" onclick="triggerMedicalWithClinic(false)"
                            class="side-btn-no w-full py-3 px-2 rounded-xl text-slate-200 font-black text-[10px] uppercase tracking-tight flex flex-col items-center justify-center text-center cursor-pointer transition-all active:scale-95 border border-slate-500/40">
                        <span class="text-xs mb-0.5">🛡️✕</span>
                        <span class="leading-tight">NO CLINIC NEEDED</span>
                    </button>
                    <span class="text-[7px] text-slate-400 mt-0.5 font-bold uppercase">BOTTOM BUTTON</span>
                </div>

                <div class="text-[7px] text-slate-400 text-center leading-tight mt-1 border-t border-slate-700/60 pt-2 w-full">
                    Use when Medical pushed
                </div>
            </div>

        </div>

        <!-- Testing Simulation Controls (Acknowledge from Console / Silence Test) -->
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <button type="button" id="simulate-ack-btn" onclick="simulateDRRMOAcknowledgement()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 active:scale-95 text-xs font-bold text-emerald-400 border border-slate-700 transition-all flex items-center gap-2 cursor-pointer shadow-sm">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>Simulate DRRMO Response (Acknowledge)</span>
            </button>
            <button type="button" onclick="resetCallBox()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 active:scale-95 text-xs font-bold text-slate-300 border border-slate-700 transition-all flex items-center gap-1.5 cursor-pointer shadow-sm">
                <span>🔄 Reset Box to Normal</span>
            </button>
        </div>

    </main>

    <!-- Embedded Scripts for Device Beeper & DRRMO Response Listener -->
    <script>
        let audioCtx = null;
        let beeperInterval = null;
        let isDeviceBeeping = false;
        let currentActiveIncidentId = null;
        let statusPollInterval = null;

        function getAudioContext() {
            if (!audioCtx) {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                audioCtx = new AudioContext();
            }
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            return audioCtx;
        }

        // Mechanical Button Click Audio
        function playButtonClickSound() {
            try {
                const ctx = getAudioContext();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'triangle';
                osc.frequency.setValueAtTime(150, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(30, ctx.currentTime + 0.08);

                gain.gain.setValueAtTime(0.4, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.08);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start();
                osc.stop(ctx.currentTime + 0.09);
            } catch(e) {}
        }

        // Start Physical Beeper (Beeps continuously until DRRMO responds!)
        function startDeviceBeeper() {
            if (isDeviceBeeping) return;
            isDeviceBeeping = true;

            const pulseRing = document.getElementById('buzzer-pulse-ring');
            if (pulseRing) pulseRing.classList.add('soundwave-active');

            // Play single beep tone
            function playSingleBeep() {
                try {
                    const ctx = getAudioContext();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();

                    // Standard loud piezoelectric alarm frequency (2400 Hz)
                    osc.type = 'square';
                    osc.frequency.setValueAtTime(2400, ctx.currentTime);

                    gain.gain.setValueAtTime(0.35, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.18);

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.start();
                    osc.stop(ctx.currentTime + 0.20);
                } catch(e) {}
            }

            playSingleBeep();
            beeperInterval = setInterval(playSingleBeep, 600);
        }

        // Stop Physical Beeper
        function stopDeviceBeeper() {
            isDeviceBeeping = false;
            if (beeperInterval) {
                clearInterval(beeperInterval);
                beeperInterval = null;
            }
            const pulseRing = document.getElementById('buzzer-pulse-ring');
            if (pulseRing) pulseRing.classList.remove('soundwave-active');
        }

        // Play Reassuring Confirmation Chime when DRRMO Responds
        function playDRRMORespondedChime() {
            try {
                const ctx = getAudioContext();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                // Two-tone rising chime (880 Hz -> 1760 Hz)
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, ctx.currentTime);
                osc.frequency.setValueAtTime(1760, ctx.currentTime + 0.15);

                gain.gain.setValueAtTime(0.4, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.45);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start();
                osc.stop(ctx.currentTime + 0.46);
            } catch(e) {}
        }

        // Location change handler
        function getSelectedDeviceCode() {
            const sel = document.getElementById('device-selector');
            return sel ? sel.value : 'GYM-001';
        }

        function onLocationChange() {
            const sel = document.getElementById('device-selector');
            if (!sel) return;
            const opt = sel.options[sel.selectedIndex];
            if (!opt) return;

            const code = opt.value;
            const bld = opt.getAttribute('data-building') || code;

            document.getElementById('box-device-code').textContent = code;
            document.getElementById('box-device-name').textContent = bld;

            resetCallBox();
        }

        // Trigger Emergency from buttons
        async function triggerEmergency(category, needClinic = true) {
            playButtonClickSound();
            const deviceCode = getSelectedDeviceCode();

            // Set Visual Alarm State
            setAlarmActiveVisuals(category);

            // Start Beeper: Beeps until DRRMO responds!
            startDeviceBeeper();

            try {
                const response = await fetch('/api/emergency', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        device_id: deviceCode,
                        emergency_category: category,
                        need_clinic: needClinic,
                        timestamp: new Date().toISOString()
                    })
                });

                const data = await response.json();
                if (response.ok && data.incident_id) {
                    currentActiveIncidentId = data.incident_id;
                    startPollingForDRRMOResponse();
                }
            } catch(e) {
                console.error("Transmission error:", e);
            }
        }

        // Pushed Medical Button: Highlights the side buttons
        function triggerMedicalAction() {
            // Pulse the side buttons to invite user selection or default to with Clinic aid
            const sideYes = document.getElementById('side-btn-clinic-yes');
            const sideNo = document.getElementById('side-btn-clinic-no');
            
            [sideYes, sideNo].forEach(btn => {
                if (btn) {
                    btn.classList.add('ring-4', 'ring-cyan-400');
                    setTimeout(() => btn.classList.remove('ring-4', 'ring-cyan-400'), 1500);
                }
            });

            // Trigger Medical Emergency (defaulting to need Clinic aid unless specified by side buttons)
            triggerEmergency('Medical Emergency', true);
        }

        function triggerMedicalWithClinic(needClinic) {
            triggerEmergency('Medical Emergency', needClinic);
        }

        // Set Alarm Active Visuals on the Box
        function setAlarmActiveVisuals(category) {
            // Lights
            const lightGreen = document.getElementById('light-green');
            const lightRed = document.getElementById('light-red');
            if (lightGreen) lightGreen.className = 'w-7 h-7 rounded-full led-green-off border-2 border-emerald-950';
            if (lightRed) lightRed.className = 'w-7 h-7 rounded-full led-red-on border-2 border-red-500 animate-pulse';

            // Witness Banner
            const headline = document.getElementById('status-headline');
            const dot = document.getElementById('status-indicator-dot');
            const desc = document.getElementById('status-description');
            const card = document.getElementById('witness-status-card');

            if (card) card.className = 'w-full max-w-lg mb-6 rounded-2xl p-4 text-center transition-all duration-300 border bg-red-950/40 border-red-500/50 shadow-xl';
            if (dot) dot.className = 'w-3 h-3 rounded-full bg-red-500 animate-ping';
            if (headline) {
                headline.className = 'text-sm font-black uppercase tracking-wider text-red-400';
                headline.textContent = `🚨 ${category.toUpperCase()} ACTIVE — WAITING FOR DRRMO...`;
            }
            if (desc) {
                desc.textContent = `Device buzzer is beeping. Help signal sent to DRRMO Command Center. Witness: please remain nearby, beeping will stop as soon as DRRMO responds.`;
            }
        }

        // Handled: DRRMO Responded! (Acknowledge or Dispatch)
        function onDRRMOResponded() {
            stopDeviceBeeper();
            playDRRMORespondedChime();

            // Lights: Red solid, Green stays off
            const lightGreen = document.getElementById('light-green');
            const lightRed = document.getElementById('light-red');
            if (lightGreen) lightGreen.className = 'w-7 h-7 rounded-full led-green-off border-2 border-emerald-950';
            if (lightRed) lightRed.className = 'w-7 h-7 rounded-full led-red-on border-2 border-red-500';

            // Update witness banner to inform them that help is confirmed
            const headline = document.getElementById('status-headline');
            const dot = document.getElementById('status-indicator-dot');
            const desc = document.getElementById('status-description');
            const card = document.getElementById('witness-status-card');

            if (card) card.className = 'w-full max-w-lg mb-6 rounded-2xl p-4 text-center transition-all duration-300 border bg-emerald-950/40 border-emerald-500/50 shadow-xl';
            if (dot) dot.className = 'w-3 h-3 rounded-full bg-emerald-400';
            if (headline) {
                headline.className = 'text-sm font-black uppercase tracking-wider text-emerald-400';
                headline.textContent = '✅ DRRMO RESPONDED — HELP IS ON THE WAY!';
            }
            if (desc) {
                desc.textContent = 'Command Center acknowledged this alarm. Beeping silenced. Emergency personnel have been dispatched to this location.';
            }

            if (statusPollInterval) {
                clearInterval(statusPollInterval);
                statusPollInterval = null;
            }
        }

        // Reset Box back to Normal / Standby
        function resetCallBox() {
            stopDeviceBeeper();
            currentActiveIncidentId = null;
            if (statusPollInterval) {
                clearInterval(statusPollInterval);
                statusPollInterval = null;
            }

            const lightGreen = document.getElementById('light-green');
            const lightRed = document.getElementById('light-red');
            if (lightGreen) lightGreen.className = 'w-7 h-7 rounded-full led-green-on border-2 border-emerald-900';
            if (lightRed) lightRed.className = 'w-7 h-7 rounded-full led-red-off border-2 border-red-950';

            const headline = document.getElementById('status-headline');
            const dot = document.getElementById('status-indicator-dot');
            const desc = document.getElementById('status-description');
            const card = document.getElementById('witness-status-card');

            if (card) card.className = 'w-full max-w-lg mb-6 rounded-2xl p-4 text-center transition-all duration-300 border bg-slate-900 border-slate-800 shadow-lg';
            if (dot) dot.className = 'w-3 h-3 rounded-full bg-emerald-500';
            if (headline) {
                headline.className = 'text-sm font-black uppercase tracking-wider text-emerald-400';
                headline.textContent = 'CALL BOX READY — STANDBY MODE';
            }
            if (desc) {
                desc.textContent = 'Push button in case of emergency. The buzzer will beep continuously until the DRRMO command center acknowledges the alert.';
            }
        }

        // Background poller & listener to check when DRRMO acknowledges
        function startPollingForDRRMOResponse() {
            if (statusPollInterval) clearInterval(statusPollInterval);

            const deviceCode = getSelectedDeviceCode();
            statusPollInterval = setInterval(async () => {
                try {
                    const res = await fetch(`/api/device/status?device_id=${encodeURIComponent(deviceCode)}`);
                    const data = await res.json();
                    if (data && data.drrmo_responded) {
                        onDRRMOResponded();
                    }
                } catch(e) {}
            }, 1200);
        }

        // Simulate DRRMO response directly from simulator
        async function simulateDRRMOAcknowledgement() {
            if (currentActiveIncidentId) {
                try {
                    await fetch(`/api/incidents/${currentActiveIncidentId}/acknowledge`, { method: 'POST' });
                } catch(e) {}
            }
            onDRRMOResponded();
        }

        // Real-time WebSocket channel listener for instant DRRMO response
        document.addEventListener('DOMContentLoaded', () => {
            if (window.Echo) {
                window.Echo.channel('emergencies')
                    .listen('EmergencyReported', (e) => {
                        if (e && e.incident) {
                            const currentDevice = getSelectedDeviceCode();
                            const eventDevice = e.incident.device ? e.incident.device.device_code : null;

                            if (eventDevice === currentDevice) {
                                if (['Acknowledged', 'Responding', 'Resolved'].includes(e.incident.status)) {
                                    onDRRMOResponded();
                                }
                            }
                        }
                    });
            }
        });
    </script>
</body>
</html>
