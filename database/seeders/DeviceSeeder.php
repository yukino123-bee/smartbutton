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
            ['id' => 1, 'device_code' => 'GYM-001', 'building' => 'Gymnasium', 'floor' => '1st Floor', 'room' => 'Main Hall', 'latitude' => 8.1234567, 'longitude' => 123.1234567, 'status' => 'active'],
            ['id' => 2, 'device_code' => 'ENG-001', 'building' => 'Engineering Building', 'floor' => '2nd Floor', 'room' => 'Room 203', 'latitude' => 8.1235567, 'longitude' => 123.1236567, 'status' => 'active'],
            ['id' => 3, 'device_code' => 'LIB-001', 'building' => 'Library', 'floor' => '1st Floor', 'room' => 'Reading Area', 'latitude' => 8.1233567, 'longitude' => 123.1232567, 'status' => 'active'],
        ]);
    }
}
