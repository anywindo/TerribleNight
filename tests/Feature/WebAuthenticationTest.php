<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;

class WebAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->app[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'HR', 'guard_name' => 'sanctum']);
        Role::firstOrCreate(['name' => 'HR', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'sanctum']);
        Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'web']);
    }

    public function test_login_page_is_at_root()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_unauthenticated_users_are_redirected_to_root()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/');

        $response = $this->get('/app/attendance');
        $response->assertRedirect('/');
    }

    public function test_hr_user_redirected_to_admin_dashboard_after_login()
    {
        $hr = User::factory()->create([
            'password' => bcrypt('password')
        ]);
        $hr->assignRole('HR');

        $response = $this->post('/', [
            'email' => $hr->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($hr);
    }

    public function test_employee_redirected_to_app_after_login()
    {
        $employee = User::factory()->create([
            'password' => bcrypt('password')
        ]);
        $employee->assignRole('Employee');

        $response = $this->post('/', [
            'email' => $employee->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/app/attendance');
        $this->assertAuthenticatedAs($employee);
    }

    public function test_authenticated_hr_cannot_access_employee_routes()
    {
        $hr = User::factory()->create();
        $hr->assignRole('HR');

        $response = $this->actingAs($hr)->get('/app/attendance');
        $response->assertStatus(403);
    }

    public function test_authenticated_employee_cannot_access_hr_routes()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');

        $response = $this->actingAs($employee)->get('/admin/dashboard');
        $response->assertStatus(403);
    }
}
