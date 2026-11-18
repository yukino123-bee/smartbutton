<?php

use App\Models\Device;
use App\Models\Incident;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

function smsDevice(): Device
{
    return Device::create([
        'device_code' => 'LIB-001', 'building' => 'Library', 'floor' => '1st', 'room' => 'Main',
        'latitude' => 7.7, 'longitude' => 123.2, 'status' => 'active', 'last_seen' => now(),
    ]);
}

test('webhook reads the firmware SMS text and honours Clinic Aid', function () {
    smsDevice();

    $this->postJson('/api/sms-webhook', [
        'sender' => '+639000000000',
        'message' => "JHCSC PANIC ALERT (OFFLINE)!\nDevice: LIB-001 (Campus Library)\nCategory: Medical Emergency\nClinic Aid: NOT REQUIRED",
    ])->assertCreated();

    $incident = Incident::first();
    expect($incident->emergency_type)->toBe(Incident::TYPE_MEDICAL)
        ->and((bool) $incident->need_clinic)->toBeFalse()
        ->and($incident->reported_at)->not->toBeNull();
});

test('webhook still reads the old format and ignores a duplicate', function () {
    smsDevice();
    $payload = ['sender' => '+639000000000', 'message' => 'EMERGENCY: LIB-001 - Critical Emergency'];

    $this->postJson('/api/sms-webhook', $payload)->assertCreated();
    $this->postJson('/api/sms-webhook', $payload)->assertOk()->assertJson(['duplicate' => true]);

    expect(Incident::count())->toBe(1)
        ->and((bool) Incident::first()->need_clinic)->toBeTrue();
});

test('an alert re-sent after an SMS-only period keeps its real press time', function () {
    $device = makeDevice('DEV-LATE-01');

    $this->postJson('/api/emergency', [
        'device_id' => $device->device_code,
        'emergency_category' => Incident::TYPE_CRITICAL,
        'age_seconds' => 600,
    ])->assertSuccessful();

    $incident = Incident::latest('id')->first();
    expect($incident->reported_at->diffInSeconds(now(), true))->toBeBetween(598, 605)
        ->and($incident->remarks)->toContain('synced later');
});
