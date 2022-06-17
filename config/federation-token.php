<?php

return [
    // 默认配置，strategies 下的每一个策略将合并此基础配置
    'default' => [
        'secret_id' => env('QCLOUD_COS_SECRET_ID'),
        'secret_key' => env('QCLOUD_COS_SECRET_KEY'),
        'region' => env('QCLOUD_COS_REGION', 'ap-guangzhou'),
        "effect" => "allow",
        "action" => [
            "cos:PutObject",
            "cos:GetObject",
            "cos:HeadObject",
            //...
        ],
        'variables' => [
            'bucket' => env('QCLOUD_COS_BUCKET'),
            'appid' => env('QCLOUD_COS_APP_ID'),
        ],
    ],
    // 请注意：
    // 官方限制了一个策略规则的总字符长度为 1000，所以一个规则尽量别写太复杂，否则可能会报错
    'strategies' => [
        // 多个策略生成一个 token
//        'all' => [
//            'strategies' => ['images'],
//        ],
//        'images' => [
//            // 策略名称，可选
//            'name' => 'cos-put',
//            // 临时凭证过期时间
//            'expires_in' => 1800,
//
//            // 将与默认配置合并
//            'variables' => [
//                'appid' => env('QCLOUD_APP_ID'),
//                'bucket' => env('QCLOUD_COS_BUCKET', ''),
//                //...
//            ],
//
//            //Statement 请参考：https://cloud.tencent.com/document/product/598/10603
//            "statements" => [
//                [
//                    "action" => [
//                        "cos:PutObject",
//                        "cos:GetObject",
//                    ],
//                    "resource" => [
//                        "qcs::cos:ap-beijing:uid/{uid}:{bucket}-{appid}/{date}/{uuid}/*",
//                    ],
//                ]
//            ],
//        ],
    ],
];
