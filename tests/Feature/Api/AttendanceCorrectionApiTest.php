<?php

namespace Tests\Feature\Api;

use App\Enums\AttendanceStatus;
use App\Enums\RequestStatus;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceCorrectionApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin', 'guard_name' => 'sanctum']);
    }

    private function createEmployeeUser(): User
    {
        return User::factory()->create();
    }

    private function createAdminUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        return $user;
    }

    private function createAttendance(User $employee): Attendance
    {
        $shift = Shift::factory()->create();
        return Attendance::create([
            'user_id' => $employee->id,
            'shift_id' => $shift->id,
            'date' => Carbon::today(),
            'status' => AttendanceStatus::EXACT,
        ]);
    }

    public function test_employee_can_submit_correction_request()
    {
        $employee = $this->createEmployeeUser();
        $attendance = $this->createAttendance($employee);

        $payload = [
            'attendance_id' => $attendance->id,
            'reason' => 'Forgot to check out',
            'requested_changes' => [
                'end_shift' => '17:00:00'
            ]
        ];

        $response = $this->actingAs($employee, 'sanctum')->postJson('/api/v1/employee/correction-requests', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('data.status', RequestStatus::PENDING->value);
    }

    public function test_admin_can_approve_correction_request()
    {
        $employee = $this->createEmployeeUser();
        $admin = $this->createAdminUser();
        $attendance = $this->createAttendance($employee);

        $request = AttendanceCorrectionRequest::create([
            'user_id' => $employee->id,
            'attendance_id' => $attendance->id,
            'reason' => 'Forgot to check out',
            'requested_changes' => ['end_shift' => '17:00:00'],
            'status' => RequestStatus::PENDING,
        ]);

        $payload = [
            'status' => RequestStatus::APPROVED->value,
            'notes' => 'Approved',
        ];

        $response = $this->actingAs($admin, 'sanctum')->putJson("/api/v1/admin/correction-requests/{$request->id}/process", $payload);

        $response->assertStatus(200)
                 ->assertJsonPath('data.status', RequestStatus::APPROVED->value);
                 
        $this->assertDatabaseHas('attendance_correction_requests', [
            'id' => $request->id,
            'status' => RequestStatus::APPROVED,
            'approved_by' => $admin->id
        ]);
    }
}
