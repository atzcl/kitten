<?php
/*
+-----------------------------------------------------------------------------------------------------------------------
| User 模块路由规则
+-----------------------------------------------------------------------------------------------------------------------
| 划分版本： v1、v2...
|
*/

Route::prefix('v1')
    // ->middleware('auth.jwt')
    ->namespace('Modules\User\Http\Controllers')
    ->group(function () {
        // 创建用户
        Route::post('users', 'AuthController@store')->name('user.register');
        // 用户登录
        Route::post('users/authorize', 'AuthController@authorize')->name('user.login');
        // 授权登录
        Route::get('socials/{social_type}/authorize', 'AuthController@socials')->name('user.socials');
        // 传递授权 code 来获取用户详情
        Route::post('socials/{social_type}/user', 'AuthController@getSocialsUser')->name('user.socials_user');
    });
