<?php

namespace Overtrue\LaravelQcloudFederationToken;

use Illuminate\Support\Facades\Facade;

class FederationToken extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Manager::class;
    }
}
