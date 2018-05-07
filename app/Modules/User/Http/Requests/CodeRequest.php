<?php

declare(strict_types = 1);

namespace Modules\User\Http\Requests;

trait CodeRequest
{
    /**
     * 验证规则
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'code' => 'required'
        ];
    }

    /**
     * 验证规则信息实现
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'code.required' => '验证码不能为空'
        ];
    }
}
