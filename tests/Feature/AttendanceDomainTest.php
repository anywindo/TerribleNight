<?php

use App\Models\User;
use App\Models\Shift;
use App\Models\Attendance;
use App\Models\AttendanceEvent;
use App\Enums\EventType;
use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;



it('can create an attendance flow with events', function () {
    $manager = User::factory()->create();
    $user = User::factory()->create([
        'direct_supervisor_id' => $manager->id,
        'nik' => '123456789',
    ]);

    $shift = Shift::create([
        'shift_name' => 'Morning Shift',
        'default_start_time' => '08:00:00',
        'default_end_time' => '17:00:00',
    ]);

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'shift_id' => $shift->id,
        'date' => now()->toDateString(),
        'status' => AttendanceStatus::EXACT,
    ]);

    $event = AttendanceEvent::create([
        'attendance_id' => $attendance->id,
        'event_type' => EventType::START_SHIFT,
        'timestamp' => now(),
        'latitude' => -6.200000,
        'longitude' => 106.816666,
        'selfie_path' => 'selfies/dummy.jpg',
    ]);

    expect($attendance->events)->toHaveCount(1);
    expect($attendance->events->first()->event_type)->toBe(EventType::START_SHIFT);
    expect($attendance->user->nik)->toBe('123456789');
    expect($attendance->user->directSupervisor->id)->toBe($manager->id);
});
