<?php

use App\Events\EmergencyReported;
use App\Models\Device;
use App\Models\Incident;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

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

test('clinic dispatching its medical team tells DRRMO without changing the DRRMO incident', function () {
    Event::fake([EmergencyReported::class]);

    $device = makeDevice();
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_MEDICAL,
        'status' => 'Acknowledged',
        'clinic_status' => 'Acknowledged',
        'reported_at' => now(),
    ]);

    $clinicUser = User::factory()->create(['role' => 'Clinic']);

    $response = $this->actingAs($clinicUser)
        ->post("/clinic/incidents/{$incident->id}/dispatch");

    $response->assertRedirect();

    $incident->refresh();
    expect($incident->clinic_status)->toBe('Responding')
        ->and($incident->clinic_dispatched_at)->not->toBeNull()
        ->and($incident->status)->toBe('Acknowledged')
        ->and($incident->dispatched_at)->toBeNull();

    // DRRMO is told the Clinic dispatched help, but its own incident state is untouched
    expect(Notification::where('incident_id', $incident->id)->where('recipient', 'Clinic')->exists())->toBeTrue()
        ->and(Notification::where('incident_id', $incident->id)->where('recipient', 'DRRMO')->where('status', 'Clinic Medical Team Dispatched')->count())->toBe(1);

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
    expect($incident->clinic_status)->toBe('Responding')
        ->and($incident->status)->toBe('Acknowledged');

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

test('api emergency with need_clinic true holds the clinic until DRRMO acknowledges', function () {
    Event::fake([EmergencyReported::class]);
    $device = makeDevice('DEV-MED-YES');

    // The Medical button comes first and alerts DRRMO only.
    $this->postJson('/api/emergency', [
        'device_id' => 'DEV-MED-YES',
        'emergency_category' => Incident::TYPE_MEDICAL,
        'need_clinic' => false,
    ])->assertCreated();

    $incident = Incident::latest('id')->first();

    // The clinic button then asks for the Clinic, which is held until DRRMO acknowledges.
    $this->postJson('/api/emergency', [
        'device_id' => 'DEV-MED-YES',
        'emergency_category' => Incident::TYPE_MEDICAL,
        'need_clinic' => true,
    ])->assertOk()
        ->assertJson(['status' => 'success', 'incident_id' => $incident->id, 'clinic_request_pending' => true]);

    $incident->refresh();
    expect(Incident::count())->toBe(1)
        ->and($incident->need_clinic)->toBeFalse()
        ->and($incident->clinic_requested_at)->not->toBeNull()
        ->and(Notification::where('incident_id', $incident->id)->where('recipient', 'Clinic')->exists())->toBeFalse()
        ->and(Notification::where('incident_id', $incident->id)->where('recipient', 'DRRMO')->exists())->toBeTrue();

    $this->actingAs(User::factory()->create(['role' => 'DRRMO']))
        ->post("/ndrrmo/incidents/{$incident->id}/acknowledge")
        ->assertRedirect();

    $incident->refresh();
    expect($incident->need_clinic)->toBeTrue()
        ->and(Notification::where('incident_id', $incident->id)->where('recipient', 'Clinic')->exists())->toBeTrue();
});

test('clinic button without a prior medical alert creates nothing on either side', function () {
    Event::fake([EmergencyReported::class]);
    makeDevice('DEV-CLINIC-ONLY');

    $this->postJson('/api/emergency', [
        'device_id' => 'DEV-CLINIC-ONLY',
        'emergency_category' => Incident::TYPE_MEDICAL,
        'need_clinic' => true,
    ])->assertOk()
        ->assertJson(['status' => 'ignored', 'ignored' => true]);

    expect(Incident::count())->toBe(0)
        ->and(Notification::count())->toBe(0);
    Event::assertNotDispatched(EmergencyReported::class);

    // Neither dashboard has anything to show.
    expect(Incident::active()->count())->toBe(0)
        ->and(Incident::clinicVisible()->clinicActive()->count())->toBe(0);
    $this->actingAs(User::factory()->create(['role' => 'DRRMO']))
        ->getJson('/ndrrmo/stats-json')->assertSuccessful()->assertJsonPath('active_alerts', 0);
    $this->actingAs(User::factory()->create(['role' => 'Clinic']))
        ->getJson('/clinic/stats-json')->assertSuccessful()->assertJsonPath('active_alerts', 0);

    // A resolved Medical alert no longer counts: the clinic button is ignored again.
    $incident = Incident::create([
        'device_id' => Device::where('device_code', 'DEV-CLINIC-ONLY')->value('id'),
        'emergency_type' => Incident::TYPE_MEDICAL,
        'need_clinic' => false,
        'status' => 'Resolved',
        'reported_at' => now(),
    ]);

    $this->postJson('/api/emergency', [
        'device_id' => 'DEV-CLINIC-ONLY',
        'emergency_category' => Incident::TYPE_MEDICAL,
        'need_clinic' => true,
    ])->assertOk()->assertJson(['ignored' => true]);

    expect(Incident::count())->toBe(1)
        ->and($incident->fresh()->need_clinic)->toBeFalse();
});

