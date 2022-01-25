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
}
