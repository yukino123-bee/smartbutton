<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The Clinic gets its own copy of the incident lifecycle so that Clinic actions
 * (acknowledge, dispatch, on scene, resolve, false alarm, delete) never change what DRRMO sees.
 */
return new class extends Migration
{
    private const COPIED_COLUMNS = [
        'acknowledged_at', 'dispatched_at', 'arrived_at', 'resolved_at',
        'resolution_type', 'false_alarm_reason', 'remarks',
        'responder_name', 'responder_contact', 'eta_minutes', 'dispatch_notes',
        'patient_name', 'patient_id_number', 'triage_level', 'treatment_summary', 'disposition',
    ];

    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->string('clinic_status', 30)->default('Pending');
            $table->timestamp('clinic_acknowledged_at')->nullable();
            $table->timestamp('clinic_dispatched_at')->nullable();
            $table->timestamp('clinic_arrived_at')->nullable();
            $table->timestamp('clinic_resolved_at')->nullable();
            $table->string('clinic_resolution_type', 30)->nullable();
            $table->string('clinic_false_alarm_reason')->nullable();
            $table->text('clinic_remarks')->nullable();
            $table->string('clinic_responder_name', 150)->nullable();
            $table->string('clinic_responder_contact', 100)->nullable();
            $table->unsignedSmallInteger('clinic_eta_minutes')->nullable();
            $table->text('clinic_dispatch_notes')->nullable();
            $table->string('clinic_patient_name', 150)->nullable();
            $table->string('clinic_patient_id_number', 50)->nullable();
            $table->string('clinic_triage_level', 30)->nullable();
            $table->text('clinic_treatment_summary')->nullable();
            $table->string('clinic_disposition', 100)->nullable();
            $table->timestamp('clinic_hidden_at')->nullable();
        });

        // Existing incidents keep showing the same state on the Clinic side.
        $set = ['clinic_status' => DB::raw('status')];
        foreach (self::COPIED_COLUMNS as $column) {
            $set['clinic_'.$column] = DB::raw($column);
        }
        DB::table('incidents')->update($set);
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn(array_merge(
                ['clinic_status', 'clinic_hidden_at'],
                array_map(fn ($column) => 'clinic_'.$column, self::COPIED_COLUMNS),
            ));
        });
    }
};
