<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Superadmin
        $superadmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'employee_id' => 'SA-00001',
            'nik' => '1111222233334444',
            'password' => bcrypt('password'),
            'location_id' => \App\Models\Location::first()->id ?? null,
        ]);
        $superAdminRoleSanctum = \Spatie\Permission\Models\Role::findByName('super-admin', 'sanctum');
        $superAdminRoleWeb = \Spatie\Permission\Models\Role::findByName('super-admin', 'web');
        $superadmin->roles()->syncWithoutDetaching([$superAdminRoleSanctum->id, $superAdminRoleWeb->id]);

        // Create an HR Admin
        $hr = User::factory()->create([
            'name' => 'HR Admin',
            'email' => 'hr@example.com',
            'employee_id' => 'HR-00001',
            'nik' => '5555666677778888',
            'password' => bcrypt('password'),
            'location_id' => \App\Models\Location::first()->id ?? null,
        ]);
        $hrRoleSanctum = \Spatie\Permission\Models\Role::findByName('HR', 'sanctum');
        $hrRoleWeb = \Spatie\Permission\Models\Role::findByName('HR', 'web');
        $hr->roles()->syncWithoutDetaching([$hrRoleSanctum->id, $hrRoleWeb->id]);

        // Create Managers
        $employeeRoleSanctum = \Spatie\Permission\Models\Role::findByName('Employee', 'sanctum');
        $employeeRoleWeb = \Spatie\Permission\Models\Role::findByName('Employee', 'web');

        $managers = User::factory(5)->create([
            'location_id' => \App\Models\Location::first()->id ?? null,
        ])->each(function ($user) use ($employeeRoleSanctum, $employeeRoleWeb) {
            $user->roles()->syncWithoutDetaching([$employeeRoleSanctum->id, $employeeRoleWeb->id]);
        });

        // Create Supervisors
        $supervisors = collect();
        foreach ($managers as $manager) {
            $createdSupervisors = User::factory(4)->create([
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
            'password' => bcrypt('password'),
            'direct_supervisor_id' => $supervisors->first()->id,
            'immediate_manager_id' => $managers->first()->id,
            'location_id' => \App\Models\Location::first()->id ?? null,
        ]);
        $employee->roles()->syncWithoutDetaching([$employeeRoleSanctum->id, $employeeRoleWeb->id]);

        // Create some random employees under supervisors
        foreach ($supervisors as $supervisor) {
            User::factory(15)->create([
                'direct_supervisor_id' => $supervisor->id,
                'immediate_manager_id' => $supervisor->immediate_manager_id,
                'location_id' => $supervisor->location_id,
            ])->each(function ($user) use ($employeeRoleSanctum, $employeeRoleWeb) {
                $user->roles()->syncWithoutDetaching([$employeeRoleSanctum->id, $employeeRoleWeb->id]);
            });
        }
    }
}
