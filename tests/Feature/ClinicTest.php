<?php

use App\Models\Device;
use App\Models\Incident;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('clinic user can access dashboard and subpages', function (string $route) {
    $user = User::factory()->create([
        'role' => 'Clinic',
        'status' => 'active',
    ]);

    $this->actingAs($user)
        ->get($route)
        ->assertSuccessful();
})->with([
    '/clinic',
    '/clinic/alerts',
    '/clinic/incoming',
    '/clinic/logs',
    '/clinic/patients',
    '/clinic/reports',
]);

test('clinic staff can acknowledge and resolve incidents', function () {
    $user = User::factory()->create(['role' => 'Clinic']);
    $device = Device::create([
        'device_code' => 'CLN-001',
        'building' => 'Health Center',
        'floor' => '1st Floor',
        'room' => 'Triage',
        'status' => 'active',
    ]);

    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => 'Medical Emergency',
        'status' => 'Pending',
    ]);

    $this->actingAs($user)
        ->post("/clinic/incidents/{$incident->id}/acknowledge")
        ->assertRedirect();

    expect($incident->fresh()->clinic_status)->toBe('Acknowledged');

    $this->actingAs($user)
        ->post("/clinic/incidents/{$incident->id}/resolve")
        ->assertRedirect();

    expect($incident->fresh()->clinic_status)->toBe('Resolved');
    expect($incident->fresh()->clinic_resolved_at)->not->toBeNull();

    // Clinic actions never change the DRRMO side of the incident.
    $incident->refresh();
    expect($incident->status)->toBe('Pending')
        ->and($incident->resolved_at)->toBeNull()
        ->and($incident->acknowledged_at)->toBeNull()
        ->and(Notification::where('incident_id', $incident->id)->where('recipient', 'DRRMO')->exists())->toBeFalse();
});

test('resolving an already resolved incident is idempotent', function () {
    $user = User::factory()->create(['role' => 'Clinic']);
    $device = Device::create([
        'device_code' => 'CLN-002',
        'building' => 'Health Center',
        'floor' => '1st Floor',
        'room' => 'Triage',
        'status' => 'active',
    ]);
    $resolvedAt = now()->subMinute();
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_MEDICAL,
        'status' => 'Resolved',
        'resolved_at' => $resolvedAt,
        'clinic_status' => 'Resolved',
        'clinic_resolved_at' => $resolvedAt,
    ]);
    $persistedResolvedAt = $incident->fresh()->clinic_resolved_at;

    $this->actingAs($user)
        ->from('/clinic/logs')
        ->post("/clinic/incidents/{$incident->id}/resolve")
        ->assertRedirect('/clinic/logs')
        ->assertSessionHas('info', 'Incident is already resolved.');

    expect($incident->fresh()->clinic_resolved_at->equalTo($persistedResolvedAt))->toBeTrue();
});

test('clinic logs do not offer the resolve action for resolved incidents', function () {
    $user = User::factory()->create(['role' => 'Clinic']);
    $device = Device::create([
        'device_code' => 'CLN-003',
        'building' => 'Health Center',
        'floor' => '1st Floor',
        'room' => 'Triage',
        'status' => 'active',
    ]);
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_MEDICAL,
        'status' => 'Resolved',
        'resolved_at' => now(),
        'clinic_status' => 'Resolved',
        'clinic_resolved_at' => now(),
    ]);

    $this->actingAs($user)
        ->get('/clinic/logs')
        ->assertSuccessful()
        ->assertSee('Resolved / Treated')
        ->assertSee('Closed')
        ->assertDontSee(route('clinic.incidents.resolve', $incident), false);
});

test('clinic user can export excel report', function () {
    $user = User::factory()->create(['role' => 'Clinic']);

    $this->actingAs($user)
        ->get('/clinic/reports/export-excel')
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/vnd.ms-excel');
});

