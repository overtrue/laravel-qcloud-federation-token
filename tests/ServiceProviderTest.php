<?php

namespace Tests;

use Overtrue\LaravelQcloudFederationToken\Manager;

class ServiceProviderTest extends TestCase
{
    public function test_manager_is_scoped_in_the_container(): void
    {
        $first = $this->app->make(Manager::class);
        $second = $this->app->make(Manager::class);

        $this->assertSame($first, $second);

        $this->app->forgetScopedInstances();

        $third = $this->app->make(Manager::class);

        $this->assertNotSame($first, $third);
    }
}
