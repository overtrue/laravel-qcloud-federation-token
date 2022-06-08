<?php

namespace Overtrue\LaravelQcloudFederationToken;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Overtrue\LaravelQcloudFederationToken\Contracts\StrategyInterface strategy(string $name)
 */
class FederationToken extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Manager::class;
    }
}
