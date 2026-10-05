<?php

use App\Events\EmergencyReported;
use App\Models\Device;
use App\Models\Incident;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(LazilyRefreshDatabase::class);

function seedSimulatorDevice(string $code = 'GYM-001', string $building = 'Gymnasium'): Device
{
    return Device::create([
        'device_code' => $code,
        'building' => $building,
        'floor' => '1st Floor',
        'room' => 'Main Hall',
        'latitude' => 7.7115556,
        'longitude' => 123.2931667,
        'status' => 'active',
        'last_seen' => now(),
    ]);
}

test('simulator browser page renders successfully with hardware controls and active devices', function () {
    $device1 = seedSimulatorDevice('GYM-001', 'Gymnasium');
    $device2 = seedSimulatorDevice('ENG-001', 'Engineering Complex');

    $response = $this->get('/simulator');

    $response->assertSuccessful()
        ->assertSee('ESP32 Emergency Call Box Simulator')
        ->assertSee('GYM-001')
        ->assertSee('Engineering Complex')
        ->assertSee('CRITICAL')
        ->assertSee('MEDICAL')
        ->assertSee('PUBLIC SAFETY')
        ->assertSee('NEED CLINIC AID')
        ->assertSee('NO CLINIC NEEDED')
        ->assertSee('BUZZER');
});

test('medical emergency alert notifies clinic when need_clinic is true and suppresses clinic notification when false', function () {
    $device = seedSimulatorDevice('MED-01', 'Science Hall');

    // Case 1: Witness pushes Medical + NEED CLINIC AID (need_clinic: true)
    $resYes = $this->postJson('/api/emergency', [
        'device_id' => 'MED-01',
        'emergency_category' => Incident::TYPE_MEDICAL,
        'need_clinic' => true,
    ])->assertStatus(201);

    $incYesId = $resYes->json('incident_id');
    expect(Notification::where('incident_id', $incYesId)->where('recipient', 'Clinic')->exists())->toBeTrue()
        ->and(Notification::where('incident_id', $incYesId)->where('recipient', 'DRRMO')->exists())->toBeTrue();

    // Case 2: Witness pushes Medical + NO CLINIC AID NEEDED (need_clinic: false)
    $resNo = $this->postJson('/api/emergency', [
        'device_id' => 'MED-01',
        'emergency_category' => Incident::TYPE_MEDICAL,
        'need_clinic' => false,
    ])->assertStatus(201);

    $incNoId = $resNo->json('incident_id');
    expect(Notification::where('incident_id', $incNoId)->where('recipient', 'Clinic')->exists())->toBeFalse()
        ->and(Notification::where('incident_id', $incNoId)->where('recipient', 'DRRMO')->exists())->toBeTrue();
});

test('device status api returns drrmo_responded when incident is acknowledged', function () {
    $device = seedSimulatorDevice('ACK-01', 'Student Center');

    // Status when no pending alert
    $resInitial = $this->getJson('/api/device/status?device_id=ACK-01')->assertSuccessful();
    expect($resInitial->json('drrmo_responded'))->toBeFalse();

    // Create pending incident
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_CRITICAL,
        'status' => 'Pending',
        'reported_at' => now(),
    ]);

    $resPending = $this->getJson('/api/device/status?device_id=ACK-01')->assertSuccessful();
    expect($resPending->json('drrmo_responded'))->toBeFalse()
        ->and($resPending->json('has_pending'))->toBeTrue();

    // DRRMO acknowledges
    $incident->update(['status' => 'Acknowledged']);

    $resAck = $this->getJson('/api/device/status?device_id=ACK-01')->assertSuccessful();
    expect($resAck->json('drrmo_responded'))->toBeTrue();
});

