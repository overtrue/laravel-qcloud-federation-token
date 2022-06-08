<?php

namespace Overtrue\LaravelQcloudFederationToken\Strategies;

use Overtrue\LaravelQcloudFederationToken\Builder;
use Overtrue\LaravelQcloudFederationToken\Token;

trait BuildTokens
{
    /**
     * @throws \Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidConfigException
     */
    public function createToken(): Token
    {
        return $this->getBuilder()->build($this->getStatements(), $this->getExpiresIn(), $this->getName());
    }

    /**
     * @throws \Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidConfigException
     */
    public function getBuilder(): Builder
    {
        return new Builder($this->getSecretId(), $this->getSecretKey(), $this->getRegion(), $this->getEndpoint());
    }
}
