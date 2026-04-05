<?php

namespace App\Notifications;

use App\Models\FreelancerInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FreelancerInvoiceStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(private readonly FreelancerInvoice $freelancerInvoice)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $status = $this->freelancerInvoice->statusLabel();
        $projectTitle = $this->freelancerInvoice->project?->title ?? 'Project';

        return [
            'freelancer_invoice_id' => $this->freelancerInvoice->id,
            'project_title' => $projectTitle,
            'status' => $this->freelancerInvoice->status,
            'title' => 'Invoice ' . $status,
            'message' => 'Your invoice for "' . $projectTitle . '" was ' . strtolower($status) . '.',
            'action_url' => route('freelancer.freelancerInvoices.index'),
        ];
    }
}
