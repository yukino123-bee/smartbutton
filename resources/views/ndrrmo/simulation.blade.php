@extends('layouts.ndrrmo')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-brand-dark mb-1">Device Simulation</h2>
    <p class="text-xs text-brand-text">Every active device is shown below with its own buttons, so you can press buttons on several devices at the same time.</p>
</div>

<div class="mb-4 px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold">
    These presses create real incidents, labelled <strong>[Simulation]</strong> in the remarks. Close test alerts as <strong>Drill</strong> when done.
</div>

@if($devices->isEmpty())
    <div class="panel p-6 text-center text-xs text-brand-text">No active devices. Add or activate one on the Device Status page.</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 gap-5">
    @foreach($devices as $device)
    <div class="panel p-4 sim-device" data-device="{{ $device->device_code }}">
        <div class="flex items-center justify-between mb-3">
            <div>
                <div class="font-black text-sm text-brand-dark">{{ $device->device_code }}</div>
                <div class="text-[11px] text-brand-text">{{ $device->building }}</div>
            </div>
            <div class="flex items-center gap-3 text-[10px] font-bold">
                <span class="flex items-center gap-1"><span class="sim-led-green w-3 h-3 rounded-full bg-slate-300"></span>Server</span>
                <span class="flex items-center gap-1"><span class="sim-led-red w-3 h-3 rounded-full bg-slate-300"></span>Alarm</span>
                <span class="sim-buzzer px-2 py-1 rounded-lg bg-slate-100 text-slate-500">Buzzer off</span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <button type="button" data-sim="Critical Emergency" data-clinic="true" class="sim-btn col-span-2 py-4 rounded-2xl bg-red-600 hover:bg-red-700 active:scale-95 text-white font-extrabold text-sm shadow-md transition-all cursor-pointer">Critical (GPIO 14)</button>
            <button type="button" data-sim="Medical Emergency" data-clinic="false" class="sim-btn py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-extrabold text-xs shadow-md transition-all cursor-pointer">Medical (GPIO 12)</button>
            <button type="button" data-sim="Public Safety Emergency" data-clinic="false" class="sim-btn py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-extrabold text-xs shadow-md transition-all cursor-pointer">Public Safety (GPIO 27)</button>
            <button type="button" data-sim="Medical Emergency" data-clinic="true" class="sim-btn col-span-2 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-extrabold text-xs shadow-md transition-all cursor-pointer">Clinic Yes (GPIO 32) — after a Medical alert</button>
        </div>

        <div class="sim-log mt-3 h-32 overflow-y-auto custom-scrollbar bg-slate-900 text-slate-100 rounded-xl p-2.5 font-mono text-[10px] space-y-0.5"></div>
    </div>
    @endforeach
</div>
<p class="text-[10px] text-brand-text mt-3">Like the real device, each device ignores its own presses within 5 s of its last alert. Other devices are not affected.</p>

<script>
(function () {
    const COOLDOWN_MS = 5000, POLL_MS = 1500;
    const EMERGENCY_URL = '{{ route('ndrrmo.simulation.emergency') }}';
    const STATUS_URL = '{{ route('ndrrmo.simulation.status') }}';
    const CSRF = '{{ csrf_token() }}';

    document.querySelectorAll('.sim-device').forEach(function (panel) {
        const code = panel.dataset.device;
        const logEl = panel.querySelector('.sim-log');
        const green = panel.querySelector('.sim-led-green');
        const red = panel.querySelector('.sim-led-red');
        const buzzer = panel.querySelector('.sim-buzzer');
        let lastAlert = 0, alarming = false, poll = null;

        function log(msg, cls) {
            const line = document.createElement('div');
            line.className = cls || '';
            line.textContent = new Date().toLocaleTimeString() + '  ' + msg;
            logEl.appendChild(line);
            logEl.scrollTop = logEl.scrollHeight;
        }

        function leds(serverOk) {
            green.className = 'sim-led-green w-3 h-3 rounded-full ' + (serverOk && !alarming ? 'bg-emerald-500' : 'bg-slate-300');
            red.className = 'sim-led-red w-3 h-3 rounded-full ' + (alarming || !serverOk ? 'bg-red-500' : 'bg-slate-300');
            buzzer.textContent = alarming ? 'Buzzer ON' : 'Buzzer off';
            buzzer.className = 'sim-buzzer px-2 py-1 rounded-lg ' + (alarming ? 'bg-red-100 text-red-700 animate-pulse' : 'bg-slate-100 text-slate-500');
        }

        function setAlarm(on) {
            alarming = on;
            clearInterval(poll);
            poll = on ? setInterval(checkStatus, POLL_MS) : null;
        }

        async function checkStatus() {
            try {
                const res = await fetch(STATUS_URL + '?device_id=' + encodeURIComponent(code), { headers: { Accept: 'application/json' } });
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

        panel.querySelectorAll('.sim-btn').forEach(function (btn) {
            btn.addEventListener('click', async function () {
                if (Date.now() - lastAlert < COOLDOWN_MS) {
                    log('[COOLDOWN] Press ignored, too soon.', 'text-amber-400');
                    return;
                }
                lastAlert = Date.now();
                const type = btn.dataset.sim;
                const needClinic = btn.dataset.clinic === 'true';
                log('[BUTTON] ' + type + (type === 'Medical Emergency' && needClinic ? ' + Clinic' : ''));
                try {
                    const res = await fetch(EMERGENCY_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': CSRF },
                        body: JSON.stringify({ device_id: code, emergency_category: type, need_clinic: needClinic }),
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

        leds(true);
        log('Virtual device ready.');
    });
})();
</script>
@endsection
