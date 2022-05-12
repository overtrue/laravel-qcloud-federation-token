<?php

namespace Tests;

use Illuminate\Support\Facades\Event;
use Overtrue\LaravelQcloudFederationToken\FederationToken;
use function config;

class FeatureTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    public function test_it_can_create_token()
    {
        $statement = [
            "principal" => [
                "qcs" => [
                    "qcs::cam::uid/1238423:uin/3232523"
                ]
            ],
            "effect" => "allow",
            "action" => [
                "cos:PutObject",
                "cos:GetObject",
                "cos:HeadObject",
                "cos:OptionsObject",
                "cos:ListParts",
                "cos:GetObjectTagging"
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
        ];

        config([
            'qcloud-federation-token' => [
                'strategies' => [
                    'default' => array_merge([
                        'secret_id' => 'secret-id',
                        'secret_key' => 'secret-key',
                        'region' => 'ap-tokyo',
                    ], $statement),
                ],
            ],
        ]);

        $builder = FederationToken::getBuilder();

        $this->assertSame([$statement], $builder->getStatement());
    }
}
