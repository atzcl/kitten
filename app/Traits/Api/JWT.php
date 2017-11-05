<?php
/*
+-----------------------------------------------------------------------------------------------------------------------
| Author: 植成樑 <atzcl0310@gmail.com>  Blog：https://www.zcloop.com
+-----------------------------------------------------------------------------------------------------------------------
| JWT 相关方法
|
*/
namespace App\Traits\Api;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

trait JWT
{
    /**
     * 获取加密 token
     *
     * @param array $sub 保存 token 的标识符（默认为用户标识）
     * @return string
     */
    protected function createJWT(array $sub)
    {
        return JWTAuth::encode(JWTFactory::sub($sub)->make());
    }

    /**
     * 获取 JWT 解密数据
     *
     * @param string $token JWT token
     * @return \Tymon\JWTAuth\Payload
     */
    protected function decodeJWT(string $token)
    {
        return JWTAuth::setToken($token)->getPayload();
    }

    /**
     * 快捷获取 Payload 载荷中的 sub 数据
     *
     * @param string $token JWT token
     * @return mixed
     */
    protected function getSubJWT(string $token)
    {
        return $this->decodeJWT($token)['sub'];
    }

    /**
     * 刷新过期 token, 返回新 token
     *
     * @param string $token JWT token
     * @return string
     */
    protected function refreshJWT(string $token)
    {
        $token = JWTAuth::setToken($token);
        return JWTAuth::refresh($token);
    }

    /**
     * 拉黑 token
     *
     * @param string $token JWT token
     * @param bool $forceForever
     * @return bool
     */
    protected function invalidateJWT(string $token, bool $forceForever = false)
    {
        $token = JWTAuth::setToken($token);
        // 利用 __call 调用 manager 的 invalidate 进行拉黑
        return JWTAuth::invalidate($token, $forceForever);
    }

    /**
     * 判断 token 是否存在并未过期、拉黑
     *
     * @param string $token JWT token
     * @return bool
     */
    protected function checkJWT(string $token)
    {
        return $this->decodeJWT($token) ? true : false;
    }

    /**
     * 重新生成 token, 拉黑旧 token【这样是为了延长活跃用户的 token 失效时间】
     *
     * @param string $token JWT token
     * @return string
     */
    protected function prolongJWT(string $token)
    {
        // 如果 token 失效的时间大于设定失效的 10分之1，那么是直接刷新 token
        $newToken = $this->refreshJWT($token);

        $Claims = $this->decodeJWT($newToken)->toArray();
        if (((int) $Claims['iat'] + (int) config('jwt.refresh_ttl') * 60) - time() <
            (((int)config('jwt.refresh_ttl') * 60) / 10)
        ) {
            // 如果 token 失效的时间小于设定失效的 10分之1，那么是直接生成新 token，拉黑旧 token
            $this->invalidateJWT($newToken);
            return $this->createJWT($Claims['sub']);
        }

        // 如果 token 失效的时间大于设定失效的 10分之1，那么是直接返回刷新 token
        return $newToken;
    }
}
