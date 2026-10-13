<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Location;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = bcrypt('password');
        $locationId = Location::first()->id ?? null;

        $superAdminRoleSanctum = Role::findByName('super-admin', 'sanctum');
        $superAdminRoleWeb = Role::findByName('super-admin', 'web');
        $hrRoleSanctum = Role::findByName('HR', 'sanctum');
        $hrRoleWeb = Role::findByName('HR', 'web');
        $employeeRoleSanctum = Role::findByName('Employee', 'sanctum');
        $employeeRoleWeb = Role::findByName('Employee', 'web');

        // Create Superadmin
        $superadmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'employee_id' => 'SA-00001',
            'nik' => '1111222233334444',
            'password' => $defaultPassword,
            'location_id' => $locationId,
        ]);
        $superadmin->roles()->syncWithoutDetaching([$superAdminRoleSanctum->id, $superAdminRoleWeb->id]);

        // Create an HR Admin
        $hr = User::factory()->create([
            'name' => 'HR Admin',
            'email' => 'hr@example.com',
            'employee_id' => 'HR-00001',
            'nik' => '5555666677778888',
            'password' => $defaultPassword,
            'location_id' => $locationId,
        ]);
        $hr->roles()->syncWithoutDetaching([$hrRoleSanctum->id, $hrRoleWeb->id]);

        // Create Managers (2 managers)
        $managers = User::factory(2)->create([
            'password' => $defaultPassword,
            'location_id' => $locationId,
        ])->each(function ($user) use ($employeeRoleSanctum, $employeeRoleWeb) {
            $user->roles()->syncWithoutDetaching([$employeeRoleSanctum->id, $employeeRoleWeb->id]);
        });

        // Create Supervisors (1 supervisor per manager = 2 total)
        $supervisors = collect();
        foreach ($managers as $manager) {
            $createdSupervisors = User::factory(1)->create([
                'password' => $defaultPassword,
                'immediate_manager_id' => $manager->id,
                'location_id' => $manager->location_id,
            ])->each(function ($user) use ($employeeRoleSanctum, $employeeRoleWeb) {
                $user->roles()->syncWithoutDetaching([$employeeRoleSanctum->id, $employeeRoleWeb->id]);
            });
            $supervisors = $supervisors->merge($createdSupervisors);
        }

        // Create an Employee (for testing)
        $employee = User::factory()->create([
            'name' => 'John Employee',
            'email' => 'employee@example.com',
            'employee_id' => 'EMP-00001',
            'nik' => '9999000011112222',
            'password' => $defaultPassword,
            'direct_supervisor_id' => $supervisors->first()?->id,
            'immediate_manager_id' => $managers->first()?->id,
            'location_id' => $locationId,
        ]);
        $employee->roles()->syncWithoutDetaching([$employeeRoleSanctum->id, $employeeRoleWeb->id]);

        // Create random employees under supervisors (2 per supervisor = 4 total)
        foreach ($supervisors as $supervisor) {
            User::factory(2)->create([
                'password' => $defaultPassword,
                'direct_supervisor_id' => $supervisor->id,
                'immediate_manager_id' => $supervisor->immediate_manager_id,
                'location_id' => $supervisor->location_id,
            ])->each(function ($user) use ($employeeRoleSanctum, $employeeRoleWeb) {
                $user->roles()->syncWithoutDetaching([$employeeRoleSanctum->id, $employeeRoleWeb->id]);
            });
        }
    }
}
