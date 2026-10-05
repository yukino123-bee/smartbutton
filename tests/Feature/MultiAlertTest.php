<?php

use App\Models\Device;
use App\Models\Incident;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

function createDevice(string $code, string $building): Device
{
    return Device::create([
        'device_code' => $code,
        'building' => $building,
        'floor' => '1st Floor',
        'room' => 'Main Room',
        'latitude' => 7.708601,
        'longitude' => 123.292456,
        'status' => 'active',
        'last_seen' => now(),
    ]);
}

test('ndrrmo stats-json returns collection of pending incidents when multiple unhandled alerts exist', function () {
    $device1 = createDevice('DEV-001', 'Gymnasium');
    $device2 = createDevice('DEV-002', 'Library');

    Incident::create([
        'device_id' => $device1->id,
        'emergency_type' => Incident::TYPE_CRITICAL,
        'status' => 'Pending',
        'reported_at' => now()->subMinutes(2),
    ]);

    Incident::create([
        'device_id' => $device2->id,
        'emergency_type' => Incident::TYPE_PUBLIC_SAFETY,
        'status' => 'Pending',
        'reported_at' => now()->subMinute(),
    ]);

    $ndrrmoUser = User::factory()->create(['role' => 'DRRMO']);

    $response = $this->actingAs($ndrrmoUser)
        ->getJson('/ndrrmo/stats-json')
        ->assertSuccessful();

    $response->assertJsonCount(2, 'pending_incidents');
    expect($response->json('pending_incidents.0.device.building'))->toBe('Library')
        ->and($response->json('pending_incidents.1.device.building'))->toBe('Gymnasium')
        ->and($response->json('active_alerts'))->toBe(2);
});

test('clinic stats-json returns only clinic-relevant pending incidents when multiple unhandled alerts exist', function () {
    $device1 = createDevice('DEV-001', 'Gymnasium');
    $device2 = createDevice('DEV-002', 'Science Complex');
    $device3 = createDevice('DEV-003', 'Administration');

    // 2 clinic-relevant incidents
    Incident::create([
        'device_id' => $device1->id,
        'emergency_type' => Incident::TYPE_CRITICAL,
        'status' => 'Pending',
        'reported_at' => now()->subMinutes(3),
    ]);

    Incident::create([
        'device_id' => $device2->id,
        'emergency_type' => Incident::TYPE_MEDICAL,
        'status' => 'Pending',
        'reported_at' => now()->subMinute(),
    ]);

    // 1 public safety incident (NDRRMO only)
    Incident::create([
        'device_id' => $device3->id,
        'emergency_type' => Incident::TYPE_PUBLIC_SAFETY,
        'status' => 'Pending',
        'reported_at' => now(),
    ]);

    $clinicUser = User::factory()->create(['role' => 'Clinic']);

    $response = $this->actingAs($clinicUser)
        ->getJson('/clinic/stats-json')
        ->assertSuccessful();

    $response->assertJsonCount(2, 'pending_incidents');
    expect($response->json('active_alerts'))->toBe(2);
});

test('ndrrmo can acknowledge all unhandled alerts at once', function () {
    $device1 = createDevice('DEV-001', 'Gymnasium');
    $device2 = createDevice('DEV-002', 'Science Complex');

    $inc1 = Incident::create([
        'device_id' => $device1->id,
        'emergency_type' => Incident::TYPE_CRITICAL,
        'status' => 'Pending',
        'reported_at' => now()->subMinutes(3),
    ]);

    $inc2 = Incident::create([
        'device_id' => $device2->id,
        'emergency_type' => Incident::TYPE_PUBLIC_SAFETY,
        'status' => 'Pending',
        'reported_at' => now()->subMinute(),
    ]);

    $ndrrmoUser = User::factory()->create(['role' => 'DRRMO']);

    $this->actingAs($ndrrmoUser)
        ->postJson('/ndrrmo/alerts/acknowledge-all')
        ->assertSuccessful()
        ->assertJson([
            'status' => 'success',
            'count' => 2,
        ]);

    expect($inc1->fresh()->status)->toBe('Acknowledged')
        ->and($inc2->fresh()->status)->toBe('Acknowledged')
        ->and(Notification::where('recipient', 'DRRMO')->where('status', 'Acknowledged')->count())->toBe(2);
});

