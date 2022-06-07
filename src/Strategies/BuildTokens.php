<?php

namespace Overtrue\LaravelQcloudFederationToken\Strategies;

use Overtrue\LaravelQcloudFederationToken\Builder;
use Overtrue\LaravelQcloudFederationToken\Token;

trait BuildTokens
{
    public function build(): Token
    {
        return $this->getBuilder()->build($this->getStatements());
    }

    public function getBuilder(): Builder
    {
        return new Builder($this->getSecretId(), $this->getSecretKey(), $this->getRegion(), $this->getEndpoint());
    }
}
