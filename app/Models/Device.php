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

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}
