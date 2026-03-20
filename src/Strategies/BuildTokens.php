<?php

namespace Overtrue\LaravelQcloudFederationToken\Strategies;

use Overtrue\LaravelQcloudFederationToken\Builder;
use Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidConfigException;
use Overtrue\LaravelQcloudFederationToken\Token;

trait BuildTokens
{
    /**
     * @throws InvalidConfigException
     */
    public function createToken(): Token
    {
        return $this->getBuilder()->build($this->getStatements(), $this->getExpiresIn(), $this->getName());
    }

    /**
     * @throws InvalidConfigException
     */
    public function getBuilder(): Builder
    {
        return new Builder($this->getSecretId(), $this->getSecretKey(), $this->getRegion(), $this->getEndpoint());
    }
}
