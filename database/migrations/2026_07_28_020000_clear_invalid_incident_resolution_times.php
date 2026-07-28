<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('incidents')
            ->whereNotNull('resolved_at')
            ->whereColumn('resolved_at', '<', 'reported_at')
            ->update(['resolved_at' => null]);
    }

    public function down(): void
    {
        // Invalid historical timestamps cannot be safely reconstructed.
    }
};
