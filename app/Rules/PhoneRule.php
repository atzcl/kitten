<?php

declare(strict_types = 1);

/*
+-----------------------------------------------------------------------------------------------------------------------
| Author: 植成樑 <atzcl0310@gmail.com>  Blog：https://www.atzcl.cn
+-----------------------------------------------------------------------------------------------------------------------
| 自定义手机号码验证规则
|
*/

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PhoneRule implements Rule
{
    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool|null|string|string[]
     */
    public function passes($attribute, $value)
    {
        return preg_match(
            '/^((\+?[0-9]{1,4})|(\(\+86\)))?(13[0-9]|14[57]|15[012356789]|17[03678]|18[0-9])\d{8}$/',
            $value
        );
    }

    /**
     * 获取验证错误信息
     *
     * @return string
     */
    public function message()
    {
        return '手机号码格式不正确';
    }
}
