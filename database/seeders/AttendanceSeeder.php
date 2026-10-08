<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Shift;
use App\Models\Attendance;
use App\Models\AttendanceEvent;
use App\Enums\AttendanceStatus;
use App\Enums\EventType;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = User::role('Employee')->get();
        $shift = Shift::first();

        if ($employees->isEmpty() || !$shift) {
            return; // Ensure users and shifts exist
        }

        foreach ($employees as $employee) {
            // Create an attendance record for yesterday
            $attendance = Attendance::factory()->create([
                'user_id' => $employee->id,
                'shift_id' => $shift->id,
                'date' => Carbon::yesterday()->toDateString(),
                'status' => AttendanceStatus::PRESENT,
            ]);

            // Start Shift Event
            AttendanceEvent::factory()->create([
                'attendance_id' => $attendance->id,
                'event_type' => EventType::START_SHIFT,
                'timestamp' => Carbon::yesterday()->setTimeFromTimeString($shift->default_start_time),
            ]);

            // Start Break
            AttendanceEvent::factory()->create([
                'attendance_id' => $attendance->id,
                'event_type' => EventType::START_BREAK,
                'timestamp' => Carbon::yesterday()->setTime(12, 0, 0),
            ]);

            // End Break
            AttendanceEvent::factory()->create([
                'attendance_id' => $attendance->id,
                'event_type' => EventType::END_BREAK,
                'timestamp' => Carbon::yesterday()->setTime(13, 0, 0),
            ]);

            // End Shift Event
            AttendanceEvent::factory()->create([
                'attendance_id' => $attendance->id,
                'event_type' => EventType::END_SHIFT,
                'timestamp' => Carbon::yesterday()->setTimeFromTimeString($shift->default_end_time),
            ]);
        }
    }
}
