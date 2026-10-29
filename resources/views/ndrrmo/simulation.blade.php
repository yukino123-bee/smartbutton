@extends('layouts.ndrrmo')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-brand-dark mb-1">Device Simulation</h2>
    <p class="text-xs text-brand-text">Press the buttons of a virtual panic device to test the system without the hardware.</p>
</div>

<div class="mb-4 px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold">
    These presses create real incidents. Close test alerts as <strong>Drill</strong> or <strong>False Alarm</strong> when done.
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="panel p-5">
        <label for="sim-device" class="block text-[10px] font-bold uppercase tracking-wider text-brand-text mb-1">Device</label>
        <select id="sim-device" class="ui-input mb-5">
            @foreach($devices as $device)
                <option value="{{ $device->device_code }}">{{ $device->device_code }} — {{ $device->building }}</option>
            @endforeach
        </select>

        <div class="flex items-center gap-4 mb-5 text-xs font-bold">
            <span class="flex items-center gap-2"><span id="sim-led-green" class="w-3 h-3 rounded-full bg-slate-300"></span>Green (server)</span>
            <span class="flex items-center gap-2"><span id="sim-led-red" class="w-3 h-3 rounded-full bg-slate-300"></span>Red (alarm)</span>
            <span id="sim-buzzer" class="ml-auto px-2 py-1 rounded-lg bg-slate-100 text-slate-500">Buzzer off</span>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <button type="button" data-sim="Critical Emergency" data-clinic="true" class="sim-btn col-span-2 py-5 rounded-2xl bg-red-600 hover:bg-red-700 active:scale-95 text-white font-extrabold text-sm shadow-md transition-all cursor-pointer">Critical (GPIO 14)</button>
            <button type="button" data-sim="Medical Emergency" data-clinic="false" class="sim-btn py-3 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-extrabold text-xs shadow-md transition-all cursor-pointer">Medical (GPIO 12)</button>
            <button type="button" data-sim="Public Safety Emergency" data-clinic="false" class="sim-btn py-3 rounded-xl bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-extrabold text-xs shadow-md transition-all cursor-pointer">Public Safety (GPIO 27)</button>
            <button type="button" data-sim="Medical Emergency" data-clinic="true" class="sim-btn col-span-2 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-extrabold text-xs shadow-md transition-all cursor-pointer">Clinic Yes (GPIO 32) — after a Medical alert</button>
        </div>
        <p class="text-[10px] text-brand-text mt-3">Like the real device, presses within 5 s of the last alert are ignored.</p>
    </div>

    <div class="panel p-5 flex flex-col">
        <h3 class="text-xs font-extrabold uppercase tracking-wider text-brand-dark mb-3">Device log</h3>
        <div id="sim-log" class="flex-1 min-h-64 max-h-96 overflow-y-auto custom-scrollbar bg-slate-900 text-slate-100 rounded-xl p-3 font-mono text-[11px] space-y-1"></div>
    </div>
</div>

<script>
(function () {
    const COOLDOWN_MS = 5000, POLL_MS = 1500;
    const deviceEl = document.getElementById('sim-device');
    const logEl = document.getElementById('sim-log');
    const green = document.getElementById('sim-led-green');
    const red = document.getElementById('sim-led-red');
    const buzzer = document.getElementById('sim-buzzer');
    let lastAlert = 0, alarming = false, poll = null;

    function log(msg, cls) {
        const line = document.createElement('div');
        line.className = cls || '';
        line.textContent = new Date().toLocaleTimeString() + '  ' + msg;
        logEl.appendChild(line);
        logEl.scrollTop = logEl.scrollHeight;
    }

    function leds(serverOk) {
        green.className = 'w-3 h-3 rounded-full ' + (serverOk && !alarming ? 'bg-emerald-500' : 'bg-slate-300');
        red.className = 'w-3 h-3 rounded-full ' + (alarming || !serverOk ? 'bg-red-500' : 'bg-slate-300');
        buzzer.textContent = alarming ? 'Buzzer ON' : 'Buzzer off';
        buzzer.className = 'ml-auto px-2 py-1 rounded-lg ' + (alarming ? 'bg-red-100 text-red-700 animate-pulse' : 'bg-slate-100 text-slate-500');
    }

    function setAlarm(on) {
        alarming = on;
        clearInterval(poll);
        poll = on ? setInterval(checkStatus, POLL_MS) : null;
    }

    async function checkStatus() {
        try {
            const res = await fetch('/api/device/status?device_id=' + encodeURIComponent(deviceEl.value), { headers: { Accept: 'application/json' } });
            const data = await res.json();
            if (alarming && (data.drrmo_responded || !data.has_pending)) {
                log('[ACKNOWLEDGED] DRRMO responded. Buzzer stopped.', 'text-emerald-400');
                setAlarm(false);
            }
            leds(res.ok);
        } catch (e) {
            leds(false);
            log('[STATUS] Server unreachable.', 'text-amber-400');
        }
    }

    document.querySelectorAll('.sim-btn').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            if (Date.now() - lastAlert < COOLDOWN_MS) {
                log('[COOLDOWN] Press ignored, too soon.', 'text-amber-400');
                return;
            }
            lastAlert = Date.now();
            const type = btn.dataset.sim;
            const needClinic = btn.dataset.clinic === 'true';
            log('[BUTTON] ' + type + (type === 'Medical Emergency' ? (needClinic ? ' + Clinic' : '') : ''));
            try {
                const res = await fetch('/api/emergency', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                    body: JSON.stringify({ device_id: deviceEl.value, emergency_category: type, need_clinic: needClinic }),
                });
                const data = await res.json();
                if (res.ok) {
                    log('[API ' + res.status + '] ' + data.message, 'text-emerald-400');
                    if (!data.ignored) { setAlarm(true); }
                } else {
                    log('[API ERROR ' + res.status + '] ' + (data.message || 'failed'), 'text-red-400');
                }
                leds(true);
            } catch (e) {
                log('[API ERROR] Server unreachable. The real device would send an SMS.', 'text-red-400');
                setAlarm(true);
                leds(false);
            }
        });
    });

    deviceEl.addEventListener('change', function () { setAlarm(false); leds(true); log('Switched to ' + deviceEl.value); });
    leds(true);
    log('Virtual device ready.');
})();
</script>
@endsection
