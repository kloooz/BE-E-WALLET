<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Limit PHP socket timeout so SMTP connection failures throw a catchable
        // Exception instead of a PHP Fatal Error (max_execution_time exceeded).
        ini_set('default_socket_timeout', 10);
    }
}
