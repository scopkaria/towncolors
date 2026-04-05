<?php

namespace App\Mail;

use App\Models\Post;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BlogPublished extends Mailable
{
    use Queueable, SerializesModels;

    public string $excerpt;
    public string $postUrl;

    public function __construct(
        public Post    $post,
        public Setting $setting,
        public string  $recipientEmail,
    ) {
        $this->excerpt = $this->buildExcerpt();
        $this->postUrl = route('blog.show', $post->slug);
    }

    public function envelope(): Envelope
    {
        $from = config('mail.from.address');
        $name = $this->setting->company_name ?: config('app.name');

        return new Envelope(
            subject: $this->post->title . ' — ' . $name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.blog-published',
        );
    }

    private function buildExcerpt(): string
    {
        $plain = strip_tags($this->post->content ?? '');
        $plain = html_entity_decode($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plain = preg_replace('/\s+/', ' ', trim($plain));

        if (mb_strlen($plain) <= 220) {
            return $plain;
        }

        return mb_substr($plain, 0, 220) . '…';
    }
}
