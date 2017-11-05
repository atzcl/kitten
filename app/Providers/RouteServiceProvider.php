<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        // 全局参数约束
        Route::pattern('id', '[0-9]+');
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        // Api 路由
        $this->mapApiRoutes();

        // Web 端路由
        $this->mapWebRoutes();

        // 后端 Api 路由
        $this->mapSystemRoutes();

        // 微信路由
        $this->mapWeChatRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace . '\Api')
             ->group(base_path('routes/api.php'));
    }

    /**
     * 后端 api 路由
     *
     * @return void
     */
    protected function mapSystemRoutes()
    {
        Route::prefix('system')
            ->middleware(['web', 'auth.admin:admin'])
            ->namespace($this->namespace . '\Admin')
            ->group(base_path('routes/system.php'));
    }

    /**
     * 微信端 路由
     *
     * @return void
     */
    protected function mapWeChatRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace . '\WeChat')
            ->group(base_path('routes/wechat.php'));
    }
}
