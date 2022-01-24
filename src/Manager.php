<?php

namespace Overtrue\LaravelCosFederationToken;

use Illuminate\Config\Repository;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;
use Overtrue\LaravelCosFederationToken\Exceptions\InvalidArgumentException;

class Manager
{
    protected Repository $config;
    protected array $strategies = [];
    protected array $customCreators;

    #[Pure]
    public function __construct(array $config)
    {
        $this->config = new Repository($config);
    }

    /**
     * @throws \Overtrue\LaravelCosFederationToken\Exceptions\InvalidArgumentException
     */
    public function strategy(string $strategy = null)
    {
        $strategy = $strategy ?: $this->getDefaultStrategy();

        if (is_null($strategy)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to resolve NULL strategy for [%s].',
                static::class
            ));
        }

        if (!isset($this->strategies[$strategy])) {
            $this->strategies[$strategy] = $this->createStrategy($strategy);
        }

        return $this->strategies[$strategy];
    }

    /**
     * @throws \Overtrue\LaravelCosFederationToken\Exceptions\InvalidArgumentException
     */
    protected function createStrategy($strategy): Strategy
    {
        if (isset($this->customCreators[$strategy])) {
            return $this->callCustomCreator($strategy);
        } else {
            $method = 'create'.Str::studly($strategy).'Strategy';

            if (method_exists($this, $method)) {
                return $this->$method();
            }
        }

        throw new InvalidArgumentException("Strategy [$strategy] not supported.");
    }

    protected function callCustomCreator(string $strategy): Strategy
    {
        return $this->customCreators[$strategy]($this->config->get("strategies.{$strategy}"));
    }

    public function extend($strategy, \Closure $callback): static
    {
        $this->customCreators[$strategy] = $callback;

        return $this;
    }

    /**
     * @return array<\Overtrue\LaravelCosFederationToken\Strategy>
     */
    public function getStrategies(): array
    {
        return $this->strategies;
    }

    public function forgetStrategies(): static
    {
        $this->strategies = [];

        return $this;
    }

    /**
     * @throws \Overtrue\LaravelCosFederationToken\Exceptions\InvalidArgumentException
     */
    public function __call($method, $parameters)
    {
        return $this->strategy()->$method(...$parameters);
    }

    protected function getDefaultStrategy(): ?string
    {
        return \array_key_first($this->config->get('strategies'));
    }
}
