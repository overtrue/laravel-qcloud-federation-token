<?php

namespace Overtrue\LaravelQcloudFederationToken\Strategies;

use Illuminate\Config\Repository;
use JetBrains\PhpStorm\ArrayShape;
use Overtrue\LaravelQcloudFederationToken\Contracts\StrategyInterface;
use Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidConfigException;
use Overtrue\LaravelQcloudFederationToken\Statement;

class StackStrategy implements StrategyInterface
{
    use BuildTokens;

    protected Repository $config;

    public function __construct(protected array $strategies, protected string $name, array $config)
    {
        $this->config = new Repository($config);
    }

    public function getName()
    {
        return $this->config->get('name', $this->name);
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
    public function getRegion(): ?string
    {
        return $this->config->get('region') ?? $this->getDefaultStrategy()->getRegion();
    }

    /**
     * @throws \Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidConfigException
     */
    public function getEndpoint(): ?string
    {
        return $this->config->get('endpoint') ?? $this->getDefaultStrategy()->getEndpoint();
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
    public function getExpiresIn(): int
    {
        return $this->config->get('expires_in', $this->config->get('duration_seconds', 1800)) ?? $this->getDefaultStrategy()->getExpiresIn();
    }

    /**
     * @throws \Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidConfigException
     */
    public function getVariables(): array
    {
        return $this->config->get('variables', []) ?? $this->getDefaultStrategy()->getVariables();
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

    #[ArrayShape([Statement::class])]
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
