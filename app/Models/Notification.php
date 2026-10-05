<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'incident_id', 'recipient', 'channel', 'status', 'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function getMessageAttribute(): string
    {
        if ($this->incident && $this->incident->device) {
            $device = $this->incident->device;
            $location = trim("{$device->building} {$device->floor} {$device->room}");

            return "EMERGENCY ALERT: [{$device->device_code}] {$this->incident->emergency_type} at {$location}";
        }

        if ($this->incident) {
            return "EMERGENCY ALERT: Incident #{$this->incident->id} - {$this->incident->emergency_type}";
        }

        return "EMERGENCY ALERT - Recipient: {$this->recipient}";
    }
}
