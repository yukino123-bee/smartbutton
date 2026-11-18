<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(['role' => 'DRRMO'], [
            'fullname' => 'Greg Lorenze Dominguez Quiling',
            'username' => 'gquiling',
            'password' => Hash::make('Greg@JHCSC2026'),
            'role' => 'DRRMO',
            'status' => 'active',
        ]);

        User::updateOrCreate(['role' => 'Clinic'], [
            'fullname' => 'Anzeille Mae E. Patigayon',
            'username' => 'apatigayon',
            'password' => Hash::make('Anzeille@JHCSC2026'),
            'role' => 'Clinic',
            'status' => 'active',
        ]);
    }
}
