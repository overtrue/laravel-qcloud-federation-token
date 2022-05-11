<?php

namespace Overtrue\LaravelQcloudFederationToken;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @see https://cloud.tencent.com/document/api/1312/48198#Credentials
 */
class Credentials implements Arrayable, Jsonable
{
    public function __construct(
        public string $token,
        public string $tmpSecretId,
        public string $tmpSecretKey,
    ) {
    }

    #[ArrayShape(['token' => "string", 'tmp_secret_id' => "string", 'tmp_secret_key' => "string"])]
    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'tmp_secret_id' => $this->tmpSecretId,
            'tmp_secret_key' => $this->tmpSecretKey,
        ];
    }

    public function toJson($options = 0): bool|string
    {
        return \json_encode($this->toArray(), $options);
    }
}
