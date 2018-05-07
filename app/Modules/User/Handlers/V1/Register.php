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

use Modules\User\Traits\AuthorizeTrait;
use App\Foundation\Abstracts\HandlerAbstract;
use Modules\User\Repositories\UserRepository;
use Modules\User\Http\Requests\RegisterRequest;

class Register extends HandlerAbstract
{
    use RegisterRequest, AuthorizeTrait;

    /**
     * Register constructor.
     *
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * 如果需要验证码，请通过前置方法来处理
     *
     * @return $this|mixed|self
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function execute()
    {
        // 验证请求数据
        $this->executeValidate();

        $this->setStatusData(
            // 创建数据，并返回创建 id
            $this->repository->create($this->request->all())->id
        );
    }
}
