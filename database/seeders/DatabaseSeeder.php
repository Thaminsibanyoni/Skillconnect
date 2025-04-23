<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Console\Seeds\WithoutModelEvents; // Use this trait
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents; // Use this trait to disable model events during seeding

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call specific seeders in order
        $this->call([
            ProvinceSeeder::class,
            CitySeeder::class,
            RolesAndPermissionsSeeder::class, // Call the roles seeder
            // Add other seeders here if needed
        ]);

        // Optionally create a default admin user or test users
        // \App\Models\User::factory()->create([
        //     'name' => 'Admin User',
        //     'email' => 'admin@example.com',
        //     'role' => 'admin', // Still set the old role column if needed elsewhere
        //     'status' => 'approved',
        // ])->assignRole('admin'); // Assign Spatie role
    }
}
