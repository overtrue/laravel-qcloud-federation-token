<?php

return [
    // 默认配置，strategies 下的每一个策略将合并此基础配置
    'default' => [
        'secret_id' => env('QCLOUD_STS_SECRET_ID'),
        'secret_key' => env('QCLOUD_STS_SECRET_KEY'),
        'region' => env('QCLOUD_COS_REGION', 'ap-guangzhou'),
        "effect" => "allow",
        "action" => [
            "cos:PutObject",
            "cos:GetObject",
            "cos:HeadObject",
            //...
        ],
        'variables' => [
            'uin' => env('QCLOUD_COS_STS_UIN'),
            'bucket' => env('QCLOUD_COS_BUCKET'),
            'appid' => env('QCLOUD_COS_APP_ID'),
        ],
    ],
    // strategies
    'strategies' => [
        // 多个策略生成一个 token
//        'all' => [
//            'strategies' => ['images'],
//        ],
        // 请参考：https://cloud.tencent.com/document/product/598/10603
//        'images' => [
//            "resource" => [
//                "qcs::cos:ap-guangzhou:uid/<appid>:<bucket>-<appid>/screens/<Y>/<m>/<d>/<prefix>/*",
//            ],
//            "condition" => [
//                "string_equal_ignore_case" => [
//                    // 限制上传 MIME
//                    "cos:content-type" => [
//                        'image/pjpeg',
//                        'image/png',
//                        'image/bmp',
//                        'image/x-windows-bmp',
//                        'image/gif',
//                        'image/webp',
//                    ],
//                ],
//                "numeric_less_than_equal" => [
//                    // 限制上传大小
//                    "cos:content-length" => 100 * 1024 * 1024
//                ]
//            ]
//        ],
    ],
];