test('api emergency creates medical alert with need_clinic false and suppresses clinic notification', function () {
    Event::fake([EmergencyReported::class]);
    $device = makeDevice('DEV-MED-NO');

    $response = $this->postJson('/api/emergency', [
        'device_id' => 'DEV-MED-NO',
        'emergency_category' => Incident::TYPE_MEDICAL,
        'need_clinic' => false,
    ]);

    $response->assertCreated()
        ->assertJson(['status' => 'success', 'need_clinic' => false]);

    $incident = Incident::latest('id')->first();
    expect($incident->need_clinic)->toBeFalse()
        ->and(Notification::where('incident_id', $incident->id)->where('recipient', 'Clinic')->exists())->toBeFalse()
        ->and(Notification::where('incident_id', $incident->id)->where('recipient', 'DRRMO')->exists())->toBeTrue();

    $ndrrmoUser = User::factory()->create(['role' => 'DRRMO']);
    $this->actingAs($ndrrmoUser)
        ->get('/ndrrmo')
        ->assertSuccessful()
        ->assertSee('CLINIC AID: NOT NEEDED');
});

test('a different button right after an unacknowledged alert turns the first into a false alarm', function () {
    Event::fake([EmergencyReported::class]);
    $device = makeDevice('DEV-WRONG-01');

    $first = $this->postJson('/api/emergency', ['device_id' => 'DEV-WRONG-01', 'emergency_category' => Incident::TYPE_CRITICAL])
        ->assertCreated()->json('incident_id');
    $second = $this->postJson('/api/emergency', ['device_id' => 'DEV-WRONG-01', 'emergency_category' => Incident::TYPE_PUBLIC_SAFETY])
        ->assertCreated()->json('incident_id');

    $old = Incident::find($first);
    expect($old->status)->toBe('Resolved')
        ->and($old->resolution_type)->toBe(Incident::RESOLUTION_FALSE_ALARM)
        ->and($old->clinic_status)->toBe('Resolved')
        ->and(Incident::find($second)->status)->toBe('Pending');
    expect(Notification::where('incident_id', $first)->where('status', 'Resolved (False Alarm)')->count())->toBe(2);
});

test('an acknowledged alert is not replaced when another button is pressed', function () {
    Event::fake([EmergencyReported::class]);
    makeDevice('DEV-WRONG-02');

    $first = $this->postJson('/api/emergency', ['device_id' => 'DEV-WRONG-02', 'emergency_category' => Incident::TYPE_PUBLIC_SAFETY])
        ->json('incident_id');
    Incident::find($first)->update(['status' => 'Acknowledged']);
    $this->postJson('/api/emergency', ['device_id' => 'DEV-WRONG-02', 'emergency_category' => Incident::TYPE_MEDICAL])->assertCreated();

    expect(Incident::find($first)->status)->toBe('Acknowledged');
});

test('device:simulate presses buttons like the firmware and respects the cooldown', function () {
    Http::fake([
        '*/api/emergency' => Http::response(['message' => 'ok'], 201),
        '*/api/device/status' => Http::response(['has_pending' => true, 'drrmo_responded' => false, 'incident_id' => 1, 'incident_status' => 'Pending']),
    ]);

    $this->artisan('device:simulate', ['device' => 'LIB-001', '--press' => ['public', 'critical'], '--delay' => 0])
        ->expectsOutputToContain('[COOLDOWN]')
        ->assertSuccessful();

    Http::assertSent(fn ($r) => str_ends_with($r->url(), '/api/emergency')
        && $r['device_id'] === 'LIB-001'
        && $r['emergency_category'] === 'Public Safety Emergency'
        && $r['need_clinic'] === false);
    Http::assertSentCount(2); // one alert + final status check
});

test('drrmo sees the simulation page and nav link, clinic cannot open it', function () {
    makeDevice('DEV-SIM-01');

    $this->actingAs(User::factory()->create(['role' => 'DRRMO']))
        ->get('/ndrrmo/simulation')->assertSuccessful()->assertSee('Device Simulation')->assertSee('DEV-SIM-01');
    $this->get('/ndrrmo')->assertSee('nav-ndrrmo-simulation', false);

    $this->actingAs(User::factory()->create(['role' => 'Clinic']))->get('/ndrrmo/simulation')->assertForbidden();
});
