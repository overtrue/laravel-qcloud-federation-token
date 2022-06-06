<?php

namespace Overtrue\LaravelQcloudFederationToken;

use Illuminate\Support\Facades\Facade;

/**
 * @method Strategy strategy(string $name)
 */
class FederationToken extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Manager::class;
    }
}
