<?php

namespace Overtrue\LaravelQcloudFederationToken;

use Carbon\Carbon;
use Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidArgumentException;

class Builder
{
    protected string $name;
    protected array $actions;
    protected array $resources;
    protected array $conditions;
    protected int $expiresIn = 1800;
    protected string $effect = 'allow';

    public const MAX_EXPIRES_IN = 7200;

    public function __construct(?string $name = null, ?Strategy $strategy = null)
    {
        $this->name = $name ?? \config('app.name');

        if ($strategy) {
            $this->applyStrategy($strategy);
        }
    }

    public function expiresIn(int $expiresIn): static
    {
        $this->expiresIn = \min($expiresIn, static::MAX_EXPIRES_IN);

        return $this;
    }

    /**
     * @throws \Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidArgumentException
     */
    public function expiredAt(int|string|Carbon $expiredAt): static
    {
        match (true) {
            \is_int($expiredAt) && $expiredAt <= static::MAX_EXPIRES_IN => $this->expiresIn($expiredAt),
            \is_string($expiredAt) => $this->expiresIn(Carbon::parse($expiredAt)->timestamp - \time()),
            \is_object($expiredAt) => $this->expiresIn($expiredAt->timestamp - \time()),
            default => throw new InvalidArgumentException('Unexpected $expiredAt value.'),
        };

        return $this;
    }

    public function withName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function allow(): static
    {
        $this->effect = 'allow';

        return $this;
    }

    public function deny(): static
    {
        $this->effect = 'deny';

        return $this;
    }

    public function actions(array $actions): static
    {
        $this->actions += $actions;

        return $this;
    }

    public function resources(string|array $resource): static
    {
        $this->resources += \is_array($resource) ? $resource : [$resource];

        return $this;
    }

    public function conditions(string|array $conditions): static
    {
        $this->conditions += \is_array($conditions) ? $conditions : [$conditions];

        return $this;
    }

    public function build(): Token
    {
        //return new Token();
    }

    // todo:
    protected function applyStrategy(Strategy $strategy): void
    {
        if (!empty($strategy->getActions())) {
            $this->actions($strategy->getActions());
        }

        if (!empty($strategy->getResources())) {
            $this->resources($strategy->getResources());
        }

        if (!empty($strategy->getConditions())) {
            $this->conditions($strategy->getConditions());
        }

        if ($strategy->getExpiresIn() !== null) {
            $this->expiresIn($strategy->getExpiresIn());
        }
    }
}
