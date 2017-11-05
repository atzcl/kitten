<?php
/*
+-----------------------------------------------------------------------------------
| Author: 植成樑 <atzcl0310@gmail.com>  Blog：https://www.zcloop.com
+-----------------------------------------------------------------------------------
| Web Routes
|------------------------------------------------------------------------------------
*/

// 后台 web 路由
Route::group(['namespace' => 'Admin', 'middleware' => ['auth.admin:admin']], function () {
    // 登录页面
    Route::get('kt_system_login', 'Auth\LoginController@showLoginForm')->name('admin.login');
    // 处理登录
    Route::post('kt_system_login', 'Auth\LoginController@login');
    // 退出登录
    Route::post('logouts', 'Auth\LoginController@logout')->name('admin.logout');
    // 后台首页
    Route::get('app', 'IndexController@index')->name('admin.index');
});

// 用户登录、注册等接口路由
Route::group(['namespace' => 'Home'], function () {
    Auth::routes();
    Route::get('member', 'IndexController@member')->name('member');
    Route::get('/', 'IndexController@index')->name('index');
});
