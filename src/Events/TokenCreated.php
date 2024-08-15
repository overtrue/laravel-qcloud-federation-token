<?php

namespace Overtrue\LaravelQcloudFederationToken\Events;

use Overtrue\LaravelQcloudFederationToken\Token;

class TokenCreated
{
    public function __construct(public Token $token) {}
}
