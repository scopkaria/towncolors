<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Confirmation email sent to the person who submitted the lead. */
class LeadConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Lead $lead) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We received your inquiry — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.lead-confirmation');
    }
}
