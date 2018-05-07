<?php

declare(strict_types = 1);

/*
+-----------------------------------------------------------------------------------------------------------------------
|
+-----------------------------------------------------------------------------------------------------------------------
| RegisterRequest 验证规则
|
*/

namespace Modules\User\Http\Requests;

use App\Rules\PhoneRule;

trait RegisterRequest
{
    /**
     * 验证规则
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'type' => 'required|max:20',
            'password' =>'required|between:6,64|confirmed' // 需要与 password_confirmation 配对一致
        ];

        $optionalRules = [
            'phone'     => [ 'required', 'max:20', 'unique:users', new PhoneRule() ],
            'email'     => 'required|email|max:100|unique:users',
            'username'  => 'required|between:5,50|unique:users',
        ];

        return array_merge($rules, [ $this->username() => $optionalRules[$this->username()] ]);
    }

    /**
     * 验证规则自定义提示
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'type.required' => '注册类型不能为空',
            'type.max' => '注册类型字符最多 20 字符',

            'username.unique' => '用户名已被占用，请重新填写',
            'username.between' => '用户名必须介于 5 - 50 个字符之间',

            'phone.unique' => '手机号码已被占用，请重新填写'
        ];
    }
}
