<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Load countries data from JSON file
        $countriesJson = file_get_contents(database_path('countries.json'));
        $data = json_decode($countriesJson, true);
        $countries = $data['countries'];

        // Prepare the data for seeding
        $seedData = [];
        foreach ($countries as $country) {
            $seedData[] = [
                'country_name' => $country['country'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Seed the countries table
        DB::table('countries')->insert($seedData);
    }
}






