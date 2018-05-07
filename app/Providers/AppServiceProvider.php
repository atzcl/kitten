<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Events\QueryExecuted;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 配置 Carbon 本地化预约
        \Carbon\Carbon::setLocale('zh');

        // 打印 SQL 执行语句
//        \DB::listen(function (QueryExecuted $executed) {
//            dump(
//                '-------------- dump sql ------------------',
//                $executed->sql,
//                $executed->bindings,
//                '-------------- dump sql end --------------'
//            );
//        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
