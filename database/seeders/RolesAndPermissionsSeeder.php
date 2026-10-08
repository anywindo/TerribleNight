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

        // Create Permissions
        Permission::firstOrCreate(['name' => 'manage-rbac', 'guard_name' => 'sanctum']);
        Permission::firstOrCreate(['name' => 'manage-rbac', 'guard_name' => 'web']);

        // Create Roles for Sanctum
        $superAdminSanctum = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'sanctum']);
        Role::firstOrCreate(['name' => 'HR', 'guard_name' => 'sanctum']);
        Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'sanctum']);

        // Create Roles for Web
        $superAdminWeb = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'HR', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'web']);
        
        $superAdminSanctum->givePermissionTo('manage-rbac');
        $superAdminWeb->givePermissionTo('manage-rbac');
    }
}
