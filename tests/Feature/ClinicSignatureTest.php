<?php

use App\Models\Device;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('DRRMO stats change when the Clinic dispatches an active incident, so the dashboard reloads', function () {
    Device::create(['device_code' => 'LIB-001', 'building' => 'Library', 'floor' => '1', 'room' => 'R', 'status' => 'active', 'last_seen' => now()]);
    $drrmo = User::factory()->create(['role' => 'DRRMO']);
    $clinic = User::factory()->create(['role' => 'Clinic']);

    $id = $this->postJson('/api/emergency', ['device_id' => 'LIB-001', 'emergency_category' => Incident::TYPE_CRITICAL, 'need_clinic' => true])
        ->assertCreated()->json('incident_id');

    $before = $this->actingAs($drrmo)->getJson('/ndrrmo/stats-json')->json('clinic_signature');

    $this->actingAs($clinic)->postJson("/clinic/incidents/{$id}/dispatch", ['responder_name' => 'Clinic Nurse Team'])->assertOk();

    $after = $this->actingAs($drrmo)->getJson('/ndrrmo/stats-json')->json('clinic_signature');
    expect($after)->not->toBe($before);

    // Nothing new from the Clinic: the signature stays the same, so no reload loop
    expect($this->actingAs($drrmo)->getJson('/ndrrmo/stats-json')->json('clinic_signature'))->toBe($after);
});
