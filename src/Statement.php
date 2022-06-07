<?php

namespace Overtrue\LaravelQcloudFederationToken;

use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

use Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidArgumentException;

use function json_encode;

class Statement
{
    protected array $principal = [];
    protected array $actions = [];
    protected array $resources = [];
    protected array $conditions = [];
    protected string $effect = 'allow';

    public function __construct(protected ?array $variables = [])
    {
    }

    public function withVariables(array $variables): static
    {
        $this->variables = $variables;

        return $this;
    }

    public function principal(array $principal): static
    {
        $this->principal = $principal;

        return $this;
    }

    /**
     * @throws \Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidArgumentException
     */
    public function effect(string $effect): static
    {
        if (!in_array($effect, ['allow', 'deny'])) {
            throw new InvalidArgumentException('Invalid effect value.');
        }

        $this->effect = $effect;

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
        $this->actions = $actions;

        return $this;
    }

    public function resources(array $resources): static
    {
        $this->resources = $resources;

        return $this;
    }

    public function conditions(array $conditions): static
    {
        $this->conditions = $conditions;

        return $this;
    }

    public function __toString(): string
    {
        return json_encode($this->toArray()) ?? '';
    }

    #[ArrayShape(['principal' => "array", 'effect' => "string", 'action' => "array", 'resource' => "array", 'condition' => "array"])]
    public function toArray(): array
    {
        $principal = $this->principal;

        if (!empty($principal)) {
            $principal['qcs'] = array_map([$this, 'replaceVariables'], $principal['qcs']);
        }

        $resources = array_map([$this, 'replaceVariables'], $this->resources);

        return [
            'principal' => $principal,
            'effect' => $this->effect,
            'action' => $this->actions,
            'resource' => $resources,
            'condition' => $this->conditions,
        ];
    }

    protected function replaceVariables(string $string): string
    {
        $variables = array_merge(...array_map(function ($key, $value) {
            return ['<'.$key.'>' => trim($value, '<>')];
        }, array_keys($this->variables), $this->variables));

        $replacements = array_merge([
            '<uuid>' => Str::uuid()->toString(),
            '<timestamp>' => \time(),
            '<random>' => Str::random(16),
            '<random:32>' => Str::random(32),
            '<date>' => \date('Ymd'),
            '<Ymd>' => \date('Ymd'),
            '<YmdHis>' => \date('YmdHis'),
            '<Y>' => \date('Y'),
            '<m>' => \date('m'),
            '<d>' => \date('d'),
            '<H>' => \date('H'),
            '<i>' => \date('i'),
            '<s>' => \date('s'),
        ], $variables);

        return str_replace(array_keys($replacements), array_values($replacements), $string);
    }
}
