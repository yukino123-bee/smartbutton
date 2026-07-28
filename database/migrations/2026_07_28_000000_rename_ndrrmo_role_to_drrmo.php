<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'NDRRMO')
            ->update(['role' => 'DRRMO']);

        DB::table('notifications')
            ->where('recipient', 'NDRRMO')
            ->update(['recipient' => 'DRRMO']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('role', 'DRRMO')
            ->update(['role' => 'NDRRMO']);

        DB::table('notifications')
            ->where('recipient', 'DRRMO')
            ->update(['recipient' => 'NDRRMO']);
    }
};
