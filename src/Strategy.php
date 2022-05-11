<?php

namespace Overtrue\LaravelQcloudFederationToken;

use Illuminate\Config\Repository;
use JetBrains\PhpStorm\Pure;

/**
 * @see https://cloud.tencent.com/document/product/598/10603
 */
class Strategy
{
    protected Repository $config;

    #[Pure]
    public function __construct(?array $config = null)
    {
        if ($config) {
            $this->config = new Repository($config);
        }
    }

    public function getSecretId()
    {
        return $this->config->get('secret_id');
    }

    public function getSecretKey()
    {
        return $this->config->get('secret_key');
    }

    public function getEndpoint()
    {
        return $this->config->get('endpoint');
    }

    public function getRegion()
    {
        return $this->config->get('region');
    }

    public function getEffect(): string
    {
        return $this->config->get('effect', 'allow');
    }

    public function getActions(): array
    {
        return $this->config->get('actions', ['cos:PutObject']);
    }

    public function getConditions(): array
    {
        return $this->config->get('conditions', []);
    }

    public function getResources(): array
    {
        return $this->config->get('resources', []);
    }

    public function getExpiresIn()
    {
        return $this->config->get('expires_in', 1800);
    }

    /**
     * @throws Exceptions\HttpException
     */
    public function build(): Token
    {
        $builder = new Builder($this->getSecretId(), $this->getSecretKey(), $this->getRegion(), $this->getEndpoint());

        if (!empty($this->getActions())) {
            $builder->actions($this->getActions());
        }

        if (!empty($this->getResources())) {
            $builder->resources($this->getResources());
        }

        if (!empty($this->getConditions())) {
            $builder->conditions($this->getConditions());
        }

        if ($this->getExpiresIn() !== null) {
            $builder->expiresIn($this->getExpiresIn());
        }

        return $builder->build();
    }
}