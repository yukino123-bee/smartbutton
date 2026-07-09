<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $fillable = [
        'device_id', 'emergency_type', 'reported_at', 'status', 'remarks', 'resolved_at'
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
