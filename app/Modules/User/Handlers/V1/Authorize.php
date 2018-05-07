<?php

declare(strict_types = 1);

/*
+-----------------------------------------------------------------------------------------------------------------------
|
+-----------------------------------------------------------------------------------------------------------------------
| 业务处理器
|
*/

namespace Modules\User\Handlers\V1;

use Modules\User\Services\WeChatService;
use App\Foundation\Abstracts\HandlerAbstract;
use Modules\User\Http\Requests\AuthorizeRequest;

class Authorize extends HandlerAbstract
{
    use AuthorizeRequest;

    /**
     * @var WeChatService
     */
    protected $weChatService;

    /**
     * Authorize constructor.
     *
     * @param WeChatService $weChatService
     */
    public function __construct(WeChatService $weChatService)
    {
        parent::__construct();

        $this->weChatService = $weChatService;
    }

    /**
     * 获取第三方授权用户的详情
     *
     * @return mixed|void
     */
    public function execute()
    {
        // 验证请求是否合法
        $this->executeValidate();

        // 获取第三方授权类型
        $socialsType = $this->weChatService->socialType($request->social_type ?? 'wechat');

        $this->setStatusData(
            $this->weChatService->{"{$socialsType}User"}()
        );
    }
}
