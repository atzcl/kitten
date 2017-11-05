<?php

namespace App\Exceptions;

use Exception;
use App\Traits\Api\ApiResponse;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException; // JWT token 失效异常
use Tymon\JWTAuth\Exceptions\TokenInvalidException; // JWT token 无效异常
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * 不应该做日志记录的异常类型列表
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
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
     * 负责记录日志等内部操作
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * 返回给客户端
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return mixed
     */
    public function render($request, Exception $e)
    {
        $modules = 'Home';
        if ($request->route()) {
            // 获取当前访问模块
            $modules = explode('\\', $request->route()->getActionName())[3];
        }

        if ($e instanceof TokenExpiredException) {
            // 捕获 JWT 抛出的异常, Token 过期或者已失效
            return $this->setCode(401)->failed('token 已失效');
        } elseif ($e instanceof TokenInvalidException) {
            // 捕获 JWT 抛出的异常, 非法无效的 token
            return $this->setCode(401)->failed('无效的 token');
        } elseif (in_array(strtolower($modules), ['api', 'system'], true)) {
            // 将 api、system 模块的所有报错都返回 json

            // 设置返回 code
            $statusCode = 500;
            // 捕获 NotFoundHttpException 跟 HttpException 的 StatusCode 属性值
            if ($e instanceof NotFoundHttpException || $e instanceof HttpException || $e instanceof ApiException) {
                $statusCode = $e->getStatusCode();
            }

            return $this->setCode($statusCode)->failed($e->getMessage());
        }

        return parent::render($request, $e);
    }
}
