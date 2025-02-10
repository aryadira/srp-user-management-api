<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserStatusChanged extends Notification
{
    use Queueable;

    protected $status;

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['mail']; // Bisa juga pakai ['database', 'mail', 'slack', dsb.]
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Account Status Has Changed')
            ->line('Your account status has been changed to: ' . ucfirst($this->status))
            ->action('Check Your Account', url('/profile'))
            ->line('If you did not request this change, please contact support.');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Your account status has been changed to: ' . ucfirst($this->status),
        ];
    }
}

