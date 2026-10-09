<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Enums\EventType;
use App\Models\Attendance;
use App\Models\Shift;
use App\Models\User;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Illuminate\Validation\ValidationException;

class AttendanceService extends BaseService
{

    public function __construct(AttendanceRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Record an attendance event for an employee.
     */
    public function recordEvent(User $employee, Shift $shift, EventType $eventType, array $data): Attendance
    {
        // 1. Validate incoming data (GPS & Selfie)
        if (empty($data['latitude']) || empty($data['longitude'])) {
            throw ValidationException::withMessages([
                'location' => 'GPS coordinates (latitude and longitude) are required.'
            ]);
        }
        if (empty($data['selfie_path'])) {
            throw ValidationException::withMessages([
                'selfie_path' => 'Selfie photo is required.'
            ]);
        }

        // 2. Fetch or create today's Attendance aggregate
        /** @var Attendance|null $attendance */
        $attendance = $this->repository->findTodayAttendanceForUser($employee->id);

        if (!$attendance) {
            // First event must be START_SHIFT
            if ($eventType !== EventType::START_SHIFT) {
                throw ValidationException::withMessages([
                    'event_type' => 'Cannot record this event. You must start your shift first.'
                ]);
            }

            $attendance = $this->repository->create([
                'user_id' => $employee->id,
                'shift_id' => $shift->id,
                'date' => today(),
                'status' => AttendanceStatus::EXACT,
            ]);
        }

        // 3. Enforce sequence rules
        $events = $attendance->events()->orderBy('timestamp')->get();
        
        $hasStartedShift = $events->contains('event_type', EventType::START_SHIFT);
        $hasStartedBreak = $events->contains('event_type', EventType::START_BREAK);
        $hasEndedBreak = $events->contains('event_type', EventType::END_BREAK);
        $hasEndedShift = $events->contains('event_type', EventType::END_SHIFT);

        if ($hasEndedShift) {
            throw ValidationException::withMessages([
                'event_type' => 'Shift has already ended for today.'
            ]);
        }

        switch ($eventType) {
            case EventType::START_SHIFT:
                if ($hasStartedShift) {
                    throw ValidationException::withMessages([
                        'event_type' => 'Shift has already started.'
                    ]);
                }

                $ruleEnabled = \App\Models\Setting::get('attendance_rule_enabled', false);
                if ($ruleEnabled) {
                    $ruleMinutes = \App\Models\Setting::get('attendance_rule_minutes', 15);
                    $shiftStartStr = $shift->default_start_time; // e.g. "08:00:00"
                    
                    if ($shiftStartStr) {
                        $shiftStartTime = \Carbon\Carbon::parse($shiftStartStr);
                        // Cutoff is exactly $ruleMinutes before the shift starts.
                        $cutoffTime = $shiftStartTime->copy()->subMinutes($ruleMinutes);
                        
                        $now = now();
                        // Format current time and cutoff time to compare only the time part, ignoring date
                        $nowTime = $now->format('H:i:s');
                        $cutoffTimeStr = $cutoffTime->format('H:i:s');

                        if ($nowTime >= $cutoffTimeStr) {
                            throw ValidationException::withMessages([
                                'event_type' => "Batas waktu presensi telah lewat. Anda harus presensi sebelum {$cutoffTime->format('H:i')} (15 menit sebelum shift)."
                            ]);
                        }
                    }
                }
                break;
            case EventType::START_BREAK:
                if ($hasStartedBreak) {
                    throw ValidationException::withMessages([
                        'event_type' => 'Break has already started.'
                    ]);
                }
                if ($shift->break_start) {
                    $breakStartTime = \Carbon\Carbon::parse($shift->break_start)->format('H:i:s');
                    $currentTime = now()->format('H:i:s');
                    if ($currentTime < $breakStartTime) {
                        throw ValidationException::withMessages([
                            'event_type' => 'Cannot start break before scheduled time ('.$breakStartTime.').'
                        ]);
                    }
                }
                break;
            case EventType::END_BREAK:
                if (!$hasStartedBreak) {
                    throw ValidationException::withMessages([
                        'event_type' => 'Cannot end break before starting it.'
                    ]);
                }
                if ($hasEndedBreak) {
                    throw ValidationException::withMessages([
                        'event_type' => 'Break has already ended.'
                    ]);
                }
                if ($shift->break_end) {
                    $breakEndTime = \Carbon\Carbon::parse($shift->break_end)->format('H:i:s');
                    $currentTime = now()->format('H:i:s');
                    if ($currentTime > $breakEndTime) {
                        throw ValidationException::withMessages([
                            'event_type' => 'Cannot end break after scheduled period ('.$breakEndTime.'). Please contact HR.'
                        ]);
                    }
                }
                break;
            case EventType::END_SHIFT:
                if ($hasStartedBreak && !$hasEndedBreak) {
                    throw ValidationException::withMessages([
                        'event_type' => 'Cannot end shift while on break.'
                    ]);
                }
                break;
        }

        // 4. Record the event
        $attendance->events()->create([
            'event_type' => $eventType,
            'timestamp' => now(),
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'selfie_path' => $data['selfie_path'],
        ]);

        return $attendance->load('events');
    }
}
