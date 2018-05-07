<?php

declare(strict_types = 1);

namespace Modules\User\Http\Requests;

use App\Rules\PhoneRule;

trait LoginRequest
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
            'password' =>'required|between:6,64'
        ];

        $optionalRules = [
            'phone'     => [ 'required', 'max:20', new PhoneRule() ],
            'email'     => 'required|email|max:100',
            'username'  => 'required|between:5,50',
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
            'type.required' => '登录类型不能为空',
            'type.max' => '登录类型字符最多 20 字符',

            'username.between' => '账号必须介于 5 - 50 个字符之间',

            'phone.max' => '手机号码最多 20 字符'
        ];
    }
}