test('clinic false alarm does not stop or change the drrmo alert', function () {
    $device = Device::create([
        'device_code' => 'CLN-004',
        'building' => 'Health Center',
        'floor' => '1st Floor',
        'room' => 'Triage',
        'status' => 'active',
    ]);
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_CRITICAL,
        'status' => 'Pending',
        'reported_at' => now(),
    ]);

    $this->actingAs(User::factory()->create(['role' => 'Clinic']))
        ->postJson("/clinic/incidents/{$incident->id}/resolve", [
            'resolution_type' => 'Drill',
            'false_alarm_reason' => 'Accidental Push',
        ])->assertOk()
        ->assertJsonPath('incident.status', 'Resolved')
        ->assertJsonPath('incident.resolution_type', 'Drill');

    $incident->refresh();
    expect($incident->clinic_status)->toBe('Resolved')
        ->and($incident->clinic_resolution_type)->toBe('Drill')
        ->and($incident->status)->toBe('Pending')
        ->and($incident->resolution_type)->toBeNull()
        ->and($incident->false_alarm_reason)->toBeNull()
        ->and($incident->resolved_at)->toBeNull()
        ->and(Incident::active()->whereKey($incident->id)->exists())->toBeTrue()
        ->and(Incident::falseAlarms()->whereKey($incident->id)->exists())->toBeFalse();

    // The device keeps alarming until DRRMO responds.
    $this->getJson('/api/device/status?device_id=CLN-004')
        ->assertOk()
        ->assertJsonPath('has_pending', true)
        ->assertJsonPath('drrmo_responded', false);

    // DRRMO still sees and can act on the alert.
    $this->actingAs(User::factory()->create(['role' => 'DRRMO']))
        ->postJson("/ndrrmo/incidents/{$incident->id}/acknowledge")
        ->assertOk();
    expect($incident->fresh()->status)->toBe('Acknowledged');

    // The Clinic's own list no longer shows it as active.
    expect(Incident::clinicVisible()->clinicActive()->whereKey($incident->id)->exists())->toBeFalse();
});

test('deleting an alert on the clinic side only hides it from the clinic', function () {
    $device = Device::create([
        'device_code' => 'CLN-005',
        'building' => 'Health Center',
        'floor' => '1st Floor',
        'room' => 'Triage',
        'status' => 'active',
    ]);
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_CRITICAL,
        'status' => 'Pending',
        'reported_at' => now(),
    ]);
    Notification::create([
        'incident_id' => $incident->id,
        'recipient' => 'DRRMO',
        'channel' => 'Dashboard',
        'status' => 'Delivered',
        'sent_at' => now(),
    ]);

    $this->actingAs(User::factory()->create(['role' => 'Clinic']))
        ->delete("/clinic/alerts/{$incident->id}")
        ->assertRedirect();

    expect(Incident::find($incident->id))->not->toBeNull()
        ->and(Incident::clinicVisible()->whereKey($incident->id)->exists())->toBeFalse()
        ->and(Notification::where('incident_id', $incident->id)->where('recipient', 'DRRMO')->exists())->toBeTrue()
        ->and(Incident::active()->whereKey($incident->id)->exists())->toBeTrue();
});

test('drrmo resolving a medical emergency also resolves it on the clinic side', function () {
    $device = Device::create([
        'device_code' => 'CLN-006',
        'building' => 'Health Center',
        'floor' => '1st Floor',
        'room' => 'Triage',
        'status' => 'active',
    ]);
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_MEDICAL,
        'need_clinic' => true,
        'status' => 'Acknowledged',
        'clinic_status' => 'Acknowledged',
        'reported_at' => now(),
    ]);

    $this->actingAs(User::factory()->create(['role' => 'DRRMO']))
        ->post("/ndrrmo/incidents/{$incident->id}/resolve", ['resolution_type' => 'Resolved'])
        ->assertRedirect();

    $incident->refresh();
    expect($incident->status)->toBe('Resolved')
        ->and($incident->clinic_status)->toBe('Resolved')
        ->and($incident->clinic_resolved_at)->not->toBeNull()
        ->and($incident->clinic_resolution_type)->toBe('Resolved')
        ->and(Incident::clinicVisible()->clinicActive()->whereKey($incident->id)->exists())->toBeFalse()
        ->and(Incident::clinicVisible()->clinicResolved()->whereKey($incident->id)->exists())->toBeTrue();

    $this->actingAs(User::factory()->create(['role' => 'Clinic']))
        ->get('/clinic/logs')
        ->assertSuccessful()
        ->assertDontSee(route('clinic.incidents.resolve', $incident), false);
});

