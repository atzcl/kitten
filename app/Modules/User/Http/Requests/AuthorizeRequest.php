<?php

declare(strict_types = 1);

namespace Modules\User\Http\Requests;

trait AuthorizeRequest
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
     * 验证规则自定义提示
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'code.required' => '请提交授权码'
        ];
    }
}
