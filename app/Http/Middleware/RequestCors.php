<?php

namespace App\Http\Middleware;

use Closure;

class RequestCors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 允许访问来源
        header('Access-Control-Allow-Origin: *');
        // 允许的跨域请求
        $headers = [
            'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers'=> 'Content-Type, X-Auth-Token, Origin'
        ];
        // 如果请求的类型是 OPTIONS 嗅探,就直接返回成功状态
        if ($request->isMethod('OPTIONS')) {
            return response('OK', 200)->headers($headers);
        }

        return $next($request);
    }
}
