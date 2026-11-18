<?php

use App\Models\Device;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function declineDevice(): Device
{
    return Device::create(['device_code' => 'DEC-001', 'building' => 'Library', 'floor' => '1', 'room' => 'R', 'status' => 'active']);
}

test('D33 records no clinic needed on the open medical alert and the badge shows only then', function () {
    $device = declineDevice();
    $drrmo = User::factory()->create(['role' => 'DRRMO']);
    $incident = Incident::create(['device_id' => $device->id, 'emergency_type' => Incident::TYPE_MEDICAL, 'status' => 'Pending', 'reported_at' => now(), 'need_clinic' => false]);

    $this->actingAs($drrmo)->get('/ndrrmo/alerts')->assertDontSee('NO CLINIC NEEDED');

    $this->postJson('/api/emergency', ['device_id' => 'DEC-001', 'emergency_category' => Incident::TYPE_MEDICAL, 'need_clinic' => false, 'clinic_declined' => true])
        ->assertOk()->assertJsonPath('incident_id', $incident->id);

    expect($incident->fresh()->clinic_declined_at)->not->toBeNull()
        ->and(Incident::count())->toBe(1);
    $this->actingAs($drrmo)->get('/ndrrmo/alerts')->assertSee('NO CLINIC NEEDED');
});

test('D33 without an open medical alert creates nothing', function () {
    declineDevice();

    $this->postJson('/api/emergency', ['device_id' => 'DEC-001', 'emergency_category' => Incident::TYPE_MEDICAL, 'clinic_declined' => true])->assertOk();

    expect(Incident::count())->toBe(0);
});
