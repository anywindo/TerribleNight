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
                'status' => AttendanceStatus::PRESENT,
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
                break;
            case EventType::START_BREAK:
                if ($hasStartedBreak) {
                    throw ValidationException::withMessages([
                        'event_type' => 'Break has already started.'
                    ]);
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
