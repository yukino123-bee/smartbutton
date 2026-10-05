<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            // Responder Assignment & Dispatch details
            $table->string('responder_name')->nullable()->after('status');
            $table->string('responder_contact')->nullable()->after('responder_name');
            $table->unsignedInteger('eta_minutes')->nullable()->after('responder_contact');
            $table->text('dispatch_notes')->nullable()->after('eta_minutes');

            // False Alarm / Drill classification
            $table->string('resolution_type', 50)->nullable()->index()->after('resolved_at');
            $table->string('false_alarm_reason')->nullable()->after('resolution_type');

            // Patient / Casualty & Triage logging
            $table->string('patient_name')->nullable()->after('false_alarm_reason');
            $table->string('patient_id_number', 50)->nullable()->after('patient_name');
            $table->string('triage_level', 20)->nullable()->after('patient_id_number');
            $table->text('treatment_summary')->nullable()->after('triage_level');
            $table->string('disposition', 100)->nullable()->after('treatment_summary');

            // Timestamped Response Audit Trail
            $table->timestamp('acknowledged_at')->nullable()->after('reported_at');
            $table->timestamp('dispatched_at')->nullable()->after('acknowledged_at');
            $table->timestamp('arrived_at')->nullable()->after('dispatched_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn([
                'responder_name',
                'responder_contact',
                'eta_minutes',
                'dispatch_notes',
                'resolution_type',
                'false_alarm_reason',
                'patient_name',
                'patient_id_number',
                'triage_level',
                'treatment_summary',
                'disposition',
                'acknowledged_at',
                'dispatched_at',
                'arrived_at',
            ]);
        });
    }
};
