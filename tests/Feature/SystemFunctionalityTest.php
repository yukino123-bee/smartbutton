<?php

use App\Models\Device;
use App\Models\Incident;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

function systemDevice(array $attributes = []): Device
{
    return Device::create(array_merge([
        'device_code' => 'TEST-001',
        'building' => 'Library',
        'floor' => 'Ground Floor',
        'room' => 'Entrance',
        'latitude' => 7.708601,
        'longitude' => 123.292456,
        'status' => 'active',
        'last_seen' => now(),
    ], $attributes));
}

test('device API accepts only the three emergency categories defined by the system', function () {
    systemDevice();

    $this->postJson('/api/emergency', [
        'device_id' => 'TEST-001',
        'emergency_category' => 'Facility & Hazard',
    ])->assertUnprocessable();

    $this->postJson('/api/emergency', [
        'device_id' => 'TEST-001',
        'emergency_category' => Incident::TYPE_PUBLIC_SAFETY,
    ])->assertCreated();
});

test('device status polling records a real heartbeat and rejects unknown devices', function () {
    $device = systemDevice(['last_seen' => null]);

    $this->getJson('/api/device/status?device_id=UNKNOWN')->assertNotFound();

    $this->getJson('/api/device/status?device_id=TEST-001')
        ->assertSuccessful()
        ->assertJsonPath('device_code', 'TEST-001');

    expect($device->fresh()->last_seen)->not->toBeNull()
        ->and($device->fresh()->is_online)->toBeTrue();
});

test('inactive devices cannot submit emergency incidents', function () {
    systemDevice(['status' => 'maintenance']);

    $this->postJson('/api/emergency', [
        'device_id' => 'TEST-001',
        'emergency_category' => Incident::TYPE_CRITICAL,
    ])->assertConflict();

    expect(Incident::count())->toBe(0);
});

test('public safety alerts notify DRRMO but are excluded from Clinic data', function () {
    systemDevice();

    $response = $this->postJson('/api/emergency', [
        'device_id' => 'TEST-001',
        'emergency_category' => Incident::TYPE_PUBLIC_SAFETY,
    ])->assertCreated();

    $incident = Incident::findOrFail($response->json('incident_id'));

    expect(Notification::whereBelongsTo($incident)->where('recipient', 'DRRMO')->exists())->toBeTrue()
        ->and(Notification::whereBelongsTo($incident)->where('recipient', 'Clinic')->exists())->toBeFalse();

    $clinic = User::factory()->create(['role' => 'Clinic']);

    $this->actingAs($clinic)
        ->get('/clinic/alerts')
        ->assertSuccessful()
        ->assertDontSee(Incident::TYPE_PUBLIC_SAFETY);

    $this->actingAs($clinic)
        ->post("/clinic/incidents/{$incident->id}/acknowledge")
        ->assertNotFound();
});

test('medical alerts are visible to both DRRMO and Clinic', function () {
    systemDevice();

    $response = $this->postJson('/api/emergency', [
        'device_id' => 'TEST-001',
        'emergency_category' => Incident::TYPE_MEDICAL,
    ])->assertCreated();

    $incident = Incident::findOrFail($response->json('incident_id'));

    expect(Notification::whereBelongsTo($incident)->where('recipient', 'DRRMO')->exists())->toBeTrue()
        ->and(Notification::whereBelongsTo($incident)->where('recipient', 'Clinic')->exists())->toBeTrue();

    $clinic = User::factory()->create(['role' => 'Clinic']);

    $this->actingAs($clinic)
        ->get('/clinic/alerts')
        ->assertSuccessful()
        ->assertSee(Incident::TYPE_MEDICAL)
        ->assertSee('Library');
});

test('DRRMO workflow updates only the selected incident and records resolution time', function () {
    $user = User::factory()->create(['role' => 'DRRMO']);
    $device = systemDevice();
    $first = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_CRITICAL,
        'status' => 'Pending',
    ]);
    $second = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_MEDICAL,
        'status' => 'Pending',
    ]);

    $this->actingAs($user)->post("/ndrrmo/incidents/{$first->id}/acknowledge")->assertRedirect();
    expect($first->fresh()->status)->toBe('Acknowledged')
        ->and($second->fresh()->status)->toBe('Pending');

    $this->actingAs($user)->post("/ndrrmo/incidents/{$first->id}/dispatch")->assertRedirect();
    expect($first->fresh()->status)->toBe('Responding');

    $this->actingAs($user)->post("/ndrrmo/incidents/{$first->id}/resolve")->assertRedirect();
    expect($first->fresh()->status)->toBe('Resolved')
        ->and($first->fresh()->resolved_at)->not->toBeNull();
});

test('role middleware keeps Clinic and DRRMO pages separated', function () {
    $clinic = User::factory()->create(['role' => 'Clinic']);
    $drrmo = User::factory()->create(['role' => 'DRRMO']);

    $this->actingAs($clinic)->get('/ndrrmo')->assertForbidden();
    $this->actingAs($drrmo)->get('/clinic')->assertForbidden();
});

test('all DRRMO navigation pages render with database-backed empty states', function (string $path) {
    $drrmo = User::factory()->create(['role' => 'DRRMO']);

    $this->actingAs($drrmo)->get($path)->assertSuccessful();
})->with([
    '/ndrrmo',
    '/ndrrmo/alerts',
    '/ndrrmo/logs',
    '/ndrrmo/map',
    '/ndrrmo/devices',
    '/ndrrmo/reports',
]);

test('inactive users cannot authenticate', function () {
    User::factory()->create([
        'username' => 'inactive-admin',
        'password' => bcrypt('password123'),
        'role' => 'DRRMO',
        'status' => 'inactive',
    ]);

    $this->post('/login', [
        'username' => 'inactive-admin',
        'password' => 'password123',
    ])->assertSessionHasErrors('username');

    $this->assertGuest();
});

test('public self-registration is disabled', function () {
    $this->get('/register')->assertNotFound();
    $this->post('/register', [])->assertNotFound();
});
