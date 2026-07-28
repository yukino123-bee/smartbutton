<?php

use App\Models\Device;
use App\Models\Incident;
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

    expect($incident->fresh()->status)->toBe('Acknowledged');

    $this->actingAs($user)
        ->post("/clinic/incidents/{$incident->id}/resolve")
        ->assertRedirect();

    expect($incident->fresh()->status)->toBe('Resolved');
    expect($incident->fresh()->resolved_at)->not->toBeNull();
});

test('clinic user can export excel report', function () {
    $user = User::factory()->create(['role' => 'Clinic']);

    $this->actingAs($user)
        ->get('/clinic/reports/export-excel')
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/vnd.ms-excel');
});
