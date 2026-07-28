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
        \App\Models\User::updateOrCreate(['role' => 'DRRMO'], [
            'fullname' => 'Greg Lorenze Dominguez Quiling',
            'username' => 'gquiling',
            'password' => \Illuminate\Support\Facades\Hash::make('Greg@JHCSC2026'),
            'role' => 'DRRMO',
            'status' => 'active'
        ]);

        \App\Models\User::updateOrCreate(['role' => 'Clinic'], [
            'fullname' => 'Anzeille Mae E. Patigayon',
            'username' => 'apatigayon',
            'password' => \Illuminate\Support\Facades\Hash::make('Anzeille@JHCSC2026'),
            'role' => 'Clinic',
            'status' => 'active'
        ]);
    }
}
