<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'device_code', 'building', 'floor', 'room', 
        'latitude', 'longitude', 'status', 'last_seen'
    ];

    protected $casts = [
        'last_seen' => 'datetime',
    ];

    public function getIsOnlineAttribute()
    {
        if ($this->status === 'online') {
            return true;
        }
        if ($this->status === 'active' && $this->last_seen && $this->last_seen->gt(now()->subMinutes(5))) {
            return true;
        }
        return false;
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}
