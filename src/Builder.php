<?php

namespace Overtrue\LaravelQcloudFederationToken;

use Carbon\Carbon;
use Overtrue\LaravelQcloudFederationToken\Exceptions\HttpException;
use Overtrue\LaravelQcloudFederationToken\Exceptions\InvalidArgumentException;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Sts\V20180813\Models\GetFederationTokenRequest;
use TencentCloud\Sts\V20180813\StsClient;
use Throwable;

class Builder
{
    protected string $name;
    protected array $principal;
    protected array $actions;
    protected array $resources;
    protected array $conditions;
    protected string $effect = 'allow';
    protected int $expiresIn = 1800;

    public const MAX_EXPIRES_IN = 7200;

    public function __construct(
        protected string $secretId,
        protected string $secretKey,
        protected string $region = 'ap-guangzhou',
        protected string $endpoint = 'sts.tencentcloudapi.com'
    ) {
        $this->name = \config('app.name');
    }

    public function expiresIn(int $expiresIn): static
    {
        $this->expiresIn = \min($expiresIn, static::MAX_EXPIRES_IN);

        return $this;
    }

    public function principal(int $uid, int $uin = null): static
    {
        $this->principal = [
            'qcs' => [
                "qcs::cam::uid/$uid".($uin ? ":uin/$uin" : ''),
            ]
        ];

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

            $request->fromJsonString(json_encode([
                'Name' => $this->name,
                'Policy' => \json_encode([
                    'version' => '2.0',
                    'statement' => [
                        [
                            'principal' => $this->principal,
                            'effect' => $this->effect,
                            'action' => $this->actions,
                            'resource' => $this->resources,
                            'condition' => $this->conditions,
                        ],
                    ],
                ]),
                'DurationSeconds' => $this->expiresIn,
            ]));

            $response = $client->GetFederationToken($request);

            $credentials = new Credentials(
                $response->getCredentials()->getToken(),
                $response->getCredentials()->getTmpSecretId(),
                $response->getCredentials()->getTmpSecretKey()
            );

            return new Token(
                $credentials,
                $response->getExpiredTime(),
                $response->getExpiration(),
                $response->getRequestId()
            );
        } catch (Throwable $e) {
            throw new HttpException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}