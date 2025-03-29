<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;
    public $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(config('app.url') . route('password.reset', ['token' => $this->token, 'email' => $this->email], false));

        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe')
            ->view('emails.reset_password', ['url' => $url, 'token' => $this->token, 'email' => $this->email]);
    }
}
