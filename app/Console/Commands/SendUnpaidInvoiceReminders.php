<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Notifications\UserAlertNotification;
use Illuminate\Console\Command;

class SendUnpaidInvoiceReminders extends Command
{
    protected $signature = 'invoices:send-reminders
                            {--days=2 : Minimum days since invoice creation before sending a reminder}';

    protected $description = 'Notify clients of invoices that have been unpaid or partially paid for the specified number of days.';

    public function handle(): int
    {
        $days      = (int) $this->option('days');
        $threshold = now()->subDays($days);

        /** @var \Illuminate\Database\Eloquent\Collection $invoices */
        $invoices = Invoice::with(['project.client'])
            ->whereIn('status', ['unpaid', 'partial'])
            ->where('created_at', '<=', $threshold)
            ->get();

        if ($invoices->isEmpty()) {
            $this->info("No overdue invoices found (threshold: {$days} day(s)).");
            return self::SUCCESS;
        }

        $sent = 0;

        foreach ($invoices as $invoice) {
            $client = $invoice->project?->client;

            if (! $client) {
                $this->warn("  Invoice #{$invoice->id} has no linked client — skipped.");
                continue;
            }

            $daysOld   = (int) $invoice->created_at->diffInDays(now());
            $amount    = 'TZS ' . number_format((float) $invoice->total_amount, 0);
            $remaining = 'TZS ' . number_format((float) $invoice->remaining_amount, 0);
            $title     = $invoice->project->title;

            $client->notify(new UserAlertNotification(
                title:      'Invoice payment reminder',
                message:    "Your invoice for project \"{$title}\" ({$amount}) has been outstanding for {$daysOld} day(s). Remaining balance: {$remaining}. Please settle at your earliest convenience.",
                subject:    "Payment reminder — {$title}",
                actionUrl:  route('invoices.show', $invoice),
                actionText: 'View Invoice',
                projectId:  $invoice->project_id,
                invoiceId:  $invoice->id,
            ));

            $this->line("  Reminded <comment>{$client->name}</comment> for invoice #{$invoice->id} ({$daysOld}d, {$remaining} remaining).");
            $sent++;
        }

        $this->info("Sent {$sent} reminder(s) out of {$invoices->count()} matching invoice(s).");

        return self::SUCCESS;
    }
}
