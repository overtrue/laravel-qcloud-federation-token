Laravel 腾讯云联合身份临时访问凭证生成器
---

![Laravel Octane Ready Status](https://img.shields.io/badge/Octance-ready-green?style=flat-square)
![GitHub release (latest SemVer)](https://img.shields.io/github/v/release/overtrue/laravel-qcloud-federation-token?style=flat-square)
![GitHub License](https://img.shields.io/github/license/overtrue/laravel-qcloud-federation-token?style=flat-square)
![Packagist Downloads](https://img.shields.io/packagist/dt/overtrue/laravel-qcloud-federation-token?style=flat-square)

Laravel [腾讯云联合身份临时访问凭证](https://cloud.tencent.com/document/product/1312/48195) 生成器，主要用于下发腾讯云联合身份临时访问凭证，比如前端直传等场景。

[![Sponsor me](https://github.com/overtrue/overtrue/blob/master/sponsor-me-button-s.svg?raw=true)](https://github.com/sponsors/overtrue)

开始之前，请您仔细阅读并理解一下官方文档：

- [获取联合身份临时访问凭证](https://cloud.tencent.com/document/product/1312/48195)
- [CAM 策略语法](https://cloud.tencent.com/document/product/598/10603)
- [临时证书](https://cloud.tencent.com/document/api/1312/48198#Credentials)
- [API Doctor(使用诊断)](https://console.cloud.tencent.com/api/diagnosis)
- [COS 自助诊断工具](https://console.cloud.tencent.com/cos/diagnose)

## 安装

```shell
$ composer require overtrue/laravel-qcloud-federation-token -vvv
```

### 配置

你可以通过以下命令将配置文件写入 `config/qcloud-federation-token.php`:

```php
$ php artisan vendor:publish --provider="Overtrue\\LaravelQCloudFederationToken\\QCloudFederationTokenServiceProvider"
```

配置语法请参考：https://cloud.tencent.com/document/product/598/10603

你可以根据使用场景配置多个策略，然后按策略分发访问凭证。

### 变量替换

在配置中难免会用到各种上下文变量或者一些动态 resouce 路径等，你可以在配置中指定 `variables` 变量来实现变量替换，例如：

> 仅 principal 和 resource 中的变量可以替换，其他变量不支持替换。

```php
// config/qcloud-federation-token.php
<?php

return [
    // 默认配置，strategies 下的每一个策略将合并此基础配置
    'default' => [
        'secret_id' => env('QCLOUD_COS_SECRET_ID', ''),
        'secret_key' => env('QCLOUD_COS_SECRET_KEY', ''),
        'region' => env('QCLOUD_COS_REGION', 'ap-guangzhou'),
        "principal" => [
            "qcs" => [
                "qcs::cam::uid/{uid}:uin/{uin}",
            ]
        ],
        
        // 全局变量，会被替换到所有策略中
        'variables' => [
            'uid' => env('QCLOUD_UID'),
            'uin' => env('QCLOUD_UIN', ''),
            'region' => env('QCLOUD_COS_REGION', 'ap-guangzhou'),
            //...
        ],
    ],
    // strategies
    'strategies' => [
        // 请参考：https://cloud.tencent.com/document/product/598/10603
        'cos' => [
            // 将与默认配置合并
            'variables' => [
                'appid' => env('QCLOUD_APP_ID'),
                'bucket' => env('QCLOUD_COS_BUCKET', ''),
                //...
            ],
            "effect" => "allow",
            "action" => [
                "cos:PutObject",
                "cos:GetObject",
            ],
            "resource" => [
                "qcs::cos:ap-beijing:uid/{uid}:{bucket}-{appid}/{date}/{uuid}/*",
            ],
        ],
    ],
];
```

以上配置将会生成如下结果：

```json
{
    "principal": {
        "qcs": [
            "qcs::cam::uid/123456:uin/233333"
        ]
    },
    "effect": "allow",
    "action": [
        "cos:PutObject",
        "cos:GetObject",
    ],
    "resource": [
        "qcs::cos:ap-beijing:uid/123456:example-12278900/20220202/bbeae9bb-d650-46f9-aab3-f4171a1bfdea/*"
    ]
}
```

### 内置变量如下

- `<uuid>` - UUID 例如：`ca007813-4a49-4d5a-afab-abae18a969a5`
- `<timestamp>` - 当前时间戳，例如：`1654485526`
- `<random>` - 随机字符串，16 位，例如：`Bbq6gkXXIPyCDsEL`
- `<random:32>` - 随机字符串，32 位，例如：`FykbMqi6GT6JHiyv6E2xqUeo3CZLPjo7`
- `<date>` - 日期，例如：`20220606`
- `<Ymd>` - 日期，例如：`20220606`
- `<YmdHis>` - 日期时间（年月日时分秒），例如：`20220606031846`
- `<Y>` - 年，例如：`2022`
- `<m>` - 月，例如：`06`
- `<d>` - 日，例如：`06`
- `<H>` - 时，例如：`03`
- `<i>` - 分，例如：`18`
- `<s>` - 秒，例如：`46`

## 使用

```php
use Overtrue\LaravelQCloudFederationToken\FederationToken;

$token = FederationToken::build();
// 或者指定策略
$token = FederationToken::strategy('cos')->build();

$token->toArray();

// 'credentials' => [
//     'token' => 'kTRtHpOSOCUzTVWmzlPKweHffXjT9Izo7b61a142d6b56d31c0a7ace4d22bcff3zpbsXKTIrCo43dRRh7bDIKE1ZOE1KRYHEm0KNLjWG_aSF63YoQWchg',
//     'tmp_secret_id' => 'AKIDw7dwZbmFSup9CnAOraJ7skiPMybaV3WPP5B4oVMCIL5kLyphV_3IyAHFJ5QMCjE6',
//     'tmp_secret_key' => '/lvEo280/AlGt4orjDl9tWLIOMl5nkexS5Pg+xys7ps=',
// ],
// 'expired_at' => 1547696355,
```

格式请参考： https://cloud.tencent.com/document/product/1312/48195

### 事件

| **Event**                                    | **Description**     |
|----------------------------------------------|---------------------|
| `Overtrue\LaravelPackage\Events\SampleEvent` | Sample description. |

## :heart: 赞助我

[![Sponsor me](https://github.com/overtrue/overtrue/blob/master/sponsor-me.svg?raw=true)](https://github.com/sponsors/overtrue)

如果你喜欢我的项目并想支持它，[点击这里 :heart:](https://github.com/sponsors/overtrue)

## 贡献代码

你可以通过以下方式参与贡献:

1. 通过 [issue tracker](https://github.com/overtrue/laravel-package/issues) 提交 Bug；
2. 通过 [issue tracker](https://github.com/overtrue/laravel-package/issues) 回答问题或修复 Bug；
3. 通过 Pull Request 增加新特性或优化文档。

_代码贡献过程不需要很正式。你只需要确保你遵循 PSR-0、PSR-1 和 PSR-2 的编码准则。任何新的代码贡献都必须附带对应的单元测试。_

## Project supported by JetBrains

Many thanks to Jetbrains for kindly providing a license for me to work on this and other open-source projects.

[![](https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg)](https://www.jetbrains.com/?from=https://github.com/overtrue)

## PHP 扩展包开发

> 想知道如何从零开始构建 PHP 扩展包？
>
> 请关注我的实战课程，我会在此课程中分享一些扩展开发经验 —— [《PHP 扩展包实战教程 - 从入门到发布》](https://learnku.com/courses/creating-package)

## License

MIT
