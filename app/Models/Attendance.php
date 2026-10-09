<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'status' => \App\Enums\AttendanceStatus::class,
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function events()
    {
        return $this->hasMany(AttendanceEvent::class);
    }

    public function getShiftStatusAttribute()
    {
        $startEvent = $this->events->where('event_type', \App\Enums\EventType::START_SHIFT)->first();
        if (!$startEvent || !$this->shift || !$this->shift->default_start_time) {
            return ['label' => $this->status->label(), 'color' => $this->status->color()];
        }

        $actual = \Carbon\Carbon::parse($startEvent->timestamp)->format('H:i:s');
        $expected = \Carbon\Carbon::parse($this->shift->default_start_time);

        $endEvent = $this->events->where('event_type', \App\Enums\EventType::END_SHIFT)->first();
        $isEarlyOut = false;
        
        if ($endEvent && $this->shift->default_end_time) {
            $actualEnd = \Carbon\Carbon::parse($endEvent->timestamp)->format('H:i:s');
            $expectedEnd = \Carbon\Carbon::parse($this->shift->default_end_time)->format('H:i:s');
            if ($actualEnd < $expectedEnd) {
                $isEarlyOut = true;
            }
        }
        
        $earlyOutSuffix = $isEarlyOut ? ', Pulang awal' : '';

        $ruleEnabled = \App\Models\Setting::get('attendance_rule_enabled', false);
        if ($ruleEnabled) {
            $ruleMinutes = \App\Models\Setting::get('attendance_rule_minutes', 15);
            $cutoffTimeStr = $expected->copy()->subMinutes($ruleMinutes)->format('H:i:s');
            
            if ($actual >= $cutoffTimeStr) {
                return ['label' => 'Terlambat' . $earlyOutSuffix, 'color' => 'danger'];
            }
            return ['label' => 'Hadir' . $earlyOutSuffix, 'color' => $isEarlyOut ? 'warning' : 'success'];
        } else {
            $expectedStr = $expected->format('H:i:s');
            if ($actual <= $expectedStr) {
                return ['label' => 'Hadir' . $earlyOutSuffix, 'color' => $isEarlyOut ? 'warning' : 'success'];
            } else {
                return ['label' => 'Terlambat' . $earlyOutSuffix, 'color' => 'danger'];
            }
        }
    }

    public function getBreakStatusAttribute()
    {
        $startBreakEvent = $this->events->where('event_type', \App\Enums\EventType::START_BREAK)->first();
        $endBreakEvent = $this->events->where('event_type', \App\Enums\EventType::END_BREAK)->first();
        
        if (!$startBreakEvent || !$this->shift || !$this->shift->break_start || !$this->shift->break_end) {
            return ['label' => '-', 'color' => 'secondary'];
        }
        
        if (!$endBreakEvent) {
            return ['label' => 'Sedang Istirahat', 'color' => 'warning'];
        }

        $actualDuration = \Carbon\Carbon::parse($endBreakEvent->timestamp)->diffInMinutes(\Carbon\Carbon::parse($startBreakEvent->timestamp));
        $expectedDuration = \Carbon\Carbon::parse($this->shift->break_end)->diffInMinutes(\Carbon\Carbon::parse($this->shift->break_start));

        if ($actualDuration <= $expectedDuration) {
            return ['label' => 'Tepat Waktu', 'color' => 'success'];
        } else {
            return ['label' => 'Terlambat', 'color' => 'danger'];
        }
    }
}
