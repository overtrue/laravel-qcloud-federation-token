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

    public function getPrincipal()
    {
        return $this->config->get('principal');
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
        return $this->config->get('action', []);
    }

    public function getConditions(): array
    {
        return $this->config->get('condition', []);
    }

    public function getResources(): array
    {
        return $this->config->get('resource', []);
    }

    public function getExpiresIn()
    {
        return $this->config->get('expires_in', 1800);
    }

    public function getVariables()
    {
        return $this->config->get('variables', []);
    }

    /**
     * @throws Exceptions\HttpException
     */
    public function build(): Token
    {
        return $this->getBuilder()->build();
    }

    public function getBuilder(): Builder
    {
        $builder = new Builder($this->getSecretId(), $this->getSecretKey(), $this->getRegion(), $this->getEndpoint(), $this->getVariables());

        if (!empty($this->getPrincipal())) {
            $builder->principal($this->getPrincipal());
        }

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

        return $builder;
    }
}
