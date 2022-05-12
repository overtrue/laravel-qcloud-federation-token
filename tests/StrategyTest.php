<?php

namespace Tests;

use Overtrue\LaravelQcloudFederationToken\Strategy;
use PHPUnit\Framework\TestCase;

class StrategyTest extends TestCase
{
    public function test_getters()
    {
        $strategy = new Strategy([
            'secret_id' => 'secretId',
            'secret_key' => 'secretKey',
            'region' => 'region',
            "principal" => [
                "qcs" => [
                    "qcs::cam::uid/1238423:uin/3232523"
                ]
            ],
            "effect" => "deny",
            "action" => [
                "cos:PutObject",
                "cos:GetObject",
            ],
            "resource" => [
                "qcs::cos:ap-beijing:uid/1238423:bucketA-1238423/*",
                "qcs::cos:ap-guangzhou:uid/1238423:bucketB-1238423/object2"
            ],
            "condition" => [
                "ip_equal" => [
                    "qcs:ip" => "10.121.2.10/24"
                ]
            ]
        ]);

        $this->assertSame('secretId', $strategy->getSecretId());
        $this->assertSame('secretKey', $strategy->getSecretKey());
        $this->assertSame('region', $strategy->getRegion());
        $this->assertSame('deny', $strategy->getEffect());
        $this->assertSame([
            'cos:PutObject',
            'cos:GetObject',
        ], $strategy->getActions());
        $this->assertSame([
            'qcs::cos:ap-beijing:uid/1238423:bucketA-1238423/*',
            'qcs::cos:ap-guangzhou:uid/1238423:bucketB-1238423/object2',
        ], $strategy->getResources());
        $this->assertSame([
            'ip_equal' => [
                'qcs:ip' => '10.121.2.10/24'
            ],
        ], $strategy->getConditions());
    }
}
