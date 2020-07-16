<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class PasswordResetNotification extends Notification
{
    use Queueable;
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {        
        $urlToResetForm = env('FRONT_APP') . "/password/reset?token=".$this->token;
        return (new MailMessage)
            ->subject(Lang::get('비밀번호 재설정 알림메일'))
            ->line(Lang::get('회원님의 계정에 대한 암호 재설정 요청을 받았기 때문에 이 이메일을 받으셨습니다.'))
            ->action(Lang::get('비밀번호 재설정'), $urlToResetForm)
            ->line(Lang::get('이 비밀번호 재설정 링크는 :count 분 후에 만료됩니다.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(Lang::get('만약 비밀번호 재설정을 요청하지 않으셨다면 아무런 추가 조치가 필요하지 않습니다.'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
