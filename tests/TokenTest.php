<?php

namespace Tests;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use function json_encode;
use Overtrue\LaravelQcloudFederationToken\Credentials;
use Overtrue\LaravelQcloudFederationToken\Token;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    public function test_getters()
    {
        $credentials = new Credentials(
            'mock-token',
            'mock-tmpSecretId',
            'mock-tmpSecretKey'
        );

        $token = new Token($credentials, 1547696355, '2022-05-05 05:05:05', 'mock-request-id');

        $this->assertEquals($credentials, $token->getCredentials());
        $this->assertSame(1547696355, $token->getExpiredAt());
        $this->assertSame('2022-05-05 05:05:05', $token->getExpiration());
        $this->assertSame('mock-request-id', $token->getRequestId());
    }

    public function test_to_array()
    {
        $credentials = new Credentials(
            'mock-token',
            'mock-tmpSecretId',
            'mock-tmpSecretKey'
        );

        $token = new Token($credentials, 1547696355, '2022-05-05 05:05:05', 'mock-request-id');

        $this->assertInstanceOf(Arrayable::class, $token);

        $this->assertSame([
            'credentials' => $credentials->toArray(),
            'expired_at' => 1547696355,
            'expiration' => '2022-05-05 05:05:05',
        ], $token->toArray());
    }

    public function test_to_json()
    {
        $credentials = new Credentials(
            'mock-token',
            'mock-tmpSecretId',
            'mock-tmpSecretKey'
        );

        $token = new Token($credentials, 1547696355, '2022-05-05 05:05:05', 'mock-request-id');

        $this->assertInstanceOf(Jsonable::class, $token);

        $this->assertSame(json_encode([
            'credentials' => $credentials->toArray(),
            'expired_at' => 1547696355,
            'expiration' => '2022-05-05 05:05:05',
        ]), $token->toJson());
    }
}
