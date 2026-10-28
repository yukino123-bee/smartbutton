<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            // Set when the device's clinic button was pressed. The Clinic is only alerted once DRRMO acknowledges.
            $table->timestamp('clinic_requested_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn('clinic_requested_at');
        });
    }
};
