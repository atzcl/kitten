<?php

declare(strict_types = 1);

/*
+-----------------------------------------------------------------------------------------------------------------------
| Author: 植成樑 <atzcl0310@gmail.com>  Blog：https://www.atzcl.cn
+-----------------------------------------------------------------------------------------------------------------------
| 用于辅助 Model 生成 uuid
|
*/

namespace App\Traits;

use Webpatser\Uuid\Uuid;

trait GenerateUuid
{
    /**
     * @var bool 开启自动维护创建 UUID
     */
    public static $autoGenerateUUID = true;

    /**
     * 通过重写模型的 boot 方法来达到自动生成 uuid 值
     *
     * @return mixed
     */
    public static function boot()
    {
        parent::boot();

        // 监听 creating 事件
        static::creating(function ($model) {
            // 自动维护创建 UUID
            static::autoGenerateUUID($model);
        });

        // 监听 saving 事件
//        static::saving(function ($model) {
//            // 自动维护创建 UUID
//            static::autoGenerateUUID($model);
//        });
    }

    /**
     * 自动维护创建 UUID
     *
     * @param $model
     * @throws \Exception
     */
    protected static function autoGenerateUUID($model)
    {
        if (static::$autoGenerateUUID) {
            // 生成 uuid, 并赋给 id 属性
            $model->id = generate_uuid();
        }
    }

    /**
     * 关闭自动维护创建 UUID
     *
     * @return string
     */
    public static function closeAutoGenerateUUID()
    {
        static::$autoGenerateUUID = false;

        return static::class;
    }
}
