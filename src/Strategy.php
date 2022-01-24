<?php

namespace Overtrue\LaravelCosFederationToken;

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

    public function getPrincipals(): array
    {
        return $this->config->get('principals', []);
    }

    public function getEffect()
    {
        return $this->config->get('effect', 'allow');
    }

    public function getActions()
    {
        return $this->config->get('actions', 'cos:PutObject');
    }

    public function getConditions()
    {
        return $this->config->get('conditions', null);
    }

    public function getResources()
    {
        return $this->config->get('resources', null);
    }
}
