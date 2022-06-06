<?php

namespace Overtrue\LaravelQcloudFederationToken;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Overtrue\LaravelQcloudFederationToken\Events\TokenCreated;
use Overtrue\LaravelQcloudFederationToken\Exceptions\HttpException;
use Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidArgumentException;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Sts\V20180813\Models\GetFederationTokenRequest;
use TencentCloud\Sts\V20180813\StsClient;
use Throwable;

use function config;
use function event;
use function is_int;
use function is_object;
use function is_string;
use function json_encode;
use function min;
use function tap;
use function time;

class Builder
{
    protected string $name;
    protected array $principal = [];
    protected array $actions = [];
    protected array $resources = [];
    protected array $conditions = [];
    protected string $effect = 'allow';
    protected int $expiresIn = 1800;

    public const MAX_EXPIRES_IN = 7200;

    public function __construct(
        protected string $secretId,
        protected string $secretKey,
        protected ?string $region = 'ap-guangzhou',
        protected ?string $endpoint = 'sts.tencentcloudapi.com',
        protected ?array $variables = []
    ) {
        $this->name = config('app.name');
    }

    public function withVariables(array $variables): static
    {
        $this->variables = $variables;

        return $this;
    }

    public function expiresIn(int $expiresIn): static
    {
        $this->expiresIn = min($expiresIn, static::MAX_EXPIRES_IN);

        return $this;
    }

    public function principal(array $principal): static
    {
        $this->principal = $principal;

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function expiredAt(int|string|Carbon $expiredAt): static
    {
        match (true) {
            is_int($expiredAt) && $expiredAt <= static::MAX_EXPIRES_IN => $this->expiresIn($expiredAt),
            is_string($expiredAt) => $this->expiresIn(Carbon::parse($expiredAt)->timestamp - time()),
            is_object($expiredAt) => $this->expiresIn($expiredAt->timestamp - time()),
            default => throw new InvalidArgumentException('Unexpected $expiredAt value.'),
        };

        return $this;
    }

    public function name(string $name): static
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

    /**
     * @throws HttpException
     */
    public function build(): Token
    {
        try {
            $credential = new Credential($this->secretId, $this->secretKey);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint($this->endpoint ?: 'sts.tencentcloudapi.com');

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new StsClient($credential, $this->region ?: 'ap-guangzhou', $clientProfile);

            $request = new GetFederationTokenRequest();

            $request->fromJsonString(
                json_encode([
                    'Name' => $this->name,
                    'Policy' => json_encode([
                        'version' => '2.0',
                        'statement' => $this->getStatement(),
                    ]),
                    'DurationSeconds' => $this->expiresIn,
                ])
            );

            $response = $client->GetFederationToken($request);

            $credentials = new Credentials($response->getCredentials()->getToken(), $response->getCredentials()->getTmpSecretId(), $response->getCredentials()->getTmpSecretKey());

            return tap(new Token($credentials, $response->getExpiredTime(), $response->getExpiration(), $response->getRequestId()), function ($token) {
                event(new TokenCreated($token));
            });
        } catch (Throwable $e) {
            throw new HttpException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public function getStatement(): array
    {
        $principal = $this->principal;
        $principal['qcs'] = array_map([$this, 'replaceVariables'], $this->principal['qcs']);
        $resources = array_map([$this, 'replaceVariables'], $this->resources);

        return [
            [
                'principal' => $principal,
                'effect' => $this->effect,
                'action' => $this->actions,
                'resource' => $resources,
                'condition' => $this->conditions,
            ],
        ];
    }

    protected function replaceVariables(string $string): string
    {
        $variables = array_merge(...array_map(function ($key, $value) {
            return ['{'.$key.'}' => trim($value, '{}')];
        }, array_keys($this->variables), $this->variables));

        $replacements = array_merge([
            '{region}' => $this->region,
            '{uuid}' => Str::uuid()->toString(),
            '{timestamp}' => \time(),
            '{random}' => Str::random(16),
            '{random:32}' => Str::random(32),
            '{date}' => \date('Ymd'),
            '{Ymd}' => \date('Ymd'),
            '{YmdHis}' => \date('YmdHis'),
            '{Y}' => \date('Y'),
            '{m}' => \date('m'),
            '{d}' => \date('d'),
            '{H}' => \date('H'),
            '{i}' => \date('i'),
            '{s}' => \date('s'),
        ], $variables);

        return str_replace(array_keys($replacements), array_values($replacements), $string);
    }
}
