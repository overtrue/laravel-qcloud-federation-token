<?php

namespace Overtrue\LaravelCosFederationToken;

class Token
{
    public function __construct(
        public Credentials $credentials,
        public int $expiredAt,
        public string $requestId,
    ) {
    }
}
