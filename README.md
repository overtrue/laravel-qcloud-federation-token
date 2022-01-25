Laravel QCloud FederationToken generator.
---

![Laravel Octane Ready Status](https://img.shields.io/badge/Octance-ready-green?style=flat-square)
![GitHub release (latest SemVer)](https://img.shields.io/github/v/release/overtrue/laravel-qcloud-federation-token?style=flat-square)
![GitHub License](https://img.shields.io/github/license/overtrue/laravel-qcloud-federation-token?style=flat-square)
![Packagist Downloads](https://img.shields.io/packagist/dt/overtrue/laravel-qcloud-federation-token?style=flat-square)

Laravel [腾讯云联合身份临时访问凭证](https://cloud.tencent.com/document/product/1312/48195) 生成器。

[![Sponsor me](https://github.com/overtrue/overtrue/blob/master/sponsor-me-button-s.svg?raw=true)](https://github.com/sponsors/overtrue)

开始之前，请您仔细阅读并理解一下官方文档：

- [获取联合身份临时访问凭证](https://cloud.tencent.com/document/product/1312/48195)
- [CAM 策略语法](https://cloud.tencent.com/document/product/598/10603)
- [临时证书](https://cloud.tencent.com/document/api/1312/48198#Credentials)

## 安装

```shell
$ composer require overtrue/laravel-qcloud-federation-token -vvv
```

### 配置

你可以通过以下命令将配置文件写入 `config/qcloud-federation-token.php`:

```php
$ php artisan vendor:publish --provider="Overtrue\\LaravelQcloudFederationToken\\PackageServiceProvider" --tag=config
```

## 使用

TODO

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
