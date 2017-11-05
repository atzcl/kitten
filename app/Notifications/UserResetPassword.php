<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserResetPassword extends Notification
{
    use Queueable;

    /**
     * 重置密码的 token
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return mixed
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * 设置通知的频道为邮件
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * 创建邮件内容
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('您正在进行密码重置，请点击下面的按钮重置密码：')
            ->action('重置密码', url(config('app.url').route('password.reset', $this->token, false)))
            ->line('如果您没有要求重置密码，则不需要进行任何操作。');
    }
}
