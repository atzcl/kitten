# kitten 多模块应用
> 基于以下技术构建

- PHP 7.2 以上
- Laravel 5.5 LTS
- Composer
- Redis
- MySQL 5.7 以上
- Elasticsearch
- Swoole / Workerman
- ...

## 快速开始
> 请确保已经拉取项目到本地环境,并且安装配置好 ` Composer ` 包管理器，另: 需把站点运行目录设置到 ` public ` 下

>  模块化依赖 ` laravel-modules ` 扩展包，详细可参考 [官方文档](https://nwidart.com/laravel-modules/v3/introduction)

>  Repository 层使用 ` l5-repository ` 拓展包，详细可参考 [官方文档](http://andersonandra.de/l5-repository/)

**1、安装依赖**
```bash
composer install
```

**2、Copy `.env` 环境变量配置**
```bash
cp .env.example .env
```

**3、生成本地应用 APP_KEY**
```bash
php artisan key:generate  
```

**4、生成本地应用 JWT_SECRET**
```bash
php artisan jwt:secret 
```

## Nginx 重写

```bash
# 去除末尾的斜杠, SEO 更加友好
if (!-d $request_filename) {
    rewrite ^/(.+)/$ /$1 permanent;
}

# 去除 index action
if ($request_uri ~* index/?$) {
    rewrite ^/(.*)/index/?$ /$1 permanent;
}

# 根据 laravel 规则进行 url 重写
if (!-e $request_filename) {
    rewrite ^/(.*)$ /index.php?/$1 last;
    break;
}
```

## 其他
```bash
// 待补充
```
