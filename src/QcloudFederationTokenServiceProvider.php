<?php

namespace Overtrue\LaravelQcloudFederationToken;

use function config;
use function dirname;
use Illuminate\Support\ServiceProvider;

class QcloudFederationTokenServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            dirname(__DIR__).'/config/federation-token.php' => config_path('federation-token.php'),
        ], 'config');

        $this->mergeConfigFrom(dirname(__DIR__).'/config/federation-token.php', 'federation-token');
    }

    public function register()
    {
        $this->app->singleton(Manager::class, function () {
            return new Manager(config('federation-token'));
        });
    }
}
