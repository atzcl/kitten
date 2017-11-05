<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAdminsTable extends Migration
{
    /**
     * 管理员表
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 32)->unique()->comment('账号');
            $table->string('phone', 50)->nullable()->unique();
            $table->string('email', 150)->nullable()->unique();
            $table->string('password');
            $table->string('avatar')->nullable()->default(null)->comment('头像');
            $table->tinyInteger('status')->default(1)->comment('用户状态: 0 禁用; 1 正常');
            $table->integer('login_sum')->unsigned()->default(0)->comment('登录次数');
            $table->rememberToken()->comment('auth token');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_admins');
    }
}
