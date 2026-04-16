<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiring extends Notification
{
    use Queueable;

    public function __construct(private readonly Subscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'      => 'Subscription expiring soon',
            'message'    => "Your {$this->subscription->plan->name} plan expires on {$this->subscription->expiry_date->format('M d, Y')}.",
            'action_url' => url('/client/subscription'),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $days    = $this->subscription->daysUntilExpiry();
        $plan    = $this->subscription->plan->name;
        $expires = $this->subscription->expiry_date->format('M d, Y');

        return (new MailMessage())
            ->subject("Your {$plan} subscription expires in {$days} day(s)")
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . ',')
            ->line("Your **{$plan}** subscription will expire on **{$expires}** ({$days} day(s) remaining).")
            ->line('Renew now to keep uninterrupted access to all your features.')
            ->action('Manage Subscription', url('/client/subscription'))
            ->line('Thank you for using Towncore.');
    }
}
