<?php

namespace Database\Seeders;

use App\Models\currencyBalance;
use Illuminate\Database\Seeder;
use Database\Seeders\CountriesSeeder;
use Database\Seeders\StatesSeeder;
use App\Models\Role;
use App\Models\User;
use App\Models\userWallet;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        // Role::create([
        //     'role' => 'admin',
        // ]);
        // Role::create([
        //     'role' => 'vendor',
        // ]);
        // Role::create([
        //     'role' => 'user',
        // ]);

        // $user = User::create([
        //     'name' => 'admin',
        //     'email' => 'admin@gmail.com',
        //     'password' => bcrypt('password'),
        // ]);
        // $user = User::where('name', 'admin')->first();
        // $role = Role::where('role', 'admin')->first();
        // $user->roles()->attach($role);


        // $wallet = userWallet::create([
        //     'user_id' => $user->id,
        //     'default_currency_id' => '2',
        //     'status' => 'active',
        // ]);

        // $currency = currencyBalance::create([
        //     'wallet_id' => $wallet->id,
        //     'currency_id' => $wallet->default_currency_id,
        //     'balance_amount' => 0
        // ]);


        $this->call(CountriesSeeder::class);
        $this->call(StatesSeeder::class);
    }
}
