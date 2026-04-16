<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserCredentialsNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $email,
        private readonly string $temporaryPassword,
        private readonly string $loginUrl,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Your Towncore account is ready')
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . ',')
            ->line('An account has been created for you on Towncore.')
            ->line('Email: ' . $this->email)
            ->line('Temporary password: ' . $this->temporaryPassword)
            ->line('For security, you will be required to change this password on your first login.')
            ->action('Login to Towncore', $this->loginUrl)
            ->line('If you were not expecting this account, please contact support.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Account created',
            'message' => 'Your login credentials have been issued. You must change your password on first login.',
            'action_url' => $this->loginUrl,
        ];
    }
}