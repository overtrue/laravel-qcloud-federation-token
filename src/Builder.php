<?php

namespace Overtrue\LaravelCosFederationToken;

class Builder
{
    protected string $effect;
    protected array $principals;
    protected array $actions;
    protected array $resources;
    protected array $conditions;

    public function build(): Token
    {
        //return new Token();
    }
}
