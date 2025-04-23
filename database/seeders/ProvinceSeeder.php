<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Prevent duplicates if seeder runs multiple times
        DB::table('provinces')->delete();

        $provinces = [
            ['name' => 'Eastern Cape'],
            ['name' => 'Free State'],
            ['name' => 'Gauteng'],
            ['name' => 'KwaZulu-Natal'],
            ['name' => 'Limpopo'],
            ['name' => 'Mpumalanga'],
            ['name' => 'Northern Cape'],
            ['name' => 'North West'],
            ['name' => 'Western Cape'],
        ];

        DB::table('provinces')->insert($provinces);
    }
}
