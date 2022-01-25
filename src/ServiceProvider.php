<?php

namespace Overtrue\LaravelQcloudFederationToken;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/qcloud-federation-token.php' => config_path('qcloud-federation-token.php'),
        ], 'config');

        $this->mergeConfigFrom(\dirname(__DIR__) . '/config/qcloud-federation-token.php', 'qcloud-federation-token');
    }

    public function register()
    {
        $this->app->singleton(Manager::class, function () {
            return new Manager(\config('qcloud-federation-token'));
        });
    }
}
