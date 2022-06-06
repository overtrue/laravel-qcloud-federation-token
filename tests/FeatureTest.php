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
                'default' => [
                    'secret_id' => 'default-secret-id',
                    'expired_at' => '+1 hour',
                ],
                'strategies' => [
                    'cvm' => array_merge([
                        'secret_id' => 'secret-id',
                        'secret_key' => 'secret-key',
                        'region' => 'ap-tokyo',
                    ], $statement),

                    'cos' => array_merge([
                        'secret_key' => 'secret-key',
                        'region' => 'ap-tokyo',
                    ], $statement),
                ],
            ],
        ]);


        $this->assertSame('secret-id', FederationToken::getSecretId());
        $this->assertSame('default-secret-id', FederationToken::strategy('cos')->getSecretId());

        $builder = FederationToken::getBuilder();
        $this->assertSame([$statement], $builder->getStatement());
    }

    public function test_it_can_replace_vars()
    {
        $statement = [
            "principal" => [
                "qcs" => [
                    "qcs::cam::uid/{uid}:uin/{uin}"
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
                "qcs::cos:{region}:uid/{uid}:bucketA-{uid}/*",
                "qcs::cos:{region}:uid/{uid}:bucketA-{uid}/{timestamp}/*",
                "qcs::cos:{region}:uid/1238423:bucketB-{appid}/{date}/{Y}/{m}/{d}/object2"
            ],
            "condition" => [
                "ip_equal" => [
                    "qcs:ip" => "10.121.2.10/24"
                ]
            ]
        ];

        config([
            'qcloud-federation-token' => [
                'default' => [
                    'secret_id' => 'default-secret-id',
                    'expired_at' => '+1 hour',
                    'variables' => [
                        'uid' => 'mock-uid',
                        'uin' => 'mock-uin',
                        'appid' => 'mock-appid',
                    ],
                ],
                'strategies' => [
                    'cvm' => array_merge([
                        'secret_id' => 'secret-id',
                        'secret_key' => 'secret-key',
                        'region' => 'ap-tokyo',
                    ], $statement),

                    'cos' => array_merge([
                        'secret_key' => 'secret-key',
                        'region' => 'ap-tokyo',
                    ], $statement),
                ],
            ],
        ]);

        $builder = FederationToken::getBuilder();
        $statement = $builder->getStatement();

        // "qcs::cam::uid/{uid}:uin/{uin}"
        $this->assertSame(['qcs' => ['qcs::cam::uid/mock-uid:uin/mock-uin']], $statement[0]['principal']);

        // "qcs::cos:{region}:uid/{uid}:bucketA-{uid}/*",
        $this->assertSame(
            sprintf(
                'qcs::cos:%s:uid/%s:bucketA-%s/*',
                'ap-tokyo',
                'mock-uid',
                'mock-uid',
            ),
            $statement[0]['resource'][0]
        );

        // "qcs::cos:{region}:uid/{uid}:bucketA-{uid}/{timestamp}/*",
        $this->assertSame(
            sprintf(
                'qcs::cos:%s:uid/%s:bucketA-%s/%s/*',
                'ap-tokyo',
                'mock-uid',
                'mock-uid',
                time(),
            ),
            $statement[0]['resource'][1]
        );

        // "qcs::cos:{region}:uid/1238423:bucketB-{appid}/{date}/{Y}/{m}/{d}/object2"
        $this->assertSame(
            sprintf(
                'qcs::cos:%s:uid/1238423:bucketB-%s/%s/%s/%s/%s/object2',
                'ap-tokyo',
                'mock-appid',
                date('Ymd'),
                date('Y'),
                date('m'),
                date('d')
            ),
            $statement[0]['resource'][2]
        );
    }
}
