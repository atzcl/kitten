<?php

declare( strict_types = 1);

/*
+-----------------------------------------------------------------------------------------------------------------------
| Author: 植成樑 <atzcl0310@gmail.com>  Blog：https://www.atzcl.cn
+-----------------------------------------------------------------------------------------------------------------------
| JWT 相关方法
|
*/

namespace App\Services;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class JWTService
{
    /**
     * 获取加密 token
     *
     * @param array $sub 保存 token 的标识符（默认为用户标识）
     * @return string
     */
    public function createJWT(array $sub)
    {
        // 为了避免 Auth-JWT 使用同一个 Claims 来进行编码
        // 这里先清空原有 custom, 重新实例一个空的来使用
        JWTAuth::manager()->getPayloadFactory()->customClaims($sub)->emptyClaims();

        return JWTAuth::encode(JWTFactory::sub($sub)->make())->get();
    }

    /**
     * 获取 JWT 解密数据
     *
     * @param string $token JWT token
     * @return \Tymon\JWTAuth\Payload
     */
    public function decodeJWT(string $token)
    {
        return JWTAuth::setToken($token)->getPayload();
    }

    /**
     * 快捷获取 Payload 载荷中的 sub 数据
     *
     * @param string $token JWT token
     * @return mixed
     */
    public function getSubJWT(string $token)
    {
        return $this->decodeJWT($token)['sub'];
    }

    /**
     * 刷新过期 token, 返回新 token
     *
     * @param string $token JWT token
     * @return string
     */
    public function refreshJWT(string $token)
    {
        $token = JWTAuth::setToken($token);

        return JWTAuth::refresh($token)->get();
    }

    /**
     * 拉黑 token
     *
     * @param string $token
     * @param bool $forceForever
     * @return mixed
     */
    public function invalidateJWT(string $token, bool $forceForever = false)
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
    public function checkJWT(string $token)
    {
        return $this->decodeJWT($token) ? true : false;
    }

    /**
     * 重新生成 token, 拉黑旧 token [ 这样是为了延长活跃用户的 token 失效时间 ]
     *
     * @param string $token JWT token
     * @return string
     */
    public function prolongJWT(string $token)
    {
        // 如果 token 失效的时间大于设定失效的 10 分之 1，那么是直接刷新 token
        $newToken = $this->refreshJWT($token);

        $claims = $this->decodeJWT($newToken)->toArray();

        $isInvalid = ((int) $claims['iat'] + (int) config('jwt.refresh_ttl') * 60) - time() <
            (((int)config('jwt.refresh_ttl') * 60) / 10);

        if ($isInvalid) {
            // 如果 token 失效的时间小于设定失效的 10分之1，那么是直接生成新 token，拉黑旧 token
            $this->invalidateJWT($newToken);

            // 返回重新创建的 token
            return $this->createJWT($claims['sub']);
        }

        // 如果 token 失效的时间大于设定失效的 10分之1，那么是直接返回刷新 token
        return $newToken;
    }
}
