<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\DB::table('devices')->insertOrIgnore([
            ['id' => 1, 'device_code' => 'GYM-001', 'building' => 'Gymnasium', 'floor' => '1st Floor', 'room' => 'Main Hall', 'latitude' => 7.7115556, 'longitude' => 123.2931667, 'status' => 'active'],
            ['id' => 2, 'device_code' => 'ENG-001', 'building' => 'Engineering Building', 'floor' => '2nd Floor', 'room' => 'Room 203', 'latitude' => 7.710675, 'longitude' => 123.291948, 'status' => 'active'],
            ['id' => 3, 'device_code' => 'LIB-001', 'building' => 'Library', 'floor' => '1st Floor', 'room' => 'Reading Area', 'latitude' => 7.708561, 'longitude' => 123.292544, 'status' => 'active'],
        ]);
    }
}
