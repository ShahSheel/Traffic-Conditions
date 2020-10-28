<?php

namespace Sheel\here_traffic\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Sheel\SportRadarTennis\Factory\TennisAPIFactory;

class TrafficServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->loadMigrationsFrom(__DIR__ . '/migrations');

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('TennisAPIFacade', function () {
            return TennisAPIFactory::create(
                env( 'TENNIS_API_KEY' )
            );
        });

    }
}
