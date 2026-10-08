<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create an HR Admin
        $hr = User::factory()->create([
            'name' => 'HR Admin',
            'email' => 'hr@example.com',
            'nik' => 'HR-00001',
            'password' => bcrypt('password'),
            'location_id' => \App\Models\Location::first()->id ?? null,
        ]);
        $hrRoleSanctum = \Spatie\Permission\Models\Role::findByName('HR', 'sanctum');
        $hrRoleWeb = \Spatie\Permission\Models\Role::findByName('HR', 'web');
        $hr->roles()->syncWithoutDetaching([$hrRoleSanctum->id, $hrRoleWeb->id]);

        // Create Managers
        $employeeRoleSanctum = \Spatie\Permission\Models\Role::findByName('Employee', 'sanctum');
        $employeeRoleWeb = \Spatie\Permission\Models\Role::findByName('Employee', 'web');

        $managers = User::factory(3)->create([
            'location_id' => \App\Models\Location::first()->id ?? null,
        ])->each(function ($user) use ($employeeRoleSanctum, $employeeRoleWeb) {
            $user->roles()->syncWithoutDetaching([$employeeRoleSanctum->id, $employeeRoleWeb->id]);
        });

        // Create Supervisors
        $supervisors = collect();
        foreach ($managers as $manager) {
            $createdSupervisors = User::factory(2)->create([
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
            'nik' => 'EMP-00001',
            'password' => bcrypt('password'),
            'direct_supervisor_id' => $supervisors->first()->id,
            'immediate_manager_id' => $managers->first()->id,
            'location_id' => \App\Models\Location::first()->id ?? null,
        ]);
        $employee->roles()->syncWithoutDetaching([$employeeRoleSanctum->id, $employeeRoleWeb->id]);

        // Create some random employees under supervisors
        foreach ($supervisors as $supervisor) {
            User::factory(5)->create([
                'direct_supervisor_id' => $supervisor->id,
                'immediate_manager_id' => $supervisor->immediate_manager_id,
                'location_id' => $supervisor->location_id,
            ])->each(function ($user) use ($employeeRoleSanctum, $employeeRoleWeb) {
                $user->roles()->syncWithoutDetaching([$employeeRoleSanctum->id, $employeeRoleWeb->id]);
            });
        }
    }
}
