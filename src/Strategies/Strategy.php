<?php

namespace Overtrue\LaravelQcloudFederationToken\Strategies;

use Illuminate\Config\Repository;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Overtrue\LaravelQcloudFederationToken\Contracts\StrategyInterface;
use Overtrue\LaravelQcloudFederationToken\Statement;

/**
 * @see https://cloud.tencent.com/document/product/598/10603
 */
class Strategy implements StrategyInterface
{
    use BuildTokens;

    protected Repository $config;

    #[Pure]
    public function __construct(?array $config = null)
    {
        if ($config) {
            $this->config = new Repository($config);
        }
    }

    public function getSecretId(): string
    {
        return $this->config->get('secret_id');
    }

    public function getSecretKey(): string
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
     * @throws \Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidArgumentException
     */
    #[ArrayShape([['principal' => "array", 'effect' => "string", 'action' => "array", 'resource' => "array", 'condition' => "array"]])]
    public function getStatements(): array
    {
        $statement = new Statement($this->getVariables());

        $statement->effect($this->getEffect());

        if (!empty($this->getPrincipal())) {
            $statement->principal($this->getPrincipal());
        }

        if (!empty($this->getActions())) {
            $statement->actions($this->getActions());
        }

        if (!empty($this->getResources())) {
            $statement->resources($this->getResources());
        }

        if (!empty($this->getConditions())) {
            $statement->conditions($this->getConditions());
        }

        return [array_filter($statement->toArray())];
    }
}
