<?php

use App\Models\Device;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

function createTestDevice(string $code = 'DEV-DISPATCH-01', string $building = 'Science Complex'): Device
{
    return Device::create([
        'device_code' => $code,
        'building' => $building,
        'floor' => '1st Floor',
        'room' => 'Physics Lab',
        'latitude' => 7.708600,
        'longitude' => 123.292400,
        'status' => 'active',
        'last_seen' => now(),
    ]);
}

test('drrmo operator can dispatch first responders with full assignment metadata', function () {
    $device = createTestDevice('DEV-DSP-01', 'Admin Building');
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_CRITICAL,
        'status' => 'Acknowledged',
        'reported_at' => now()->subMinutes(5),
        'acknowledged_at' => now()->subMinutes(4),
    ]);

    $ndrrmoUser = User::factory()->create(['role' => 'DRRMO']);

    $response = $this->actingAs($ndrrmoUser)
        ->post("/ndrrmo/incidents/{$incident->id}/dispatch", [
            'responder_name' => 'Officer Dela Cruz - DRRMO Rapid Unit',
            'responder_contact' => '0917-889-1001 / Radio Ch. 1',
            'eta_minutes' => 3,
            'dispatch_notes' => 'Bring spine board and oxygen tank to east lobby',
        ]);

    $response->assertRedirect();

    $incident->refresh();
    expect($incident->status)->toBe('Responding')
        ->and($incident->responder_name)->toBe('Officer Dela Cruz - DRRMO Rapid Unit')
        ->and($incident->responder_contact)->toBe('0917-889-1001 / Radio Ch. 1')
        ->and($incident->eta_minutes)->toBe(3)
        ->and($incident->dispatch_notes)->toBe('Bring spine board and oxygen tank to east lobby')
        ->and($incident->dispatched_at)->not->toBeNull();
});

test('drrmo can mark first responder team arrived on scene', function () {
    $device = createTestDevice('DEV-DSP-02', 'Gymnasium');
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_CRITICAL,
        'status' => 'Responding',
        'reported_at' => now()->subMinutes(6),
        'dispatched_at' => now()->subMinutes(3),
        'responder_name' => 'Campus Security Patrol Alpha',
    ]);

    $ndrrmoUser = User::factory()->create(['role' => 'DRRMO']);

    $response = $this->actingAs($ndrrmoUser)
        ->post("/ndrrmo/incidents/{$incident->id}/on-scene");

    $response->assertRedirect();

    $incident->refresh();
    expect($incident->arrived_at)->not->toBeNull()
        ->and($incident->status)->toBe('Responding');
});

test('incident can be resolved and classified as false alarm with specific reason', function () {
    $device = createTestDevice('DEV-DSP-03', 'Library Building');
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_CRITICAL,
        'status' => 'Responding',
        'reported_at' => now()->subMinutes(10),
    ]);

    $ndrrmoUser = User::factory()->create(['role' => 'DRRMO']);

    $response = $this->actingAs($ndrrmoUser)
        ->post("/ndrrmo/incidents/{$incident->id}/resolve", [
            'resolution_type' => 'False Alarm',
            'false_alarm_reason' => 'Accidental button push by student / staff',
            'remarks' => 'Student leaned against the wall button during crowded hallway exchange',
        ]);

    $response->assertRedirect();

    $incident->refresh();
    expect($incident->status)->toBe('Resolved')
        ->and($incident->resolution_type)->toBe('False Alarm')
        ->and($incident->false_alarm_reason)->toBe('Accidental button push by student / staff')
        ->and($incident->resolved_at)->not->toBeNull();

    expect(Incident::falseAlarms()->where('id', $incident->id)->exists())->toBeTrue()
        ->and(Incident::realEmergencies()->where('id', $incident->id)->exists())->toBeFalse();
});

