<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles for Sanctum
        Role::firstOrCreate(['name' => 'HR', 'guard_name' => 'sanctum']);
        Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'sanctum']);

        // Create Roles for Web
        Role::firstOrCreate(['name' => 'HR', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'web']);
        
        // (Optional) define specific permissions here if needed later
    }
}
