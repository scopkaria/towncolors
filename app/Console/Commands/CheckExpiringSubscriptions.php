<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\SubscriptionExpiring;
use Illuminate\Console\Command;

class CheckExpiringSubscriptions extends Command
{
    protected $signature   = 'subscriptions:check-expiring {--days=5 : Notify if expiring within this many days}';
    protected $description = 'Send expiry reminders for subscriptions expiring soon';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        $expiring = Subscription::with(['user', 'plan'])
            ->where('status', 'active')
            ->whereBetween('expiry_date', [
                now()->toDateString(),
                now()->addDays($days)->toDateString(),
            ])
            ->get();

        $notified = 0;
        foreach ($expiring as $subscription) {
            // Skip if already notified today for this subscription
            $alreadyNotified = $subscription->user
                ->notifications()
                ->where('type', SubscriptionExpiring::class)
                ->where('created_at', '>=', now()->startOfDay())
                ->exists();

            if ($alreadyNotified) {
                continue;
            }

            $subscription->user->notify(new SubscriptionExpiring($subscription));
            $notified++;
        }

        $this->info("Notified {$notified} user(s) about expiring subscriptions.");

        // Auto-expire overdue active subscriptions
        $expired = Subscription::where('status', 'active')
            ->where('expiry_date', '<', now()->toDateString())
            ->update(['status' => 'expired']);

        if ($expired > 0) {
            $this->info("Marked {$expired} subscription(s) as expired.");
        }

        return self::SUCCESS;
    }
}
