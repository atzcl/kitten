<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->unique();
            $table->string('phone', 50)->unique();
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->string('vita')->nullable()->default(null)->comment('个人简介');
            $table->tinyInteger('sex')->default(0)->comment('性别: 0 女; 1 男; 3 未知');
            $table->string('avatar')->nullable()->default(null)->comment('头像');
            $table->tinyInteger('status')->default(1)->comment('用户状态: 0 禁用; 1 正常');
            $table->integer('login_sum')->unsigned()->default(0)->comment('登录次数');
            $table->bigInteger('created_ip')->comment('注册IP');
            $table->rememberToken()->comment('auth token');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['name', 'phone', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
