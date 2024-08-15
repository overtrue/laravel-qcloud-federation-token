<?php

namespace Overtrue\LaravelQcloudFederationToken;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidArgumentException;

use function json_encode;

class Statement implements \ArrayAccess, \JsonSerializable, Arrayable, Jsonable
{
    protected array $principal = [];

    protected array $action = [];

    protected array $resource = [];

    protected array $condition = [];

    protected array $variables = [];

    protected string $effect = 'allow';

    /**
     * @throws \Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidArgumentException
     */
    public function __construct(array $config = [])
    {
        $this->setEffect($config['effect'] ?? 'allow');

        if (! empty($config['principal'])) {
            $this->setPrincipal($config['principal']);
        }

        if (! empty($config['action'])) {
            $this->setAction($config['action']);
        }

        if (! empty($config['resource'])) {
            $this->setResource($config['resource']);
        }

        if (! empty($config['condition'])) {
            $this->setCondition($config['condition']);
        }
    }

    public function setVariables(array $variables): static
    {
        $this->variables = $variables;

        return $this;
    }

    public function setPrincipal(array $principal): static
    {
        $this->principal = $principal;

        return $this;
    }

    /**
     * @throws \Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidArgumentException
     */
    public function setEffect(string $effect): static
    {
        if (! in_array($effect, ['allow', 'deny'])) {
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

    public function setAction(array $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function setResource(array $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function setCondition(array $condition): static
    {
        $this->condition = $condition;

        return $this;
    }

    public function __toString(): string
    {
        return json_encode($this->toArray()) ?? '';
    }

    #[ArrayShape(['principal' => 'array', 'effect' => 'string', 'action' => 'array', 'resource' => 'array', 'condition' => 'array'])]
    public function toArray(): array
    {
        $principal = $this->principal;

        if (! empty($principal['qcs'])) {
            $principal['qcs'] = array_map([$this, 'replaceVariables'], $principal['qcs']);
        }

        return [
            'principal' => $principal,
            'effect' => $this->effect,
            'action' => $this->action,
            'resource' => array_map([$this, 'replaceVariables'], $this->resource),
            'condition' => $this->condition,
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

    public function toJson($options = 0): bool|string
    {
        return json_encode($this->toArray());
    }

    #[ArrayShape(['principal' => 'array', 'effect' => 'string', 'action' => 'array', 'resource' => 'array', 'condition' => 'array'])]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function offsetExists(mixed $offset): bool
    {
        return property_exists($this, $offset);
    }

    public function offsetGet(mixed $offset)
    {
        return $this->toArray()[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value)
    {
        $method = sprintf('set%s', ucfirst($offset));

        if (method_exists($this, $method)) {
            $this->$method($value);
        }
    }

    public function offsetUnset(mixed $offset) {}
}
