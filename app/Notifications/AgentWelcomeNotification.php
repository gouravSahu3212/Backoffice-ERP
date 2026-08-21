<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgentWelcomeNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $resetUrl, public string $tempPassword)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Backoffice ERP')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your account has been created by the administrator.')
            ->line('Here are your login credentials:')
            ->line('Username: '.$notifiable->username)
            ->line('Temporary Password: '.$this->tempPassword)
            ->line('Please click the link below to reset your password and access your account.')
            ->action('Reset Password', $this->resetUrl)
            ->line('If you did not expect this, no further action is required.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
