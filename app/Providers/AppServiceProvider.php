<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        Passport::ignoreRoutes();


        if(url('/') == 'http://localhost/GSpark'){
            $apiUrl = 'http://localhost/credit-pass/api/';
        }
        else {
            $apiUrl = 'https://credit-pass.suhaani.co.in/api/';
        }

        // Bind the global variable into the service container
        $this->app->singleton('api_url', function () use ($apiUrl) {
            return $apiUrl;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