test('clinic can acknowledge all unhandled medical alerts at once', function () {
    $device1 = createDevice('DEV-001', 'Gymnasium');
    $device2 = createDevice('DEV-002', 'Science Complex');

    $inc1 = Incident::create([
        'device_id' => $device1->id,
        'emergency_type' => Incident::TYPE_CRITICAL,
        'status' => 'Pending',
        'reported_at' => now()->subMinutes(3),
    ]);

    $inc2 = Incident::create([
        'device_id' => $device2->id,
        'emergency_type' => Incident::TYPE_MEDICAL,
        'status' => 'Pending',
        'reported_at' => now()->subMinute(),
    ]);

    $clinicUser = User::factory()->create(['role' => 'Clinic']);

    $this->actingAs($clinicUser)
        ->postJson('/clinic/alerts/acknowledge-all')
        ->assertSuccessful()
        ->assertJson([
            'status' => 'success',
            'count' => 2,
        ]);

    expect($inc1->fresh()->status)->toBe('Acknowledged')
        ->and($inc2->fresh()->status)->toBe('Acknowledged')
        ->and(Notification::where('recipient', 'Clinic')->where('status', 'Acknowledged')->count())->toBe(2);
});

test('clinic dashboard displays multiple emergency cards when multiple unhandled alerts exist', function () {
    $device1 = createDevice('DEV-001', 'Gymnasium');
    $device2 = createDevice('DEV-002', 'Science Complex');

    Incident::create([
        'device_id' => $device1->id,
        'emergency_type' => Incident::TYPE_CRITICAL,
        'status' => 'Pending',
        'reported_at' => now()->subMinutes(3),
    ]);

    Incident::create([
        'device_id' => $device2->id,
        'emergency_type' => Incident::TYPE_MEDICAL,
        'status' => 'Pending',
        'reported_at' => now()->subMinute(),
    ]);

    $clinicUser = User::factory()->create(['role' => 'Clinic']);

    $response = $this->actingAs($clinicUser)
        ->get('/clinic')
        ->assertSuccessful();

    $response->assertSee('MULTIPLE ACTIVE MEDICAL EMERGENCIES (2 UNHANDLED ALERTS)')
        ->assertSee('Acknowledge All (2 Alerts)')
        ->assertSee('Alert #1: Medical Emergency')
        ->assertSee('Alert #2: Critical Emergency')
        ->assertSee('Gymnasium')
        ->assertSee('Science Complex');
});

test('alerts index pages render acknowledge all buttons when multiple alerts are pending', function () {
    $device1 = createDevice('DEV-001', 'Gymnasium');
    $device2 = createDevice('DEV-002', 'Science Complex');

    Incident::create([
        'device_id' => $device1->id,
        'emergency_type' => Incident::TYPE_CRITICAL,
        'status' => 'Pending',
        'reported_at' => now()->subMinutes(3),
    ]);

    Incident::create([
        'device_id' => $device2->id,
        'emergency_type' => Incident::TYPE_MEDICAL,
        'status' => 'Pending',
        'reported_at' => now()->subMinute(),
    ]);

    $ndrrmoUser = User::factory()->create(['role' => 'DRRMO']);
    $clinicUser = User::factory()->create(['role' => 'Clinic']);

    $this->actingAs($ndrrmoUser)
        ->get('/ndrrmo/alerts')
        ->assertSuccessful()
        ->assertSee('Acknowledge All (2)');

    $this->actingAs($clinicUser)
        ->get('/clinic/alerts')
        ->assertSuccessful()
        ->assertSee('Acknowledge All (2)');
});
