<?php

namespace App\Models;

use App\Events\EmergencyReported;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incident extends Model
{
    public const TYPE_CRITICAL = 'Critical Emergency';

    public const TYPE_MEDICAL = 'Medical Emergency';

    public const TYPE_PUBLIC_SAFETY = 'Public Safety Emergency';

    public const EMERGENCY_TYPES = [
        self::TYPE_CRITICAL,
        self::TYPE_MEDICAL,
        self::TYPE_PUBLIC_SAFETY,
    ];

    public const RESOLUTION_RESOLVED = 'Resolved';

    public const RESOLUTION_FALSE_ALARM = 'False Alarm';

    public const RESOLUTION_DRILL = 'Drill';

    public const TRIAGE_RED = 'Red - Immediate';

    public const TRIAGE_YELLOW = 'Yellow - Delayed';

    public const TRIAGE_GREEN = 'Green - Minor';

    public const TRIAGE_BLACK = 'Black - Expectant';

    /** Incident fields the Clinic keeps its own copy of (stored as clinic_<field>). */
    public const CLINIC_FIELDS = [
        'status', 'acknowledged_at', 'dispatched_at', 'arrived_at', 'resolved_at',
        'resolution_type', 'false_alarm_reason', 'remarks',
        'responder_name', 'responder_contact', 'eta_minutes', 'dispatch_notes',
        'patient_name', 'patient_id_number', 'triage_level', 'treatment_summary', 'disposition',
    ];

    protected $fillable = [
        'device_id', 'emergency_type', 'need_clinic', 'reported_at', 'status', 'remarks', 'resolved_at',
        'responder_name', 'responder_contact', 'eta_minutes', 'dispatch_notes',
        'resolution_type', 'false_alarm_reason',
        'patient_name', 'patient_id_number', 'triage_level', 'treatment_summary', 'disposition',
        'acknowledged_at', 'dispatched_at', 'arrived_at',
        'clinic_status', 'clinic_acknowledged_at', 'clinic_dispatched_at', 'clinic_arrived_at', 'clinic_resolved_at',
        'clinic_resolution_type', 'clinic_false_alarm_reason', 'clinic_remarks',
        'clinic_responder_name', 'clinic_responder_contact', 'clinic_eta_minutes', 'clinic_dispatch_notes',
        'clinic_patient_name', 'clinic_patient_id_number', 'clinic_triage_level', 'clinic_treatment_summary',
        'clinic_disposition', 'clinic_hidden_at', 'clinic_requested_at',
    ];

    protected $casts = [
        'need_clinic' => 'boolean',
        'reported_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'arrived_at' => 'datetime',
        'resolved_at' => 'datetime',
        'eta_minutes' => 'integer',
        'clinic_acknowledged_at' => 'datetime',
        'clinic_dispatched_at' => 'datetime',
        'clinic_arrived_at' => 'datetime',
        'clinic_resolved_at' => 'datetime',
        'clinic_eta_minutes' => 'integer',
        'clinic_hidden_at' => 'datetime',
        'clinic_requested_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['Pending', 'Acknowledged', 'Responding']);
    }

    public function scopeClinicActive(Builder $query): Builder
    {
        return $query->whereIn('clinic_status', ['Pending', 'Acknowledged', 'Responding']);
    }

    public function scopeClinicResolved(Builder $query): Builder
    {
        return $query->where('clinic_status', 'Resolved');
    }

    /** Clinic-relevant incidents the Clinic has not deleted from its own dashboard. */
    public function scopeClinicVisible(Builder $query): Builder
    {
        return $query->clinicRelevant()->whereNull('clinic_hidden_at');
    }

    public function scopeResolved(Builder $query): Builder
    {
        return $query->where('status', 'Resolved');
    }

    public function scopeRealEmergencies(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('resolution_type')
                ->orWhere('resolution_type', self::RESOLUTION_RESOLVED);
        });
    }

    public function scopeFalseAlarms(Builder $query): Builder
    {
        return $query->where('resolution_type', self::RESOLUTION_FALSE_ALARM);
    }

    public function scopeDrills(Builder $query): Builder
    {
        return $query->where('resolution_type', self::RESOLUTION_DRILL);
    }

    public function involvesClinic(): bool
    {
        return $this->emergency_type === self::TYPE_CRITICAL
            || ($this->emergency_type === self::TYPE_MEDICAL && $this->need_clinic)
            || $this->notifications()->where('recipient', 'Clinic')->exists();
    }

    public function scopeClinicRelevant(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('emergency_type', self::TYPE_CRITICAL)
                ->orWhere(function (Builder $sub) {
                    $sub->where('emergency_type', self::TYPE_MEDICAL)
                        ->where('need_clinic', true);
                })
                ->orWhereHas('notifications', function ($sub) {
                    $sub->where('recipient', 'Clinic');
                });
        });
    }

    /**
     * The device's clinic button was pressed. The Clinic is alerted only after DRRMO has acknowledged the
     * incident: until then the request is held (clinic_requested_at). Returns true if the Clinic was alerted now.
     */
    public function requestClinic(): bool
    {
        if ($this->need_clinic) {
            return true;
        }

        $this->update(['clinic_requested_at' => $this->clinic_requested_at ?? now()]);

        if ($this->status === 'Pending') {
            return false;
        }

        return $this->releaseClinicRequest();
    }

    /** DRRMO acknowledged: alert the Clinic if the clinic button was pressed in the meantime. */
    public function releaseClinicRequest(): bool
    {
        if ($this->need_clinic || ! $this->clinic_requested_at) {
            return false;
        }

        $this->update([
            'need_clinic' => true,
            'remarks' => 'Medical Emergency (Witness requested Clinic Aid)',
        ]);

        Notification::firstOrCreate([
            'incident_id' => $this->id,
            'recipient' => 'Clinic',
        ], [
            'channel' => 'Dashboard',
            'status' => 'Delivered',
            'sent_at' => now(),
        ]);

        broadcast(new EmergencyReported($this));

        return true;
    }

    /**
     * DRRMO resolved the incident: close the Clinic's copy too, unless the Clinic already resolved it itself.
     */
    public function resolveClinicSide(string $resolutionType, ?string $falseAlarmReason, ?string $remarks): void
    {
        if (! $this->involvesClinic() || $this->clinic_status === 'Resolved') {
            return;
        }

        $this->update([
            'clinic_status' => 'Resolved',
            'clinic_resolved_at' => now(),
            'clinic_resolution_type' => $resolutionType,
            'clinic_false_alarm_reason' => $falseAlarmReason,
            'clinic_remarks' => $remarks ?? $this->clinic_remarks,
        ]);
    }

    /**
     * Show the Clinic's own state (clinic_*) in place of the shared DRRMO fields.
     * For rendering Clinic pages/JSON only: never save or broadcast the model afterwards.
     */
    public function clinicView(): static
    {
        foreach (self::CLINIC_FIELDS as $field) {
            $value = $this->getAttributeValue('clinic_'.$field);
            $this->setAttribute($field, $field === 'remarks' ? ($value ?? $this->remarks) : $value);
        }

        return $this;
    }

    public function getAckDurationAttribute(): ?string
    {
        if ($this->reported_at && $this->acknowledged_at) {
            $secs = $this->reported_at->diffInSeconds($this->acknowledged_at);

            return $secs < 60 ? "{$secs}s" : floor($secs / 60).'m '.($secs % 60).'s';
        }

        return null;
    }

    public function getTotalDurationAttribute(): ?string
    {
        if ($this->reported_at && $this->resolved_at) {
            $secs = $this->reported_at->diffInSeconds($this->resolved_at);

            return $secs < 60 ? "{$secs}s" : floor($secs / 60).'m '.($secs % 60).'s';
        }

        return null;
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
