<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipment = [
            ['name' => 'Stretcher 1', 'type' => 'Stretcher', 'status' => 'Ready', 'location' => 'Main Clinic Bay'],
            ['name' => 'Stretcher 2', 'type' => 'Stretcher', 'status' => 'Ready', 'location' => 'Main Clinic Bay'],
            ['name' => 'Wheelchair 1', 'type' => 'Wheelchair', 'status' => 'Ready', 'location' => 'Entrance'],
            ['name' => 'Defibrillator (AED)', 'type' => 'AED', 'status' => 'Ready', 'location' => 'Emergency Room'],
            ['name' => 'Trauma First Aid Kit', 'type' => 'First Aid', 'status' => 'Ready', 'location' => 'Nurse Station'],
            ['name' => 'Oxygen Tank A', 'type' => 'Oxygen', 'status' => 'Ready', 'location' => 'Storage'],
        ];

        foreach ($equipment as $item) {
            \App\Models\Equipment::create($item);
        }
    }
}
