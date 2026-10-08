<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceEvent extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'event_type' => \App\Enums\EventType::class,
        'timestamp' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
