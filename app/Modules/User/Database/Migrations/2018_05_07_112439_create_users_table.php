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
            $table->uuid('id')->unique();
            $table->string('username', 64)->unique()->nullable()->comment('登录用户名');
            $table->string('phone', 20)->unique()->nullable()->comment('手机'); //
            $table->string('email', 50)->unique()->nullable()->comment('邮箱');

            $table->string('password')->comment('密码');

            // 可以在业务逻辑做唯一验证
            $table->string('real_name', 20)->nullable()->comment('真实姓名');
            $table->string('nickname', 50)->nullable()->comment('昵称');
            $table->string('telephone', 50)->nullable()->comment('座机');
            $table->string('qq', 50)->nullable()->comment('QQ');
            $table->tinyInteger('sex')->default(0)->comment('性别: 0: 未知; 1: 男; 2: 女');
            $table->string('avatar')->nullable()->comment('头像');

            $table->string('contact', 50)->nullable()->comment('联系人');
            $table->string('address', 255)->nullable()->comment('详细地址');
            $table->string('province', 50)->nullable()->comment('省');
            $table->string('city', 50)->nullable()->comment('市');
            $table->string('district', 50)->nullable()->comment('区');
            $table->string('zip', 50)->nullable()->comment('邮编');

            $table->timestamps();
            $table->softDeletes();
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
