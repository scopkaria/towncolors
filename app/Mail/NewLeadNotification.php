<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Sent to admin when a new lead is submitted. */
class NewLeadNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Lead $lead) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [$this->lead->email],
            subject: 'New Lead: ' . ($this->lead->project_type
                ? \App\Models\Lead::projectTypes()[$this->lead->project_type] ?? $this->lead->project_type
                : 'General Inquiry') . ' from ' . $this->lead->name,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.lead-notification');
    }
}
