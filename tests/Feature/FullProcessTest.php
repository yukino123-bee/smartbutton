<?php

use App\Models\Device;
use App\Models\Incident;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function press(string $device, string $type, ?bool $needClinic = null)
{
    $payload = ['device_id' => $device, 'emergency_category' => $type];
    if ($needClinic !== null) {
        $payload['need_clinic'] = $needClinic;
    }

    return test()->postJson('/api/emergency', $payload);
}

function drrmo(): User
{
    return User::factory()->create(['role' => 'DRRMO']);
}

function clinicUser(): User
{
    return User::factory()->create(['role' => 'Clinic']);
}

function deviceState(string $code): array
{
    return test()->getJson('/api/device/status?device_id='.$code)->json();
}

beforeEach(function () {
    foreach (['LIB-001', 'GYM-001'] as $code) {
        Device::create(['device_code' => $code, 'building' => $code, 'floor' => '1', 'room' => 'R', 'status' => 'active', 'last_seen' => now()]);
    }
});

test('public safety: DRRMO only, full DRRMO lifecycle, device silenced on acknowledge', function () {
    $id = press('LIB-001', Incident::TYPE_PUBLIC_SAFETY, false)->assertCreated()->json('incident_id');

    // Clinic never sees it
    $this->actingAs(clinicUser())->getJson('/clinic/stats-json')->assertJsonCount(0, 'pending_incidents');
    $this->actingAs(clinicUser())->postJson("/clinic/incidents/{$id}/dispatch")->assertNotFound();
    expect(Notification::where('incident_id', $id)->where('recipient', 'Clinic')->exists())->toBeFalse();

    // DRRMO sees it and the device keeps alarming until acknowledged
    $this->actingAs(drrmo())->getJson('/ndrrmo/stats-json')->assertOk();
    expect(deviceState('LIB-001'))->toMatchArray(['has_pending' => true, 'drrmo_responded' => false]);

    $this->actingAs(drrmo())->postJson("/ndrrmo/incidents/{$id}/acknowledge")->assertOk();
    expect(deviceState('LIB-001')['drrmo_responded'])->toBeTrue();

    $this->actingAs(drrmo())->postJson("/ndrrmo/incidents/{$id}/dispatch", ['responder_name' => 'Campus Security Patrol Alpha', 'eta_minutes' => 2])->assertOk();
    $this->actingAs(drrmo())->postJson("/ndrrmo/incidents/{$id}/on-scene")->assertOk();
    $this->actingAs(drrmo())->postJson("/ndrrmo/incidents/{$id}/resolve", ['resolution_type' => 'Resolved'])->assertOk();

    $incident = Incident::find($id);
    expect($incident->status)->toBe('Resolved')
        ->and($incident->acknowledged_at)->not->toBeNull()
        ->and($incident->dispatched_at)->not->toBeNull()
        ->and($incident->arrived_at)->not->toBeNull()
        ->and($incident->resolved_at)->not->toBeNull();
});

test('wrong button: the first alert becomes a false alarm and the second is the valid one', function () {
    $first = press('LIB-001', Incident::TYPE_PUBLIC_SAFETY, false)->json('incident_id');
    $second = press('LIB-001', Incident::TYPE_MEDICAL, false)->assertCreated()->json('incident_id');

    expect(Incident::find($first))->status->toBe('Resolved')->resolution_type->toBe('False Alarm')
        ->and(Incident::find($second))->status->toBe('Pending')
        ->and(Incident::active()->count())->toBe(1);
    // Buzzer follows the one valid alert
    expect(deviceState('LIB-001'))->toMatchArray(['has_pending' => true, 'incident_id' => $second]);
});

