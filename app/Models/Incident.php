<?php

namespace App\Models;

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

    protected $fillable = [
        'device_id', 'emergency_type', 'reported_at', 'status', 'remarks', 'resolved_at',
        'responder_name', 'responder_contact', 'eta_minutes', 'dispatch_notes',
        'resolution_type', 'false_alarm_reason',
        'patient_name', 'patient_id_number', 'triage_level', 'treatment_summary', 'disposition',
        'acknowledged_at', 'dispatched_at', 'arrived_at',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'arrived_at' => 'datetime',
        'resolved_at' => 'datetime',
        'eta_minutes' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['Pending', 'Acknowledged', 'Responding']);
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

    public function scopeClinicRelevant(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereIn('emergency_type', [
                self::TYPE_CRITICAL,
                self::TYPE_MEDICAL,
            ])->orWhereHas('notifications', function ($sub) {
                $sub->where('recipient', 'Clinic');
            });
        });
    }

    public function getAckDurationAttribute(): ?string
    {
        if ($this->reported_at && $this->acknowledged_at) {
            $secs = $this->reported_at->diffInSeconds($this->acknowledged_at);
            return $secs < 60 ? "{$secs}s" : floor($secs / 60) . 'm ' . ($secs % 60) . 's';
        }
        return null;
    }

    public function getTotalDurationAttribute(): ?string
    {
        if ($this->reported_at && $this->resolved_at) {
            $secs = $this->reported_at->diffInSeconds($this->resolved_at);
            return $secs < 60 ? "{$secs}s" : floor($secs / 60) . 'm ' . ($secs % 60) . 's';
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
