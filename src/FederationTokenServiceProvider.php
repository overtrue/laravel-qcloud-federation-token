<?php

namespace Overtrue\LaravelCosFederationToken;

use Illuminate\Support\ServiceProvider;

class FederationTokenServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(\dirname(__DIR__) . '/config/cos-federation-token.php', 'cos-federation-token');
    }

    public function register()
    {
        $this->app->singleton(Manager::class, function () {
            return new Manager(\config('cos-federation-token'));
        });
    }
}
