<?php

declare(strict_types = 1);

/*
+-----------------------------------------------------------------------------------------------------------------------
| Author: 植成樑 <atzcl0310@gmail.com>  Blog：https://www.atzcl.cn
+-----------------------------------------------------------------------------------------------------------------------
| 用户认证相关
|
*/

namespace Modules\User\Traits;

use Illuminate\Foundation\Auth\ThrottlesLogins;

trait AuthorizeTrait
{
    use ThrottlesLogins;

    /**
     * @var int 登录次数限制
     */
    protected $maxAttempts = 5;

    /**
     * @var int 登录次数限制的时间
     */
    protected $decayMinutes = 1;

    /**
     * 处理登录
     *
     * @return object
     */
    public function attemptLogin(): object
    {
        // 判断是否已经超过了登录限制
        if ($this->hasTooManyLoginAttempts($this->request)) {
            abort(403, '您因为尝试登录次数过频，请 ' . $this->decayMinutes() . ' 分钟后重新尝试');
        }

        // 尝试获取登录用户数据
        $result = $this->getUserInfo();

        // 验证密码
        $this->verifyPassword($result);

        // 如果登录成功，那么应该清除掉错误登录次数
        $this->clearLoginAttempts($this->request);

        // 返回登录用户数据
        return $result;
    }

    /**
     * 获取用户数据
     *
     * @return mixed
     */
    public function getUserInfo()
    {
        return $this->repository->addConditions([
            $this->username() => $this->request[$this->username()]
        ])->first();
    }

    /**
     * 验证用户密码是否一致
     *
     * @param $user
     */
    public function verifyPassword($user)
    {
        // 判断是否存在，并且密码是否一致
        if (empty($user) || !app('hash')->check($this->request->password, $user->password)) {
            // 增加错误尝试次数
            $this->incrementLoginAttempts($this->request);

            // 抛出验证错误
            abort(
                422,
                trans('validation.attributes.' . $this->username()) . '或密码错误' . $this->restrictionsTips()
            );
        }
    }

    /**
     * 计算还能尝试登录次数，然后给出合适的错误提示
     *
     * @return string
     */
    public function restrictionsTips(): string
    {
        // 登录失败次数
        $errorLimit = $this->limiter()->attempts($this->throttleKey($this->request));

        // 计算还能尝试登录次数，然后给出合适的错误提示
        return ', 登录失败 ' . $errorLimit . ' 次, 您还可以尝试 ' . ($this->maxAttempts() - $errorLimit) . ' 次';
    }

    /**
     * 获取登录字段
     *
     * @return string
     */
    public function username(): string
    {
        switch ($this->request->type ?? 'default') {
            case 'phone':
                return 'phone';
            case 'email':
                return 'email';
            default:
                return 'username';
        }
    }
}
