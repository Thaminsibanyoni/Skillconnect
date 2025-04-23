<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar; // Import PermissionRegistrar

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles if they don't exist
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'provider', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'seeker', 'guard_name' => 'web']);

        // Create Permissions (example - add more as needed)
        // Permission::firstOrCreate(['name' => 'manage users', 'guard_name' => 'web']);
        // Permission::firstOrCreate(['name' => 'manage services', 'guard_name' => 'web']);
        // Permission::firstOrCreate(['name' => 'manage orders', 'guard_name' => 'web']);
        // Permission::firstOrCreate(['name' => 'access admin panel', 'guard_name' => 'web']);

        // Assign Permissions to Roles (example)
        // $adminRole = Role::findByName('admin', 'web');
        // $adminRole->givePermissionTo(Permission::all()); // Give all permissions to admin

        // $providerRole = Role::findByName('provider', 'web');
        // $providerRole->givePermissionTo(['manage orders']); // Example

        // $seekerRole = Role::findByName('seeker', 'web');
        // $seekerRole->givePermissionTo([]); // Example

        // Assign the 'admin' role to the first user created (usually during seeding or manually)
        // $user = \App\Models\User::first();
        // if ($user) {
        //     $user->assignRole('admin');
        // }
    }
}
