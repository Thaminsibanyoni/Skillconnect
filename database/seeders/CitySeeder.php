<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Province; // Import Province model

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing cities first
        DB::table('cities')->delete();

        // Fetch province IDs - ensure ProvinceSeeder runs first
        $provinces = Province::pluck('id', 'name');

        // Check if provinces were found
        if ($provinces->isEmpty()) {
            $this->command->error('Provinces not found. Please run ProvinceSeeder first.');
            return;
        }

        $cities = [
            // Gauteng
            ['name' => 'Johannesburg', 'province_id' => $provinces->get('Gauteng')],
            ['name' => 'Pretoria', 'province_id' => $provinces->get('Gauteng')],
            ['name' => 'Sandton', 'province_id' => $provinces->get('Gauteng')],
            ['name' => 'Soweto', 'province_id' => $provinces->get('Gauteng')],
            ['name' => 'Midrand', 'province_id' => $provinces->get('Gauteng')],
            // Western Cape
            ['name' => 'Cape Town', 'province_id' => $provinces->get('Western Cape')],
            ['name' => 'Stellenbosch', 'province_id' => $provinces->get('Western Cape')],
            ['name' => 'George', 'province_id' => $provinces->get('Western Cape')],
            ['name' => 'Paarl', 'province_id' => $provinces->get('Western Cape')],
            // KwaZulu-Natal
            ['name' => 'Durban', 'province_id' => $provinces->get('KwaZulu-Natal')],
            ['name' => 'Pietermaritzburg', 'province_id' => $provinces->get('KwaZulu-Natal')],
            ['name' => 'Richards Bay', 'province_id' => $provinces->get('KwaZulu-Natal')],
            // Eastern Cape
            ['name' => 'Gqeberha (Port Elizabeth)', 'province_id' => $provinces->get('Eastern Cape')],
            ['name' => 'East London', 'province_id' => $provinces->get('Eastern Cape')],
            // Free State
            ['name' => 'Bloemfontein', 'province_id' => $provinces->get('Free State')],
            // Limpopo
            ['name' => 'Polokwane', 'province_id' => $provinces->get('Limpopo')],
            // Mpumalanga
            ['name' => 'Mbombela (Nelspruit)', 'province_id' => $provinces->get('Mpumalanga')],
            // Northern Cape
            ['name' => 'Kimberley', 'province_id' => $provinces->get('Northern Cape')],
            // North West
            ['name' => 'Rustenburg', 'province_id' => $provinces->get('North West')],
            ['name' => 'Mahikeng (Mafikeng)', 'province_id' => $provinces->get('North West')],
            // Add more cities as needed...
        ];

        // Filter out any entries where province_id might be null if a province name was misspelled
        $validCities = array_filter($cities, fn($city) => !is_null($city['province_id']));

        DB::table('cities')->insert($validCities);
    }
}
