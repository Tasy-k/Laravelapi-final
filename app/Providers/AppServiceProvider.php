<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Queue;

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
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Queue::after(function ($connection, $job, $data) {
        //     //
        // });
        // //
        // Queue::failing(function ($connection, $job, $data) {
        //     // Notify team of failing job...
        // });
    }
}
