<?php

return [
    // strategies
    'strategies' => [
        // 请参考：https://cloud.tencent.com/document/product/598/10603
    //    'cos' => [
    //        'secret_id' => env('QCLOUD_COS_SECRET_ID', ''),
    //        'secret_key' => env('QCLOUD_COS_SECRET_KEY', ''),
    //        'region' => env('QCLOUD_COS_REGION', 'ap-guangzhou'),
    //        "principal" => [
    //            "qcs" => [
    //                sprintf("qcs::cam::uid/%s:uin/%s", env('QCLOUD_UID'), env('QCLOUD_UIN')),
    //            ]
    //        ],
    //        "effect" => "allow",
    //        "action" => [
    //            "cos:PutObject",
    //            "cos:GetObject",
    //        ],
    //        "resource" => [
    //            sprintf("qcs::cos:ap-beijing:uid/%s:bucketA-1238423/*", env('QCLOUD_UID')),
    //        ],
    //        "condition" => []
    //    ],
    ],
];