test('critical: both are alerted, Clinic dispatch tells DRRMO, DRRMO resolve closes the Clinic copy', function () {
    $id = press('GYM-001', Incident::TYPE_CRITICAL, true)->assertCreated()->json('incident_id');

    expect(Notification::where('incident_id', $id)->pluck('recipient')->unique()->sort()->values()->all())->toBe(['Clinic', 'DRRMO']);
    $this->actingAs(clinicUser())->getJson('/clinic/stats-json')->assertJsonCount(1, 'pending_incidents');

    // Clinic: Dispatch only (no acknowledge needed), popup stops, DRRMO is told
    $this->actingAs(clinicUser())->postJson("/clinic/incidents/{$id}/dispatch", ['responder_name' => 'Clinic Nurse Team'])->assertOk();
    $this->actingAs(clinicUser())->getJson('/clinic/stats-json')->assertJsonCount(0, 'pending_incidents');
    expect(Notification::where('incident_id', $id)->where('recipient', 'DRRMO')->where('status', 'Clinic Medical Team Dispatched')->exists())->toBeTrue()
        ->and(Incident::find($id)->status)->toBe('Pending'); // DRRMO's own state unchanged
    $this->actingAs(drrmo())->get('/ndrrmo')->assertSee('Medical Dispatched');
    $this->actingAs(drrmo())->get('/ndrrmo/alerts')->assertSee('CLINIC: MEDICAL TEAM DISPATCHED');

    // Clinic cannot call it a false alarm; it can resolve as treated
    $this->actingAs(clinicUser())->postJson("/clinic/incidents/{$id}/resolve", ['resolution_type' => 'False Alarm'])->assertStatus(422);

    // DRRMO closes it and the Clinic's copy closes with it
    $this->actingAs(drrmo())->postJson("/ndrrmo/incidents/{$id}/acknowledge")->assertOk();
    $this->actingAs(drrmo())->postJson("/ndrrmo/incidents/{$id}/resolve", ['resolution_type' => 'Resolved'])->assertOk();
    expect(Incident::find($id)->clinic_status)->toBe('Resolved');
});

test('medical + clinic button: Clinic is alerted only after DRRMO acknowledges', function () {
    $id = press('LIB-001', Incident::TYPE_MEDICAL, false)->json('incident_id');
    $this->actingAs(clinicUser())->getJson('/clinic/stats-json')->assertJsonCount(0, 'pending_incidents');

    // Clinic button before acknowledgment: no new incident, request is held
    press('LIB-001', Incident::TYPE_MEDICAL, true)->assertOk()->assertJsonPath('clinic_request_pending', true);
    expect(Incident::count())->toBe(1);
    $this->actingAs(clinicUser())->getJson('/clinic/stats-json')->assertJsonCount(0, 'pending_incidents');

    $this->actingAs(drrmo())->postJson("/ndrrmo/incidents/{$id}/acknowledge")->assertOk();
    $this->actingAs(clinicUser())->getJson('/clinic/stats-json')->assertJsonCount(1, 'pending_incidents');
});

test('medical without clinic never reaches the Clinic, and a clinic press with no Medical alert is ignored', function () {
    $id = press('GYM-001', Incident::TYPE_MEDICAL, false)->json('incident_id');
    $this->actingAs(drrmo())->postJson("/ndrrmo/incidents/{$id}/acknowledge")->assertOk();
    $this->actingAs(drrmo())->postJson("/ndrrmo/incidents/{$id}/dispatch")->assertOk();
    expect(Notification::where('incident_id', $id)->where('recipient', 'Clinic')->exists())->toBeFalse();

    // Different device with no open Medical alert
    press('LIB-001', Incident::TYPE_MEDICAL, true)->assertOk()->assertJsonPath('ignored', true);
    expect(Incident::count())->toBe(1);
});

test('repeat press within 30 s is a duplicate; acknowledged alerts are not replaced', function () {
    $id = press('LIB-001', Incident::TYPE_CRITICAL, true)->json('incident_id');
    press('LIB-001', Incident::TYPE_CRITICAL, true)->assertOk()->assertJsonPath('duplicate', true);
    expect(Incident::count())->toBe(1);

    $this->actingAs(drrmo())->postJson("/ndrrmo/incidents/{$id}/acknowledge")->assertOk();
    press('LIB-001', Incident::TYPE_PUBLIC_SAFETY, false)->assertCreated();
    expect(Incident::find($id)->status)->toBe('Acknowledged')->and(Incident::count())->toBe(2);
});

test('device API honours the shared token and the old open incident endpoints are gone', function () {
    config(['services.device.token' => 'secret-token']);

    $this->postJson('/api/emergency', ['device_id' => 'LIB-001', 'emergency_category' => Incident::TYPE_CRITICAL])->assertUnauthorized();
    $this->getJson('/api/device/status?device_id=LIB-001')->assertUnauthorized();
    $this->withHeader('X-Device-Token', 'wrong')->getJson('/api/device/status?device_id=LIB-001')->assertUnauthorized();
    $this->withHeader('X-Device-Token', 'secret-token')->postJson('/api/emergency', ['device_id' => 'LIB-001', 'emergency_category' => Incident::TYPE_CRITICAL])->assertCreated();

    $id = Incident::first()->id;
    foreach (['acknowledge', 'dispatch', 'resolve'] as $action) {
        $this->withHeader('X-Device-Token', 'secret-token')->postJson("/api/incidents/{$id}/{$action}")->assertNotFound();
    }
});

