<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\User\Handlers\V1\Authorize;
use Modules\User\Handlers\V1\Login;
use Modules\User\Handlers\V1\Register;
use Modules\User\Services\WeChatService;

class AuthController extends Controller
{
    /**
     * 用户注册
     *
     * @param Register $handler
     * @return mixed
     * @throws \Exception
     */
    public function store(Register $handler)
    {
        return $handler->toResponse();
    }

    /**
     * 用户登录
     *
     * @param Login $handler
     * @return mixed
     * @throws \Exception
     */
    public function authorize(Login $handler)
    {
        return $handler->toResponse();
    }

    /**
     * 发起第三方登录授权
     * 流程为: 前端调用该 api, 传递相关参数：第三方类型、接收 code 授权码的回调地址（这里会做回调地址的合法性验证），然后后端发起登录授权
     *        前端在回调地址获取 code, 然后提交给下面的获取第三方用户详情的接口，来触发自身项目体系的登录处理
     *
     * @param Request $request
     * @param WeChatService $weChatService
     * @return mixed
     */
    public function socials(Request $request, WeChatService $weChatService)
    {
        // 获取第三方授权类型
        $socialsType = $weChatService->socialType($request->social_type ?? 'wechat');

        return $weChatService->{"{$socialsType}Oauth"}();
    }

    /**
     * 根据 oauth 返回的 code（授权码）来获取用户详情
     *
     * @param Authorize $handler
     * @return mixed
     * @throws \Exception
     */
    public function getSocialsUser(Authorize $handler)
    {
        return $handler->toResponse();
    }
}
