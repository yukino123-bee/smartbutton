<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'incident_id', 'recipient', 'channel', 'status', 'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }
}
