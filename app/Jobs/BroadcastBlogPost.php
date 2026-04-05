<?php

namespace App\Jobs;

use App\Mail\BlogPublished;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BroadcastBlogPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Retry once on failure. */
    public int $tries = 2;

    /** Timeout in seconds. */
    public int $timeout = 120;

    public function __construct(public Post $post) {}

    public function handle(): void
    {
        $setting = Setting::instance();

        Subscriber::orderBy('id')->chunk(100, function ($subscribers) use ($setting) {
            foreach ($subscribers as $subscriber) {
                try {
                    Mail::to($subscriber->email)
                        ->send(new BlogPublished($this->post, $setting, $subscriber->email));
                } catch (\Throwable $e) {
                    Log::warning('Newsletter delivery failed', [
                        'email' => $subscriber->email,
                        'post'  => $this->post->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        });
    }
}
