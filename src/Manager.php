<?php

namespace Overtrue\LaravelQcloudFederationToken;

use Closure;
use Illuminate\Config\Repository;
use Illuminate\Support\Arr;
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
    protected function createStrategy($name): StrategyInterface
    {
        $strategyConfig = $this->getStrategyConfig($name);

        if (isset($this->customCreators[$name])) {
            return $this->callCustomCreator($name);
        } elseif (array_key_exists('strategies', $strategyConfig)) {
            return $this->createStackStrategy($name, $strategyConfig);
        } elseif (\array_key_exists($name, $this->config->get('strategies'))) {
            $method = 'create'.Str::studly($name).'Strategy';

            if (method_exists($this, $method)) {
                return $this->$method();
            }

            return new Strategy($name, $strategyConfig);
        }

        throw new InvalidArgumentException("Strategy [$name] not supported.");
    }

    /**
     * @throws \Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidArgumentException
     */
    protected function createStackStrategy(string $strategyName, array $config): StrategyInterface
    {
        $strategies = [];

        foreach ($config['strategies'] ?? [] as $name) {
            $strategies[] = $this->strategy($name);
        }

        return new StackStrategy($strategies, $strategyName, $config);
    }

    protected function getStrategyConfig(string $strategyName): array
    {
        $defaultConfig = $this->config->get('default');
        $strategyConfig = array_merge($defaultConfig, $this->config->get("strategies.$strategyName", []));

        $strategyConfig['variables'] = array_merge(
            $this->config->get('default.variables', []),
            $this->config->get("strategies.{$strategyName}.variables", [])
        );

        $defaultStatement = Arr::only($defaultConfig, ['action', 'effect', 'principal', 'resource', 'condition']);

        $strategyConfig['statements'] = array_map(fn ($statement) => array_merge($defaultStatement, $statement), $strategyConfig['statements'] ?? []);

        return $strategyConfig;
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
