<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * 处理后端登录的中间件 (参考 RedirectIfAuthenticated.php)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param string|null $guard 看守器名称
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // 其实 $guard 直接指定就行了，没必要用参数传入
        // 判断是否登录，如果没有登录就跳转 404 页面，为什么不直接跳转到登录页面，是为了不暴露登录入口
        if (!Auth::guard($guard ?? 'admin')->check() && $request->url() !== route('admin.login')) {
            // 抛出异常，进入 404 页面
            abort(404);
        } elseif (Auth::guard($guard ?? 'admin')->check() && $request->url() === route('admin.login')) {
            // 如果已经登录，那么就不允许再进入登录页面
            return redirect()->guest(route('admin.index'));
        }

        return $next($request);
    }
}
