<?php

namespace Overtrue\LaravelCosFederationToken\Events;

use Overtrue\LaravelCosFederationToken\Token;

class TokenCreated
{
    public function __construct(public Token $token)
    {
    }
}
