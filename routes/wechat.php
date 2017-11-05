<?php
/*
+-----------------------------------------------------------------------------------------------------------------------
| Author: 植成樑 <atzcl0310@gmail.com>  Blog：https://www.zcloop.com
+-----------------------------------------------------------------------------------------------------------------------
| 微信 路由规则
|
*/

// 微信入口
Route::any('wechat', 'IndexController@index')->name('wechat.index');

// 模板消息
Route::get('wechat/notify', 'NotifyController@send')->name('wechat.notify.send');
