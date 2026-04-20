<?php

namespace App\Console\Commands;

use App\Models\LiveChatSession;
use Illuminate\Console\Command;

class CloseInactiveLiveChatSessions extends Command
{
    protected $signature = 'livechat:close-inactive {--minutes=30 : Minutes of inactivity before auto-close}';
    protected $description = 'Auto-close live chat sessions that have been inactive';

    public function handle()
    {
        $minutes = (int) $this->option('minutes');
        $cutoff = now()->subMinutes($minutes);

        $sessions = LiveChatSession::whereIn('status', ['waiting', 'active'])
            ->where(function ($query) use ($cutoff) {
                $query->whereDoesntHave('messages')
                      ->where('created_at', '<', $cutoff);
            })
            ->orWhere(function ($query) use ($cutoff) {
                $query->whereIn('status', ['waiting', 'active'])
                      ->whereHas('messages', function ($q) use ($cutoff) {
                          $q->havingRaw('MAX(created_at) < ?', [$cutoff]);
                      });
            })
            ->get();

        // Simpler approach: check each session's last activity
        $closedCount = 0;
        $activeSessions = LiveChatSession::whereIn('status', ['waiting', 'active'])->get();

        foreach ($activeSessions as $session) {
            $lastMessage = $session->messages()->latest()->first();
            $lastActivity = $lastMessage ? $lastMessage->created_at : $session->created_at;

            if ($lastActivity->lt($cutoff)) {
                $session->update([
                    'status' => 'closed',
                    'closed_at' => now(),
                ]);
                $closedCount++;
            }
        }

        $this->info("Closed {$closedCount} inactive live chat sessions.");
    }
}
