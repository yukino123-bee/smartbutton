<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(['username' => 'ndrrmo'], [
            'fullname' => 'NDRRMO Administrator',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'NDRRMO',
            'status' => 'active'
        ]);

        \App\Models\User::updateOrCreate(['username' => 'clinic'], [
            'fullname' => 'Clinic Administrator',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'Clinic',
            'status' => 'active'
        ]);
    }
}
