<?php

namespace Overtrue\LaravelQcloudFederationToken;

class Token
{
    public function __construct(
        public Credentials $credentials,
        public int $expiredAt,
        public string $expiration,
        public string $requestId,
    ) {
    }
}
