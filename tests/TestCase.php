<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Overtrue\LaravelQcloudFederationToken\QCloudFederationTokenServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Load package service provider.
     *
     * @param  Application  $app
     *
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [QCloudFederationTokenServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