test('incident can be resolved and classified as campus drill simulation', function () {
    $device = createTestDevice('DEV-DSP-04', 'Engineering Annex');
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_CRITICAL,
        'status' => 'Responding',
        'reported_at' => now()->subMinutes(15),
    ]);

    $ndrrmoUser = User::factory()->create(['role' => 'DRRMO']);

    $response = $this->actingAs($ndrrmoUser)
        ->post("/ndrrmo/incidents/{$incident->id}/resolve", [
            'resolution_type' => 'Drill',
            'remarks' => 'Quarterly Earthquake Drill (NSED)',
        ]);

    $response->assertRedirect();

    $incident->refresh();
    expect($incident->status)->toBe('Resolved')
        ->and($incident->resolution_type)->toBe('Drill')
        ->and($incident->resolved_at)->not->toBeNull();

    expect(Incident::drills()->where('id', $incident->id)->exists())->toBeTrue()
        ->and(Incident::realEmergencies()->where('id', $incident->id)->exists())->toBeFalse();
});

test('clinic can record casualty triage details upon medical resolution', function () {
    $device = createTestDevice('DEV-DSP-05', 'Nursing Clinic');
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_MEDICAL,
        'status' => 'Responding',
        'reported_at' => now()->subMinutes(12),
        'dispatched_at' => now()->subMinutes(9),
    ]);

    $clinicUser = User::factory()->create(['role' => 'Clinic']);

    $response = $this->actingAs($clinicUser)
        ->post("/clinic/incidents/{$incident->id}/resolve", [
            'resolution_type' => 'Resolved',
            'patient_name' => 'Maria Kristina Santos',
            'patient_id_number' => '2024-00123',
            'triage_level' => 'Yellow',
            'treatment_summary' => 'Splinted right forearm fracture, vitals checked BP 120/80, cold compress applied',
            'disposition' => 'Referred / Transferred to external hospital via EMS',
            'remarks' => 'Parent notified and arriving at hospital',
        ]);

    $response->assertRedirect();

    $incident->refresh();
    expect($incident->status)->toBe('Resolved')
        ->and($incident->patient_name)->toBe('Maria Kristina Santos')
        ->and($incident->patient_id_number)->toBe('2024-00123')
        ->and($incident->triage_level)->toBe('Yellow')
        ->and($incident->treatment_summary)->toContain('Splinted right forearm')
        ->and($incident->disposition)->toBe('Referred / Transferred to external hospital via EMS')
        ->and($incident->resolved_at)->not->toBeNull();

    expect(Incident::realEmergencies()->where('id', $incident->id)->exists())->toBeTrue();
});

test('incident calculates ack_duration and total_duration accessors correctly', function () {
    $device = createTestDevice('DEV-DSP-06', 'Main Gate');
    $reported = now()->subMinutes(10);
    $acked = $reported->copy()->addMinutes(2);
    $resolved = $reported->copy()->addMinutes(8);

    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_CRITICAL,
        'status' => 'Resolved',
        'reported_at' => $reported,
        'acknowledged_at' => $acked,
        'resolved_at' => $resolved,
    ]);

    expect($incident->ack_duration)->toBe('2m 0s')
        ->and($incident->total_duration)->toBe('8m 0s');
});

test('clinic can confirm on scene arrival', function () {
    $device = createTestDevice('DEV-DSP-07', 'Cafeteria');
    $incident = Incident::create([
        'device_id' => $device->id,
        'emergency_type' => Incident::TYPE_MEDICAL,
        'status' => 'Responding',
        'reported_at' => now()->subMinutes(5),
        'dispatched_at' => now()->subMinutes(2),
        'responder_name' => 'Clinic Nurse Team Alpha',
    ]);

    $clinicUser = User::factory()->create(['role' => 'Clinic']);

    $response = $this->actingAs($clinicUser)
        ->post("/clinic/incidents/{$incident->id}/on-scene");

    $response->assertRedirect();

    $incident->refresh();
    expect($incident->arrived_at)->not->toBeNull();
});
