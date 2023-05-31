<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;


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
        Schema::defaultStringLength(191);
        $this->app->bind('db.connector.mysql', function () {
            return new \Illuminate\Database\Connectors\MySqlConnector('utf8mb4');
        });
        
    }
}
