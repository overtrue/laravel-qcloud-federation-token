<?php

namespace Tests;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Overtrue\LaravelQcloudFederationToken\Credentials;
use Overtrue\LaravelQcloudFederationToken\Token;
use PHPUnit\Framework\TestCase;

use function json_encode;

class TokenTest extends TestCase
{
    public function test_getters()
    {
        $credentials = new Credentials(
            'mock-token',
            'mock-tmpSecretId',
            'mock-tmpSecretKey'
        );

        $token = new Token($credentials, 1547696355, '2022-05-05 05:05:05', 'mock-request-id', [
            'mock-statement',
        ]);

        $this->assertEquals($credentials, $token->getCredentials());
        $this->assertSame(1547696355, $token->getExpiredAt());
        $this->assertSame('2022-05-05 05:05:05', $token->getExpiration());
        $this->assertSame('mock-request-id', $token->getRequestId());
        $this->assertSame(['mock-statement'], $token->getStatements());
    }

    public function test_to_array()
    {
        $credentials = new Credentials(
            'mock-token',
            'mock-tmpSecretId',
            'mock-tmpSecretKey'
        );

        $token = new Token($credentials, 1547696355, '2022-05-05 05:05:05', 'mock-request-id', [
            'mock-statement',
        ]);

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
