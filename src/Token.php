<?php

namespace Overtrue\LaravelQcloudFederationToken;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JetBrains\PhpStorm\ArrayShape;

class Token implements Arrayable, Jsonable
{
    public function __construct(
        public Credentials $credentials,
        public int $expiredAt,
        public string $expiration,
        public string $requestId,
    ) {
    }

    #[ArrayShape(['credentials' => "mixed", 'expired_at' => "int", 'expiration' => "string"])]
    public function toArray(): array
    {
        return [
            'credentials' => $this->credentials->toArray(),
            'expired_at' => $this->expiredAt,
            'expiration' => $this->expiration,
        ];
    }

    public function toJson($options = 0): bool|string
    {
        return \json_encode($this->toArray(), $options);
    }
}
