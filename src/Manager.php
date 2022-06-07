<?php

namespace Overtrue\LaravelQcloudFederationToken;

use Closure;
use Illuminate\Config\Repository;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;
use Overtrue\LaravelQcloudFederationToken\Contracts\StrategyInterface;
use Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidArgumentException;
use Overtrue\LaravelQcloudFederationToken\Strategies\StackStrategy;
use Overtrue\LaravelQcloudFederationToken\Strategies\Strategy;

use function array_key_first;

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
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
     */
    protected function createStrategy($strategy): StrategyInterface
    {
        $strategyConfig = $this->config->get("strategies.{$strategy}");

        if (isset($this->customCreators[$strategy])) {
            return $this->callCustomCreator($strategy);
        } elseif (array_key_exists('strategies', $strategyConfig)) {
            return $this->createStackStrategy($strategyConfig);
        } elseif (\array_key_exists($strategy, $this->config->get('strategies'))) {
            $method = 'create'.Str::studly($strategy).'Strategy';

            if (method_exists($this, $method)) {
                return $this->$method();
            }

            return new Strategy($this->getStrategyConfig($strategy));
        }

        throw new InvalidArgumentException("Strategy [$strategy] not supported.");
    }

    /**
     * @throws \Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidArgumentException
     */
    protected function createStackStrategy($config): StrategyInterface
    {
        $strategies = [];

        foreach ($config['strategies'] ?? [] as $name) {
            $strategies[] = $this->strategy($name);
        }

        return new StackStrategy($strategies, $config);
    }

    protected function getStrategyConfig(string $strategyName): array
    {
        $variables = array_merge(
            $this->config->get('default.variables', []),
            $this->config->get("strategies.{$strategyName}.variables", [])
        );

        return \array_merge(
            $this->config->get('default', []),
            $this->config->get("strategies.$strategyName", []),
            compact('variables')
        );
    }

    protected function callCustomCreator(string $strategy): StrategyInterface
    {
        return $this->customCreators[$strategy]($this->config->get("strategies.{$strategy}"));
    }

    public function extend($strategy, Closure $callback): static
    {
        $this->customCreators[$strategy] = $callback;

        return $this;
    }

    /**
     * @return array<StrategyInterface>
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
     * @throws InvalidArgumentException
     */
    public function __call($method, $parameters)
    {
        return $this->strategy()->$method(...$parameters);
    }

    protected function getDefaultStrategy(): ?string
    {
        return array_key_first($this->config->get('strategies'));
    }
}