test('drrmo resolving does not overwrite an incident the clinic already resolved', function () {
    $device = Device::create([
        'device_code' => 'CLN-007',
        'building' => 'Health Center',
        'floor' => '1st Floor',
        'room' => 'Triage',
        'status' => 'active',
    ]);
    $clinicResolvedAt = now()->subMinutes(5)->startOfSecond();
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_CRITICAL,
        'status' => 'Pending',
        'clinic_status' => 'Resolved',
        'clinic_resolved_at' => $clinicResolvedAt,
        'clinic_resolution_type' => 'False Alarm',
        'reported_at' => now(),
    ]);

    $this->actingAs(User::factory()->create(['role' => 'DRRMO']))
        ->post("/ndrrmo/incidents/{$incident->id}/resolve", ['resolution_type' => 'Resolved'])
        ->assertRedirect();

    $incident->refresh();
    expect($incident->status)->toBe('Resolved')
        ->and($incident->clinic_resolution_type)->toBe('False Alarm')
        ->and($incident->clinic_resolved_at->equalTo($clinicResolvedAt))->toBeTrue();
});

test('drrmo resolving a medical emergency with no clinic involvement leaves the clinic copy alone', function () {
    $device = Device::create([
        'device_code' => 'CLN-008',
        'building' => 'Health Center',
        'floor' => '1st Floor',
        'room' => 'Triage',
        'status' => 'active',
    ]);
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_MEDICAL,
        'need_clinic' => false,
        'status' => 'Pending',
        'reported_at' => now(),
    ]);

    $this->actingAs(User::factory()->create(['role' => 'DRRMO']))
        ->post("/ndrrmo/incidents/{$incident->id}/resolve")
        ->assertRedirect();

    expect($incident->fresh()->clinic_status)->toBe('Pending')
        ->and($incident->fresh()->clinic_resolved_at)->toBeNull();
});

test('clinic cannot classify an incident as a false alarm', function () {
    $device = Device::create(['device_code' => 'CLN-FA', 'building' => 'Clinic', 'floor' => '1', 'room' => 'R', 'status' => 'active']);
    $incident = Incident::create(['device_id' => $device->id, 'emergency_type' => Incident::TYPE_CRITICAL, 'status' => 'Pending', 'reported_at' => now()]);

    $response = $this->actingAs(User::factory()->create(['role' => 'Clinic']))
        ->postJson("/clinic/incidents/{$incident->id}/resolve", ['resolution_type' => 'False Alarm']);

    $response->assertSessionHasErrors('resolution_type');
    expect($incident->fresh()->clinic_status)->not->toBe('Resolved');
});

test('clinic dispatch notifies DRRMO without changing the DRRMO incident', function () {
    $device = Device::create(['device_code' => 'CLN-DSP', 'building' => 'Clinic', 'floor' => '1', 'room' => 'R', 'status' => 'active']);
    $incident = Incident::create(['device_id' => $device->id, 'emergency_type' => Incident::TYPE_CRITICAL, 'status' => 'Pending', 'reported_at' => now()]);

    $this->actingAs(User::factory()->create(['role' => 'Clinic']))
        ->postJson("/clinic/incidents/{$incident->id}/dispatch")->assertOk();

    expect(Notification::where('incident_id', $incident->id)->where('recipient', 'DRRMO')->where('status', 'Clinic Medical Team Dispatched')->exists())->toBeTrue()
        ->and($incident->refresh()->status)->toBe('Pending')
        ->and($incident->clinic_status)->toBe('Responding');

    // Once dispatched it no longer pops up as an unhandled alert on the Clinic HUD.
    $this->getJson('/clinic/stats-json')->assertJsonCount(0, 'pending_incidents');
});
