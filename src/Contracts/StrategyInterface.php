<?php

namespace Overtrue\LaravelQcloudFederationToken\Contracts;

use Overtrue\LaravelQcloudFederationToken\Token;

interface StrategyInterface
{
    public function getSecretId(): string;
    public function getSecretKey(): string;
    public function getStatements(): array;
    public function build(): Token;
}
