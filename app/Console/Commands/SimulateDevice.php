<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class SimulateDevice extends Command
{
    protected $signature = 'device:simulate
        {device=LIB-001 : Device code to act as}
        {--url=http://127.0.0.1:8000 : Server running the app}
        {--press=* : Press buttons without prompting: critical, medical, public, clinic}
        {--delay=6 : Seconds between scripted presses}
        {--cooldown=5 : Seconds the device ignores presses after an alert (firmware: 5)}';

    protected $description = 'Act as an ESP32 panic button: press buttons and watch the alarm until DRRMO acknowledges';

    private const BUTTONS = [
        '1' => ['Critical Emergency', true, 'critical'],
        '2' => ['Medical Emergency', false, 'medical'],
        '3' => ['Public Safety Emergency', false, 'public'],
        '4' => ['Medical Emergency', true, 'clinic'],
    ];

    private string $url;

    private bool $alarming = false;

    private float $lastAlert = 0;

    public function handle(): int
    {
        $this->url = rtrim($this->option('url'), '/');
        $device = $this->argument('device');

        $this->info("Simulating {$device} -> {$this->url}");

        $presses = $this->option('press');
        if ($presses) {
            return $this->runScript($device, $presses);
        }

        return $this->runInteractive($device);
    }

    private function runScript(string $device, array $presses): int
    {
        foreach ($presses as $i => $name) {
            $key = $this->keyFor($name);
            if (! $key) {
                $this->error("Unknown button '{$name}'. Use critical, medical, public or clinic.");

                return self::FAILURE;
            }
            if ($i > 0 && (int) $this->option('delay') > 0) {
                sleep((int) $this->option('delay'));
            }
            $this->press($device, $key);
        }
        $this->checkStatus($device);

        return self::SUCCESS;
    }

    private function runInteractive(string $device): int
    {
        $this->line('  1 Critical   2 Medical   3 Public Safety   4 Clinic (after Medical)   s status   q quit');

        while (true) {
            $this->output->write('> ');
            $read = [STDIN];
            $write = $except = null;
            // Wake every 1.5 s like the firmware's status poll while the alarm is on.
            $ready = stream_select($read, $write, $except, 1, 500000);

            if ($ready === 0 || $ready === false) {
                $this->output->write("\r");
                if ($this->alarming) {
                    $this->checkStatus($device);
                }

                continue;
            }

            $input = strtolower(trim((string) fgets(STDIN)));
            if ($input === 'q') {
                return self::SUCCESS;
            }
            if ($input === 's') {
                $this->checkStatus($device);
            } elseif (isset(self::BUTTONS[$input])) {
                $this->press($device, $input);
            } elseif ($input !== '') {
                $this->warn('Unknown input.');
            }
        }
    }

    private function press(string $device, string $key): void
    {
        [$type, $needClinic, $label] = self::BUTTONS[$key];

        if (microtime(true) - $this->lastAlert < (float) $this->option('cooldown')) {
            $this->warn("[COOLDOWN] {$label} press suppressed, too soon.");

            return;
        }
        $this->lastAlert = microtime(true);

        $this->line("[BUTTON] {$label} pressed");

        try {
            $response = $this->http(4)->post("{$this->url}/api/emergency", [
                'device_id' => $device,
                'emergency_category' => $type,
                'need_clinic' => $needClinic,
                'simulated' => true,
            ]);
        } catch (ConnectionException) {
            $this->error('[API ERROR] Server unreachable. The real device would send an SMS instead and sound the siren for 60 s.');
            $this->alarming = true;

            return;
        }

        if ($response->successful()) {
            $body = $response->json();
            $this->info("[API SUCCESS] HTTP {$response->status()}: ".($body['message'] ?? ''));
            if (! ($body['ignored'] ?? false)) {
                $this->alarming = true;
                $this->line('[ALARM] Siren on until DRRMO acknowledges.');
            }
        } else {
            $this->error("[API ERROR] HTTP {$response->status()}: ".($response->json('message') ?? $response->body()));
            $this->alarming = true;
        }
    }

    private function checkStatus(string $device): void
    {
        try {
            $response = $this->http(2)->get("{$this->url}/api/device/status", ['device_id' => $device]);
        } catch (ConnectionException) {
            $this->warn('[STATUS] Server unreachable (red LED).');

            return;
        }

        if (! $response->successful()) {
            $this->warn("[STATUS] HTTP {$response->status()}");

            return;
        }

        $status = $response->json();
        $this->line('[STATUS] green LED on; incident '.($status['incident_id'] ?? '-').' is '.($status['incident_status'] ?? 'none'));

        if ($this->alarming && (($status['drrmo_responded'] ?? false) || ! ($status['has_pending'] ?? false))) {
            $this->info('[ACKNOWLEDGED] DRRMO responded. Siren stopped, 3 confirmation beeps.');
            $this->alarming = false;
        }
    }

    private function http(int $timeout)
    {
        $request = Http::acceptJson()->timeout($timeout);
        $token = config('services.device.token');

        return $token ? $request->withHeaders(['X-Device-Token' => $token]) : $request;
    }

    private function keyFor(string $name): ?string
    {
        foreach (self::BUTTONS as $key => $button) {
            if ($button[2] === strtolower($name)) {
                return $key;
            }
        }

        return null;
    }
}
