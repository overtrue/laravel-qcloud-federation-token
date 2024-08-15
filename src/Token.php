<?php

namespace Overtrue\LaravelQcloudFederationToken;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JetBrains\PhpStorm\ArrayShape;

use function json_encode;

class Token implements Arrayable, Jsonable
{
    public function __construct(
        public Credentials $credentials,
        public int $expiredAt,
        public string $expiration,
        public string $requestId,
        public array $statements = [],
    ) {}

    public function getCredentials(): Credentials
    {
        return $this->credentials;
    }

    public function getExpiredAt(): int
    {
        return $this->expiredAt;
    }

    public function getExpiration(): string
    {
        return $this->expiration;
    }

    public function getRequestId(): string
    {
        return $this->requestId;
    }

    public function getStatements(): array
    {
        return $this->statements;
    }

    #[ArrayShape(['credentials' => 'mixed', 'expired_at' => 'int', 'expiration' => 'string'])]
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
        return json_encode($this->toArray(), $options);
    }
}
