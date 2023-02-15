<?php

namespace Overtrue\LaravelQcloudFederationToken\Contracts;

use JetBrains\PhpStorm\ArrayShape;
use Overtrue\LaravelQcloudFederationToken\Token;

interface StrategyInterface
{
    public function getSecretId(): string;

    public function getSecretKey(): string;

    public function getRegion(): ?string;

    public function getEndpoint(): ?string;

    #[ArrayShape([['principal' => 'array', 'effect' => 'string', 'action' => 'array', 'resource' => 'array', 'condition' => 'array']])]
    public function getStatements(): array;

    public function getExpiresIn(): int;

    /**
     * @return array<string, string>
     */
    public function getVariables(): array;

    public function createToken(): Token;
}
