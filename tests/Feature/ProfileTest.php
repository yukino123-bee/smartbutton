<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('guests are redirected from profile routes', function () {
    $this->get('/ndrrmo/profile')->assertRedirect('/login');
    $this->get('/clinic/profile')->assertRedirect('/login');
});

test('ndrrmo user can view profile page', function () {
    $user = User::factory()->create([
        'role' => 'DRRMO',
        'status' => 'active',
    ]);

    $this->actingAs($user)
        ->get('/ndrrmo/profile')
        ->assertSuccessful()
        ->assertSee($user->fullname);
});

test('clinic user can view profile page', function () {
    $user = User::factory()->create([
        'role' => 'Clinic',
        'status' => 'active',
    ]);

    $this->actingAs($user)
        ->get('/clinic/profile')
        ->assertSuccessful()
        ->assertSee($user->fullname);
});

test('administrator can update profile details', function () {
    $user = User::factory()->create([
        'fullname' => 'Old Name',
        'username' => 'oldusername',
        'role' => 'DRRMO',
    ]);

    $this->actingAs($user)
        ->put('/ndrrmo/profile', [
            'fullname' => 'New Admin Name',
            'username' => 'newadminusername',
        ])
        ->assertRedirect();

    $user->refresh();

    expect($user->fullname)->toBe('New Admin Name');
    expect($user->username)->toBe('newadminusername');
});

test('administrator can update password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('oldpassword123'),
        'role' => 'DRRMO',
    ]);

    $this->actingAs($user)
        ->put('/ndrrmo/profile/password', [
            'current_password' => 'oldpassword123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])
        ->assertRedirect();

    $user->refresh();

    expect(Hash::check('newpassword123', $user->password))->toBeTrue();
});