test('simulation page runs through the secured API, labels incidents and can be switched off', function () {
    config(['services.device.token' => 'secret-token']);

    $this->actingAs(drrmo())->postJson('/ndrrmo/simulation/emergency', ['device_id' => 'LIB-001', 'emergency_category' => Incident::TYPE_PUBLIC_SAFETY, 'need_clinic' => false])->assertCreated();
    expect(Incident::first()->remarks)->toStartWith('[Simulation]');
    $this->actingAs(drrmo())->getJson('/ndrrmo/simulation/status?device_id=LIB-001')->assertOk()->assertJsonPath('has_pending', true);

    config(['app.simulation_enabled' => false]);
    $this->actingAs(drrmo())->get('/ndrrmo/simulation')->assertNotFound();
    $this->actingAs(drrmo())->postJson('/ndrrmo/simulation/emergency', ['device_id' => 'LIB-001', 'emergency_category' => Incident::TYPE_MEDICAL])->assertNotFound();
    $this->actingAs(drrmo())->get('/ndrrmo')->assertDontSee('nav-ndrrmo-simulation', false);
});

test('role separation and unknown or inactive devices', function () {
    $this->actingAs(clinicUser())->get('/ndrrmo')->assertForbidden();
    $this->actingAs(clinicUser())->get('/ndrrmo/simulation')->assertForbidden();
    $this->actingAs(drrmo())->get('/clinic')->assertForbidden();
    $this->actingAs(drrmo())->get('/ndrrmo/simulation')->assertOk();

    press('NOPE-999', Incident::TYPE_CRITICAL)->assertNotFound();
    Device::where('device_code', 'GYM-001')->update(['status' => 'inactive']);
    press('GYM-001', Incident::TYPE_CRITICAL)->assertStatus(409);
});

test('clinic dispatch from the alerts page records the responder name and ETA entered', function () {
    $id = press('LIB-001', Incident::TYPE_CRITICAL, true)->json('incident_id');

    $this->actingAs(clinicUser())->get('/clinic/alerts')->assertSee('name="responder_name"', false)->assertSee('name="eta_minutes"', false);
    $this->actingAs(clinicUser())->post("/clinic/incidents/{$id}/dispatch", ['responder_name' => 'Nurse Reyes', 'eta_minutes' => 4])->assertRedirect();

    expect(Incident::find($id))->clinic_responder_name->toBe('Nurse Reyes')->clinic_eta_minutes->toBe(4)->clinic_status->toBe('Responding');
});

test('dashboards render the multi-alert grid containers for simultaneous alerts', function () {
    press('LIB-001', Incident::TYPE_CRITICAL, true)->assertCreated();
    press('GYM-001', Incident::TYPE_CRITICAL, true)->assertCreated();

    $this->actingAs(drrmo())->get('/ndrrmo')->assertSee('id="hud-alert-grid"', false)->assertSee('hudCardAction', false);
    $this->actingAs(clinicUser())->get('/clinic')->assertSee('id="clinic-alert-grid"', false)->assertSee('clinicCardAction', false);
    $this->actingAs(drrmo())->getJson('/ndrrmo/stats-json')->assertJsonCount(2, 'pending_incidents');
    $this->actingAs(clinicUser())->getJson('/clinic/stats-json')->assertJsonCount(2, 'pending_incidents');
});

test('campus map legend lists a colour for every alert type and colours incident cards to match', function () {
    press('LIB-001', Incident::TYPE_MEDICAL, false)->assertCreated();
    press('GYM-001', Incident::TYPE_PUBLIC_SAFETY, false)->assertCreated();

    $page = $this->actingAs(drrmo())->get('/ndrrmo/map')->assertOk();
    $page->assertSee('Critical Emergency')->assertSee('Medical Emergency')->assertSee('Public Safety');
    $page->assertSee('bg-orange-50/60', false)->assertSee('bg-amber-50/60', false);
});

test('alert Map buttons focus the clicked alert on the campus map', function () {
    $a = press('LIB-001', Incident::TYPE_MEDICAL, false)->json('incident_id');
    press('GYM-001', Incident::TYPE_PUBLIC_SAFETY, false);

    $page = $this->actingAs(drrmo())->get("/ndrrmo/map?incident_id={$a}")->assertOk();
    $page->assertSee('window.ndrrmoCampusMap = map', false)
        ->assertSee('deviceMarkers[device.id] = marker', false)
        ->assertSee('Active alerts on this device')
        ->assertDontSee('marker.openPopup();'."\n".'            map.setView', false);
});
