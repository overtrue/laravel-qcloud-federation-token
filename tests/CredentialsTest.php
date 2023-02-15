<?php

namespace Tests;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use function json_encode;
use Overtrue\LaravelQcloudFederationToken\Credentials;
use PHPUnit\Framework\TestCase;

class CredentialsTest extends TestCase
{
    public function test_getters()
    {
        $credentials = new Credentials(
            'mock-token',
            'mock-tmpSecretId',
            'mock-tmpSecretKey'
        );

        $this->assertSame('mock-token', $credentials->getToken());
        $this->assertSame('mock-tmpSecretId', $credentials->getTmpSecretId());
        $this->assertSame('mock-tmpSecretKey', $credentials->getTmpSecretKey());
    }

    public function test_to_array()
    {
        $credentials = new Credentials(
            'mock-token',
            'mock-tmpSecretId',
            'mock-tmpSecretKey'
        );

        $this->assertInstanceOf(Arrayable::class, $credentials);

        $this->assertSame([
            'token' => 'mock-token',
            'tmp_secret_id' => 'mock-tmpSecretId',
            'tmp_secret_key' => 'mock-tmpSecretKey',
        ], $credentials->toArray());
    }

    public function test_to_json()
    {
        $credentials = new Credentials(
            'mock-token',
            'mock-tmpSecretId',
            'mock-tmpSecretKey'
        );

        $this->assertInstanceOf(Jsonable::class, $credentials);

        $this->assertSame(json_encode([
            'token' => 'mock-token',
            'tmp_secret_id' => 'mock-tmpSecretId',
            'tmp_secret_key' => 'mock-tmpSecretKey',
        ]), $credentials->toJson());
    }
}
