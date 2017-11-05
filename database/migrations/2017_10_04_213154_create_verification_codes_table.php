<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVerificationCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->default(null)->comment('触发该通知的用户id');
            $table->string('channel', 50)->comment('发送的通知类型：短信 sms; 邮件 email');
            $table->string('account', 100)->comment('发送的账号');
            $table->string('code', 20)->comment('发送的验证码');
            $table->tinyInteger('status')->nullable()->default(0)->comment('发送状态： 0 等待发送； 1 已发送 2 发送失败');
            $table->bigInteger('ip')->nullable()->comment('发送方IP');
            $table->timestamp('expired_at')->nullable()->comment('过期时间');
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
        Schema::dropIfExists('verification_codes');
    }
}
