<?php

namespace App\Http\Controllers\Admin;

use App\Models\UserAdmin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class IndexController extends Controller
{

    public function index(UserAdmin $userAdmin, Role $role, Request $request)
    {
        // dump(Permission::create(['guard_name' => 'admin', 'name' => 'App\Http\Controllers\Admin\V1\Cms\CmsArticleController@index'])); // 创建权限
        // dump(Role::create(['guard_name' => 'admin', 'name' => '超级管理员'])); // 创建角色用户组

        // 把权限分配给角色【也可以分配给用户】
//        $roleName = $role::findByName('超级管理员', 'admin');
//        dump($roleName->givePermissionTo('App\Http\Controllers\Admin\V1\Cms\CmsArticleController@index'));

        // 给用户绑定角色
//
//        $getUser = $userAdmin::find(1);
//        dump($getUser->assignRole('超级管理员'));

        // 判断用户是否有该权限【通过节点来控制】
       // dump(\Auth::guard('admin')->user()->can('App\Http\Controllers\Admin\V1\Cms\CmsArticleController@index'));

        // dump($userAdmin->hasPermissionTo('App\Http\Controllers\Admin\V1\Cms\CmsArticleController@index'));
        return view('admin.index');
    }
}
