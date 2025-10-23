<?php

namespace Overtrue\LaravelQcloudFederationToken\Strategies;

use Illuminate\Config\Repository;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Overtrue\LaravelQcloudFederationToken\Contracts\StrategyInterface;
use Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidConfigException;
use Overtrue\LaravelQcloudFederationToken\Statement;

/**
 * @see https://cloud.tencent.com/document/product/598/10603
 */
class Strategy implements StrategyInterface
{
    use BuildTokens;

    protected Repository $config;

    #[Pure]
    public function __construct(protected string $name, array $config)
    {
        $this->config = new Repository($config);
    }

    public function get(string $key)
    {
        return $this->config->get($key);
    }

    public function getHeaders()
    {
        return $this->config->get('headers', [])
    }

    public function getName()
    {
        return $this->config->get('name', $this->name);
    }

    public function getSecretId(): string
    {
        return $this->config->get('secret_id');
    }

    public function getSecretKey(): string
    {
        return $this->config->get('secret_key');
    }

    public function getEndpoint(): ?string
    {
        return $this->config->get('endpoint');
    }

    public function getRegion(): ?string
    {
        return $this->config->get('region');
    }

    public function getResources(): array
    {
        return $this->config->get('resource', []);
    }

    public function getExpiresIn(): int
    {
        return $this->config->get('expires_in', $this->config->get('duration_seconds', 1800));
    }

    public function getVariables(): array
    {
        return $this->config->get('variables', []);
    }

    /**
     * @throws \Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidConfigException
     * @throws \Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidArgumentException
     */
    #[ArrayShape([Statement::class])]
    public function getStatements(): array
    {
        $statements = $this->config->get('statements');

        if (empty($statements)) {
            throw new InvalidConfigException('No statements found.');
        }

        $variables = array_merge([
            'region' => $this->getRegion(),
        ], $this->getVariables());

        return array_map(fn ($config) => (new Statement($config))->setVariables($variables)->toArray(), $statements);
    }
}
