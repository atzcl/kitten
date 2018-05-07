<?php

namespace App\Exceptions;

use Exception;
use App\Traits\Api\ApiResponse;
use App\Traits\HandlerExceptions;
use Tymon\JWTAuth\Exceptions\TokenExpiredException; // JWT token 无效异常
use Tymon\JWTAuth\Exceptions\TokenInvalidException; // JWT token 无效异常
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    use ApiResponse, HandlerExceptions;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        TokenExpiredException::class,
        TokenInvalidException::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Exception $exception
     * @return mixed|void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // 获取处理异常自定义 code / message 结果
        list($errorCode, $errorMessage) = $this->handlerExceptions($exception);

        // 返回异常信息
        return $this->setStatusCode($errorCode)->failed($errorMessage);
    }
}
