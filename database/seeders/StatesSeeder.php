<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatesSeeder extends Seeder
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
            $countryId = DB::table('countries')->where('country_name', $country['country'])->value('id');
            $states = $country['states'];
            foreach ($states as $state) {
                $seedData[] = [
                    'country_id' => $countryId,
                    'state_name' => $state,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Seed the states table
        DB::table('states')->insert($seedData);
    }

    /**
     * Generate a state code based on the state name
     *
     * @param string $stateName
     * @return string
     */
    private function generateStateCode($stateName)
    {
        // Convert the state name to uppercase and remove any non-alphanumeric characters
        $stateCode = preg_replace('/[^a-zA-Z0-9]/', '', strtoupper($stateName));

        // Truncate the state code to a maximum of 3 characters
        $stateCode = substr($stateCode, 0, 3);

        return $stateCode;
    }
}
