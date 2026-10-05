<?php

use App\Events\EmergencyReported;
use App\Models\Device;
use App\Models\Incident;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(LazilyRefreshDatabase::class);

function makeDevice(string $code = 'DEV-STEP-01', string $building = 'Engineering Hall'): Device
{
    return Device::create([
        'device_code' => $code,
        'building' => $building,
        'floor' => '2nd Floor',
        'room' => 'Lab 204',
        'latitude' => 7.708601,
        'longitude' => 123.292456,
        'status' => 'active',
        'last_seen' => now(),
    ]);
}

test('ndrrmo dashboard displays active emergency command console with 5-step stepper and SOP actions', function () {
    $device = makeDevice();
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_CRITICAL,
        'status' => 'Pending',
        'reported_at' => now(),
    ]);

    $ndrrmoUser = User::factory()->create(['role' => 'DRRMO']);

    $response = $this->actingAs($ndrrmoUser)
        ->get('/ndrrmo')
        ->assertSuccessful();

    $response->assertSee('ACTIVE EMERGENCY COMMAND CONSOLE')
        ->assertSee('5-STEP RESPONSE LIFECYCLE')
        ->assertSee('1. Triggered')
        ->assertSee('2. Acknowledge')
        ->assertSee('3. Dispatch')
        ->assertSee('4. Inter-Agency')
        ->assertSee('5. Resolution')
        ->assertSee('Acknowledge Alert')
        ->assertSee('Dispatch Responders')
        ->assertSee('Notify Clinic')
        ->assertSee('Mark Incident Resolved')
        ->assertSee('Engineering Hall');
});

test('ndrrmo can dispatch responders transitioning status to responding and creating dual notifications', function () {
    Event::fake([EmergencyReported::class]);

    $device = makeDevice();
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_MEDICAL,
        'status' => 'Acknowledged',
        'reported_at' => now(),
    ]);

    $ndrrmoUser = User::factory()->create(['role' => 'DRRMO']);

    $response = $this->actingAs($ndrrmoUser)
        ->post("/ndrrmo/incidents/{$incident->id}/dispatch");

    $response->assertRedirect();

    $incident->refresh();
    expect($incident->status)->toBe('Responding');

    // Asserts both DRRMO and Clinic notifications were created for cross-agency sync
    expect(Notification::where('incident_id', $incident->id)->where('recipient', 'DRRMO')->exists())->toBeTrue()
        ->and(Notification::where('incident_id', $incident->id)->where('recipient', 'Clinic')->exists())->toBeTrue();

    Event::assertDispatched(EmergencyReported::class, function ($event) use ($incident) {
        return $event->incident->id === $incident->id;
    });
});

test('clinic can dispatch medical team transitioning status to responding and notifying drrmo', function () {
    Event::fake([EmergencyReported::class]);

    $device = makeDevice();
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_MEDICAL,
        'status' => 'Acknowledged',
        'reported_at' => now(),
    ]);

    $clinicUser = User::factory()->create(['role' => 'Clinic']);

    $response = $this->actingAs($clinicUser)
        ->post("/clinic/incidents/{$incident->id}/dispatch");

    $response->assertRedirect();

    $incident->refresh();
    expect($incident->status)->toBe('Responding');

    // Asserts Clinic and DRRMO notifications were created
    expect(Notification::where('incident_id', $incident->id)->where('recipient', 'Clinic')->exists())->toBeTrue()
        ->and(Notification::where('incident_id', $incident->id)->where('recipient', 'DRRMO')->exists())->toBeTrue();

    Event::assertDispatched(EmergencyReported::class, function ($event) use ($incident) {
        return $event->incident->id === $incident->id;
    });
});

test('clinic dashboard displays 5-step medical response stepper and inter-agency coordination status', function () {
    $device = makeDevice('DEV-CLINIC-01', 'Nursing Building');
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_MEDICAL,
        'status' => 'Pending',
        'reported_at' => now(),
    ]);

    $clinicUser = User::factory()->create(['role' => 'Clinic']);

    $response = $this->actingAs($clinicUser)
        ->get('/clinic')
        ->assertSuccessful();

    $response->assertSee('5-STEP MEDICAL RESPONSE LIFECYCLE')
        ->assertSee('1. Triggered')
        ->assertSee('2. Acknowledge')
        ->assertSee('3. Dispatch')
        ->assertSee('4. Triage')
        ->assertSee('5. Resolution')
        ->assertSee('Dispatch Medical Team')
        ->assertSee('Mark Treated')
        ->assertSee('Nursing Building');
});

test('full emergency lifecycle synchronizes between ndrrmo and clinic', function () {
    Event::fake([EmergencyReported::class]);

    $device = makeDevice('DEV-CROSS-01', 'Main Auditorium');
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_CRITICAL,
        'status' => 'Pending',
        'reported_at' => now(),
    ]);

    $ndrrmoUser = User::factory()->create(['role' => 'DRRMO']);
    $clinicUser = User::factory()->create(['role' => 'Clinic']);

    // Step 2: NDRRMO acknowledges
    $this->actingAs($ndrrmoUser)
        ->post("/ndrrmo/incidents/{$incident->id}/acknowledge")
        ->assertRedirect();

    $incident->refresh();
    expect($incident->status)->toBe('Acknowledged');

    // Step 3: Clinic dispatches medical team
    $this->actingAs($clinicUser)
        ->post("/clinic/incidents/{$incident->id}/dispatch")
        ->assertRedirect();

    $incident->refresh();
    expect($incident->status)->toBe('Responding');

    // Step 4: NDRRMO notifies clinic
    $this->actingAs($ndrrmoUser)
        ->post("/ndrrmo/incidents/{$incident->id}/notify-clinic")
        ->assertRedirect();

    expect(Notification::where('incident_id', $incident->id)->where('recipient', 'Clinic')->count())->toBeGreaterThanOrEqual(1);

    // Step 5: Incident is resolved from NDRRMO
    $this->actingAs($ndrrmoUser)
        ->post("/ndrrmo/incidents/{$incident->id}/resolve", ['resolution_notes' => 'Patient stabilized and transported'])
        ->assertRedirect();

    $incident->refresh();
    expect($incident->status)->toBe('Resolved')
        ->and($incident->remarks)->toBe('Patient stabilized and transported');

    // Check that resolution notification reached both NDRRMO and Clinic
    expect(Notification::where('incident_id', $incident->id)->where('recipient', 'DRRMO')->where('status', 'Resolved')->exists())->toBeTrue()
        ->and(Notification::where('incident_id', $incident->id)->where('recipient', 'Clinic')->where('status', 'Resolved by DRRMO')->exists())->toBeTrue();
});
