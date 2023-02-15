<?php

namespace Overtrue\LaravelQcloudFederationToken;

use Carbon\Carbon;
use Overtrue\LaravelQcloudFederationToken\Events\TokenCreated;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Sts\V20180813\Models\GetFederationTokenRequest;
use TencentCloud\Sts\V20180813\StsClient;

class Builder
{
    public const MAX_EXPIRES_IN = 7200;

    public function __construct(
        protected string $secretId,
        protected string $secretKey,
        protected ?string $region = 'ap-guangzhou',
        protected ?string $endpoint = 'sts.tencentcloudapi.com',
    ) {
    }

    public function build(array $statements, int|string|Carbon $expiresIn = 1800, ?string $name = null): Token
    {
        $credential = new Credential($this->secretId, $this->secretKey);
        $httpProfile = new HttpProfile();
        $httpProfile->setEndpoint($this->endpoint ?: 'sts.tencentcloudapi.com');

        $clientProfile = new ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);
        $client = new StsClient($credential, $this->region ?: 'ap-guangzhou', $clientProfile);

        $request = new GetFederationTokenRequest();

        $request->fromJsonString(
            json_encode([
                'Name' => $name ?? config('app.name').'.'.uniqid(),
                'Policy' => json_encode([
                    'version' => '2.0',
                    'statement' => $statements,
                ]),
                'DurationSeconds' => $this->expiresIn($expiresIn),
            ])
        );

        $response = $client->GetFederationToken($request);

        $credentials = new Credentials($response->getCredentials()->getToken(), $response->getCredentials()->getTmpSecretId(), $response->getCredentials()->getTmpSecretKey());

        return tap(new Token($credentials, $response->getExpiredTime(), $response->getExpiration(), $response->getRequestId()), function ($token) {
            event(new TokenCreated($token));
        });
    }

    protected function expiresIn(int|string|Carbon $expiredAt): int
    {
        $format = fn ($expiresIn) => (int) (min($expiresIn, static::MAX_EXPIRES_IN));

        return match (true) {
            is_int($expiredAt) => $format($expiredAt),
            is_object($expiredAt) => $format((int) $expiredAt->timestamp - time()),
            is_string($expiredAt) => $format((int) Carbon::parse($expiredAt)->timestamp - time()),
        };
    }
}
