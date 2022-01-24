<?php

namespace Overtrue\LaravelCosFederationToken;

/**
 * @see https://cloud.tencent.com/document/api/1312/48198#Credentials
 */
class Credentials
{
    public function __construct(
        public string $token,
        public string $tmpSecretId,
        public string $tmpSecretKey,
    ) {
    }
}
