<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientRecord extends Model
{
    protected $fillable = [
        'incident_id', 'patient_name', 'student_id', 'injury_details', 'treatment_given', 'status'
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }
}
