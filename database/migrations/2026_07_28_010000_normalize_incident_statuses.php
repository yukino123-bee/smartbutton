<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('incidents')->whereRaw('LOWER(status) = ?', ['pending'])->update(['status' => 'Pending']);
        DB::table('incidents')->whereRaw('LOWER(status) = ?', ['acknowledged'])->update(['status' => 'Acknowledged']);
        DB::table('incidents')->whereRaw('LOWER(status) = ?', ['responding'])->update(['status' => 'Responding']);
        DB::table('incidents')->whereRaw('LOWER(status) = ?', ['resolved'])->update(['status' => 'Resolved']);
        DB::table('incidents')
            ->where('status', 'Resolved')
            ->whereNull('resolved_at')
            ->update(['resolved_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        // Status normalization is intentionally retained.
    }
};
