<?php

namespace Tests\Feature\Api;

use App\Enums\AttendanceStatus;
use App\Enums\EventType;
use App\Models\Attendance;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceApiTest extends TestCase
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

    public function test_employee_can_view_own_attendance_history()
    {
        $employee = $this->createEmployeeUser();
        $shift = Shift::factory()->create();
        
        Attendance::create([
            'user_id' => $employee->id,
            'shift_id' => $shift->id,
            'date' => Carbon::today(),
            'status' => AttendanceStatus::PRESENT,
        ]);

        $response = $this->actingAs($employee, 'sanctum')->getJson('/api/v1/employee/attendance/history');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id', 'user_id', 'shift_id', 'date', 'status', 'events'
                         ]
                     ]
                 ]);
        
        $this->assertCount(1, $response->json('data'));
    }

    public function test_employee_can_submit_attendance_event()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $employee = $this->createEmployeeUser();
        $shift = \App\Models\Shift::factory()->create();

        $file = \Illuminate\Http\Testing\File::image('selfie.jpg');

        $payload = [
            'shift_id' => $shift->id,
            'event_type' => EventType::START_SHIFT->value,
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'selfie' => $file,
        ];

        $response = $this->actingAs($employee, 'sanctum')->postJson('/api/v1/employee/attendance/events', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('message', 'Attendance event recorded successfully.')
                 ->assertJsonStructure([
                     'data' => ['id', 'user_id', 'status', 'events']
                 ]);
    }

    public function test_admin_can_view_all_attendances()
    {
        $employee = $this->createEmployeeUser();
        $admin = $this->createAdminUser();
        $shift = Shift::factory()->create();
        
        Attendance::create([
            'user_id' => $employee->id,
            'shift_id' => $shift->id,
            'date' => Carbon::today(),
            'status' => AttendanceStatus::PRESENT,
        ]);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/v1/admin/attendances');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_employee_cannot_view_admin_attendances()
    {
        $employee = $this->createEmployeeUser();

        $response = $this->actingAs($employee, 'sanctum')->getJson('/api/v1/admin/attendances');

        $response->assertStatus(403);
    }
}