test('simulator can register a new location device via json', function () {
    $payload = [
        'device_code' => 'SCI-001',
        'building' => 'Science Complex',
        'floor' => '2nd Floor',
        'room' => 'Biochemistry Lab',
        'latitude' => 7.712345,
        'longitude' => 123.294567,
    ];

    $response = $this->postJson('/simulator/devices', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'status' => 'success',
            'device' => [
                'device_code' => 'SCI-001',
                'building' => 'Science Complex',
            ],
        ]);

    $this->assertDatabaseHas('devices', [
        'device_code' => 'SCI-001',
        'building' => 'Science Complex',
        'status' => 'active',
    ]);
});

test('simulator trigger-all triggers alerts across every registered location', function () {
    Event::fake([EmergencyReported::class]);

    $dev1 = seedSimulatorDevice('DEV-01', 'Gymnasium');
    $dev2 = seedSimulatorDevice('DEV-02', 'Library');
    $dev3 = seedSimulatorDevice('DEV-03', 'Cafeteria');

    $response = $this->postJson('/simulator/trigger-all', [
        'emergency_category' => Incident::TYPE_CRITICAL,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'status' => 'success',
            'count' => 3,
        ]);

    expect(Incident::where('status', 'Pending')->count())->toBe(3);

    // Verify dual notifications created for all 3 devices
    expect(Notification::where('recipient', 'DRRMO')->count())->toBe(3)
        ->and(Notification::where('recipient', 'Clinic')->count())->toBe(3);

    Event::assertDispatched(EmergencyReported::class, 3);
});

test('simulator reset-alerts resolves all active simulated alerts', function () {
    Event::fake([EmergencyReported::class]);

    $dev1 = seedSimulatorDevice('DEV-01', 'Gymnasium');
    $dev2 = seedSimulatorDevice('DEV-02', 'Library');

    Incident::create([
        'device_id' => $dev1->id,
        'emergency_type' => Incident::TYPE_MEDICAL,
        'status' => 'Pending',
        'reported_at' => now(),
    ]);

    Incident::create([
        'device_id' => $dev2->id,
        'emergency_type' => Incident::TYPE_CRITICAL,
        'status' => 'Responding',
        'reported_at' => now(),
    ]);

    expect(Incident::active()->count())->toBe(2);

    $response = $this->postJson('/simulator/reset-alerts');

    $response->assertSuccessful()
        ->assertJson([
            'status' => 'success',
            'resolved_count' => 2,
        ]);

    expect(Incident::active()->count())->toBe(0)
        ->and(Incident::where('status', 'Resolved')->count())->toBe(2);
});

test('api emergency endpoint processes esp32 signal and broadcasts event', function () {
    Event::fake([EmergencyReported::class]);

    $device = seedSimulatorDevice('AUD-001', 'Auditorium');

    $response = $this->postJson('/api/emergency', [
        'device_id' => 'AUD-001',
        'emergency_category' => Incident::TYPE_CRITICAL,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'status' => 'success',
            'message' => 'Emergency alert received and processed.',
        ]);

    $incidentId = $response->json('incident_id');
    $incident = Incident::find($incidentId);

    expect($incident)->not->toBeNull()
        ->and($incident->device_id)->toBe($device->id)
        ->and($incident->status)->toBe('Pending');

    Event::assertDispatched(EmergencyReported::class, function ($event) use ($incident) {
        return $event->incident->id === $incident->id;
    });
});

test('artisan device:simulate command triggers alert for a specific location', function () {
    Event::fake([EmergencyReported::class]);

    $device = seedSimulatorDevice('LAB-001', 'Computer Lab');

    $this->artisan('device:simulate', [
        '--device' => 'LAB-001',
        '--category' => Incident::TYPE_MEDICAL,
    ])->assertSuccessful();

    $incident = Incident::where('device_id', $device->id)->latest()->first();

    expect($incident)->not->toBeNull()
        ->and($incident->status)->toBe('Pending')
        ->and($incident->emergency_type)->toBe(Incident::TYPE_MEDICAL);

    Event::assertDispatched(EmergencyReported::class);
});
