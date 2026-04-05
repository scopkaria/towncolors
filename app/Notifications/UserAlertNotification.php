<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $title,
        private readonly string $message,
        private readonly string $subject,
        private readonly string $actionUrl,
        private readonly string $actionText,
        private readonly ?int $projectId = null,
        private readonly ?int $invoiceId = null,
        private readonly ?string $note = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
            'project_id' => $this->projectId,
            'invoice_id' => $this->invoiceId,
            'note' => $this->note,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage())
            ->subject($this->subject)
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . ',')
            ->line($this->message);

        if ($this->note) {
            $mail->line('Note: ' . $this->note);
        }

        return $mail
            ->action($this->actionText, $this->actionUrl)
            ->line('This notification was sent from Towncore.');
    }
}
