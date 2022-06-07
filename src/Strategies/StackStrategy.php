<?php

namespace Overtrue\LaravelQcloudFederationToken\Strategies;

use Illuminate\Config\Repository;
use Overtrue\LaravelQcloudFederationToken\Contracts\StrategyInterface;
use Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidConfigException;

class StackStrategy implements StrategyInterface
{
    use BuildTokens;

    protected Repository $config;

    public function __construct(protected array $strategies, ?array $config = null)
    {
        if ($config) {
            $this->config = new Repository($config);
        }
    }

    /**
     * @throws \Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidConfigException
     */
    public function getSecretId(): string
    {
        return $this->config->get('secret_id') ?? $this->getDefaultStrategy()->getSecretId();
    }

    /**
     * @throws \Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidConfigException
     */
    public function getSecretKey(): string
    {
        return $this->config->get('secret_key') ?? $this->getDefaultStrategy()->getSecretKey();
    }

    /**
     * @throws \Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidConfigException
     */
    public function getDefaultStrategy(): StrategyInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy instanceof StrategyInterface) {
                return $strategy;
            }
        }

        throw new InvalidConfigException('Invalid stack strategy config, no available strategy found.');
    }

    public function getStatements(): array
    {
        $statements = [];

        foreach ($this->strategies as $strategy) {
            if ($strategy instanceof StrategyInterface) {
                $statements = array_merge($statements, $strategy->getStatements());
            }
        }

        return $statements;
    }
}
