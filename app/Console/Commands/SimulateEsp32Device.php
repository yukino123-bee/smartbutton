<?php

namespace App\Console\Commands;

use App\Events\EmergencyReported;
use App\Models\Device;
use App\Models\Incident;
use App\Models\Notification;
use Illuminate\Console\Command;

class SimulateEsp32Device extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'device:simulate
                            {--device= : Device code (e.g. GYM-001)}
                            {--category= : Emergency category (Critical Emergency, Medical Emergency, Public Safety Emergency)}
                            {--all : Trigger emergency alert across all registered locations simultaneously}
                            {--list : List all registered devices}
                            {--new : Register a new temporary device location}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate physical ESP32 Smart Panic Button signal triggers across any or all campus locations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->line('');
        $this->info('===========================================================');
        $this->info('   ⚡ SMART PANIC BUTTON - ESP32 SIGNAL SIMULATOR ⚡       ');
        $this->info('===========================================================');
        $this->line('');

        if ($this->option('list')) {
            return $this->listDevices();
        }

        if ($this->option('new')) {
            return $this->registerNewDevice();
        }

        if ($this->option('all')) {
            return $this->triggerAllLocations();
        }

        return $this->triggerSingleDevice();
    }

    protected function listDevices(): int
    {
        $devices = Device::all();
        if ($devices->isEmpty()) {
            $this->warn('No devices registered. Use --new to create one.');
            return self::SUCCESS;
        }

        $headers = ['ID', 'Device Code', 'Building', 'Floor / Room', 'Coordinates', 'Status', 'Last Seen'];
        $rows = $devices->map(fn($d) => [
            $d->id,
            $d->device_code,
            $d->building,
            "{$d->floor} · {$d->room}",
            "{$d->latitude}, {$d->longitude}",
            $d->status,
            $d->last_seen ? $d->last_seen->diffForHumans() : 'Never',
        ]);

        $this->table($headers, $rows);
        return self::SUCCESS;
    }

    protected function registerNewDevice(): int
    {
        $this->info('Registering a new temporary device location:');

        $building = $this->ask('Building Name (e.g. Science Complex, Cafeteria)', 'Science Complex');
        $room = $this->ask('Room or Area (e.g. Lab 102, Main Dining Hall)', 'Room 101');
        $floor = $this->ask('Floor Level (e.g. Ground Floor, 2nd Floor)', '1st Floor');

        $defaultCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $building), 0, 3) . '-001');
        $deviceCode = strtoupper($this->ask('Device Code', $defaultCode));

        $latitude = $this->ask('Latitude', '7.708601');
        $longitude = $this->ask('Longitude', '123.292456');

        $device = Device::create([
            'device_code' => $deviceCode,
            'building' => $building,
            'floor' => $floor,
            'room' => $room,
            'latitude' => (float) $latitude,
            'longitude' => (float) $longitude,
            'status' => 'active',
            'last_seen' => now(),
        ]);

        $this->info("✓ Successfully created device [{$device->device_code}] for location '{$device->building}'.");
        return self::SUCCESS;
    }

    protected function triggerSingleDevice(): int
    {
        $deviceCode = $this->option('device');
        if (! $deviceCode) {
            $devices = Device::where('status', 'active')->pluck('building', 'device_code')->toArray();
            if (empty($devices)) {
                $this->error('No active devices available. Run `php artisan device:simulate --new` first.');
                return self::FAILURE;
            }

            $choices = [];
            foreach ($devices as $code => $bld) {
                $choices[$code] = "{$code} - {$bld}";
            }

            $deviceCode = $this->choice('Select target location device to trigger', $choices);
        }

        $device = Device::where('device_code', $deviceCode)->first();
        if (! $device) {
            $this->error("Device [{$deviceCode}] not found in database.");
            return self::FAILURE;
        }

        $category = $this->option('category');
        if (! $category || ! in_array($category, Incident::EMERGENCY_TYPES, true)) {
            $category = $this->choice('Select Emergency Category', Incident::EMERGENCY_TYPES, 0);
        }

        $this->line("[ESP32-HARDWARE] Simulating GPIO button interrupt on {$device->device_code}...");
        $this->line("[RADIO-TX] Transmitting HTTP POST to /api/emergency...");

        $device->update(['last_seen' => now()]);

        $incident = Incident::create([
            'device_id' => $device->id,
            'emergency_type' => $category,
            'reported_at' => now(),
            'status' => 'Pending',
            'remarks' => 'Triggered via Artisan CLI Simulator',
        ]);

        Notification::create([
            'incident_id' => $incident->id,
            'recipient' => 'DRRMO',
            'channel' => 'Dashboard',
            'status' => 'Delivered',
            'sent_at' => now(),
        ]);

        if (in_array($category, [Incident::TYPE_CRITICAL, Incident::TYPE_MEDICAL], true)) {
            Notification::create([
                'incident_id' => $incident->id,
                'recipient' => 'Clinic',
                'channel' => 'Dashboard',
                'status' => 'Delivered',
                'sent_at' => now(),
            ]);
        }

        broadcast(new EmergencyReported($incident));

        $this->info("✓ [201 CREATED] Emergency alert triggered successfully!");
        $this->table(
            ['Field', 'Details'],
            [
                ['Incident ID', "#{$incident->id}"],
                ['Location', $device->building . ' (' . ($device->room ?? 'N/A') . ')'],
                ['Device Code', $device->device_code],
                ['Category', $incident->emergency_type],
                ['Status', 'Pending (Siren & Flash Active)'],
                ['GPS Coordinates', "{$device->latitude}, {$device->longitude}"],
                ['Broadcast Channel', 'emergencies (WebSockets/Reverb)'],
            ]
        );

        $this->comment('NDRRMO & Clinic dashboard consoles will now sound alarm and display response workflow.');
        return self::SUCCESS;
    }

    protected function triggerAllLocations(): int
    {
        $devices = Device::where('status', 'active')->get();
        if ($devices->isEmpty()) {
            $this->error('No active devices found.');
            return self::FAILURE;
        }

        $category = $this->option('category') ?: Incident::TYPE_CRITICAL;

        $this->warn("⚡ Triggering alerts across ALL {$devices->count()} active locations simultaneously...");
        $bar = $this->output->createProgressBar($devices->count());
        $bar->start();

        $count = 0;
        foreach ($devices as $device) {
            $device->update(['last_seen' => now()]);

            $incident = Incident::create([
                'device_id' => $device->id,
                'emergency_type' => $category,
                'reported_at' => now(),
                'status' => 'Pending',
                'remarks' => 'Multi-location cascade triggered via Artisan CLI Simulator',
            ]);

            Notification::create([
                'incident_id' => $incident->id,
                'recipient' => 'DRRMO',
                'channel' => 'Dashboard',
                'status' => 'Delivered',
                'sent_at' => now(),
            ]);

            if (in_array($category, [Incident::TYPE_CRITICAL, Incident::TYPE_MEDICAL], true)) {
                Notification::create([
                    'incident_id' => $incident->id,
                    'recipient' => 'Clinic',
                    'channel' => 'Dashboard',
                    'status' => 'Delivered',
                    'sent_at' => now(),
                ]);
            }

            broadcast(new EmergencyReported($incident));
            $count++;
            $bar->advance();
            usleep(150000); // 150ms stagger
        }

        $bar->finish();
        $this->line('');
        $this->info("✓ Successfully dispatched {$count} emergency alerts across every campus building!");
        $this->comment('Multi-alert modal queues and stepper consoles are now active on all connected dashboards.');

        return self::SUCCESS;
    }
}
