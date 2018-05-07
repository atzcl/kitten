<?php

declare(strict_types = 1);

/*
+-----------------------------------------------------------------------------------------------------------------------
| Author: 植成樑 <atzcl0310@gmail.com>  Blog：https://www.atzcl.cn
+-----------------------------------------------------------------------------------------------------------------------
| 每个处理具体业务功能点需要继承基类
|
*/

namespace App\Foundation\Abstracts;

use Validator;
use Exception;
use App\Traits\Api\ApiResponse;
use App\Traits\HandlerExceptions;
use Illuminate\Container\Container;
use App\Foundation\Interfaces\HandlerInterface;

abstract class HandlerAbstract implements HandlerInterface
{
    use ApiResponse, HandlerExceptions;

    /**
     * @var \Illuminate\Support\Facades\DB DB 门面
     */
    protected $db;

    /**
     * @var \Illuminate\Http\Request 请求类
     */
    protected $request;

    /**
     * @var \Illuminate\Container\Container $container 容器类
     */
    protected $container;

    /**
     * @var object repository 实例
     */
    protected $repository;

    /**
     * @var bool 自动提交事务
     */
    protected $autoCommit = false;

    /**
     * @var bool 自动回滚事务
     */
    protected $autoRollBack = false;

    /**
     * @var string 执行前/后置的方法
     */
    protected $handlerMethod = 'execute';

    /**
     * 调用前/后置的方法的返回结果
     */
    protected $handlerResult = [];

    /**
     * HandlerAbstract constructor.
     *
     * @return void
     */
    public function __construct()
    {
        // 获取容器实例
        $this->container = Container::getInstance();
        // 实例化 request
        $this->request = $this->container->make('request');
    }

    /**
     * 返回/创建 DB 实例 [ 做多这个是避免事务用错 ]
     *
     * @return \Illuminate\Support\Facades\DB
     */
    protected function makeDB()
    {
        if (empty($this->db)) {
            $this->db = $this->container->make('db');
        }

        return $this->db;
    }

    /**
     * 创建事务
     *
     * @throws \Exception
     */
    protected function beginTransaction()
    {
        // 开启自动事务提交
        $this->autoCommit = true;

        // 开启自动事务回滚
        $this->autoRollBack = true;

        // 创建事务
        $this->makeDB()->beginTransaction();
    }

    /**
     * 提交事务
     *
     * @throws \Exception
     */
    protected function commitTransaction()
    {
        $this->makeDB()->commit();

        // 将不自动提交事务
        $this->autoCommit = false;
    }

    /**
     * 是否自定提交事务
     *
     * @return $this
     * @throws Exception
     */
    private function isAutoCommit(): self
    {
        // 自动提交事务
        if ($this->autoCommit) {
            $this->commitTransaction();
        }

        return $this;
    }

    /**
     * 回滚事务
     *
     * @throws \Exception
     */
    protected function rollBackTransaction()
    {
        $this->makeDB()->rollBack();

        // 将不自动回滚事务
        $this->autoRollBack = false;
    }

    /**
     * 是否自动回滚事务
     *
     * @return $this
     * @throws Exception
     */
    private function isAutoRollBack(): self
    {
        // 自动回滚事务
        if ($this->autoRollBack) {
            $this->rollBackTransaction();
        }

        return $this;
    }

    /**
     * 前置需要调用方法集合
     *
     * @return array
     */
    public function beforeExecutes(): array
    {
        return [];
    }

    /**
     * 业务逻辑处理
     *
     * @return mixed
     * @throws \Exception
     */
    abstract public function execute();

    /**
     * 后置需要调用方法集合
     *
     * @return array
     */
    public function afterExecutes(): array
    {
        return [];
    }

    /**
     * 需要处理的执行方法集合 [ 后面可能会加上模块别名等，然后查表/缓存 来判断是否有加载该模块，或者是有权限使用该模块（该功能） ]
     *
     * @param array $executes
     * @return mixed
     */
    protected function handle(array $executes)
    {
        if (empty($executes)) {
            return false;
        }

        foreach ($executes as $execute) {
            // 获取类的对象
            $className = is_string($execute) ? $execute : get_class($execute);

            // 判断是否存在
            if (class_exists($className) && method_exists($className, $this->handlerMethod)) {
                // 调用容器类来调用指定 class 的 method,且自动处理 method 的依赖注入
                $this->handlerResult[$className] = $this->container->call("{$className}@{$this->handlerMethod}");
            }
        }
    }

    /**
     * 响应返回
     *
     * @return mixed
     * @throws Exception
     */
    public function toResponse()
    {
        try {
            // 执行前置
            $this->handle($this->beforeExecutes());

            // 执行具体业务
            $this->execute();

            // 执行后置
            $this->handle($this->afterExecutes());

            // 重新设置返回结果
            $this->setStatusMessage($this->parserResult());

            // 返回成功
            if ($this->statusCode === 200 || !empty($this->statusData)) {
                return $this->isAutoCommit()->succeed();
            }

            // 返回失败
            return $this->isAutoRollBack()->failed();
        } catch (Exception $exception) {
            // 获取处理异常自定义 code / message 结果
            list($errorCode, $errorMessage) = $this->handlerExceptions($exception);

            // 返回失败
            return $this->isAutoRollBack()->setStatusCode($errorCode)->failed($errorMessage);
        }
    }

    /**
     * 解析、组装调用结果
     *
     * @return string
     */
    public function parserResult()
    {
        // 合并成功结果 [ 假设需要 ]
        return array_reduce($this->handlerResult, function ($carry, $item) {
            // 简单地合并，真实可能会更加复杂
            return $carry . ',' . $item;
        }, $this->getStatusMessage());
    }

    /**
     * 获取验证规则 [ 需子类实现 ]
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * 获取验证规则的自定义错误信息 [ 需子类实现 ]
     *
     * @return array
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * 验证逻辑处理
     *
     * @return null
     */
    public function executeValidate()
    {
        // 如果没有返回或者返回为空，那么就代表无需验证
        if (count($this->rules()) === 0) {
            return null;
        }

        // 手动创建验证
        $validator = Validator::make($this->request->all(), $this->rules(), $this->messages());

        // 如果验证不通过
        if ($validator->fails()) {
            // 手动抛出第一个错误
            abort(422, $validator->errors()->all()[0]);
        }
    }

    /**
     * 获取注入的 Repository 实例
     *
     * @return \Prettus\Repository\Eloquent\BaseRepository|mixed
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * 获取 request 实例
     *
     * @return \Illuminate\Http\Request|mixed|\Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
