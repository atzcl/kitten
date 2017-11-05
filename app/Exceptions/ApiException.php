<?php
/*
+-----------------------------------------------------------------------------------------------------------------------
| Author: 植成樑 <atzcl0310@gmail.com>  Blog：https://www.zcloop.com
+-----------------------------------------------------------------------------------------------------------------------
| API 异常类
|
*/

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    /**
     * 跟 Laravel 的 NotFoundHttpException 、HttpException 异常保持一致的参数
     *
     * @var int
     */
    private $statusCode;

    /**
     * 自定义异常，用于 Api 模块抛出
     *
     * @param string|null $message
     * @param int $code
     */
    public function __construct(int $code = 404, string $message = null)
    {
        $this->statusCode = $code;
        parent::__construct($message, $code);
    }

    /**
     * 获取 statusCode
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * 处理异常
     *
     * @param Exception $e
     */
    public function report(Exception $e)
    {
        dump(111);
    }
}
