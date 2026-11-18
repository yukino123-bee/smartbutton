<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    protected $fillable = [
        'device_code', 'building', 'floor', 'room',
        'latitude', 'longitude', 'status', 'last_seen',
    ];

    protected $appends = ['is_online'];

    protected function casts(): array
    {
        return [
            'last_seen' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function getIsOnlineAttribute()
    {
        return $this->status === 'active'
            && $this->last_seen
            && $this->last_seen->gt(now()->subSeconds(90)); // idle buttons ping every 10 to 60 s
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }
}
