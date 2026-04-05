<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Conversation;
use App\Models\Invoice;
use App\Models\Message;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Rule-based AI Assistant.
 *
 * Provides four capabilities:
 *  1. Freelancer matching — suggest the best freelancer for a project's categories.
 *  2. Urgency detection  — detect high-priority keywords in message text.
 *  3. Daily summary      — compile today's platform activity for the admin dashboard.
 *  4. Inactive freelancer detection — find freelancers silent for ≥24 h on active projects.
 */
class AiAssistant
{
    /** Keywords that trigger HIGH priority classification. */
    private const URGENT_KEYWORDS = ['urgent', 'asap', 'fast', 'immediately', 'emergency', 'rush', 'critical'];

    // ─────────────────────────────────────────────────────────────────────────
    // 1. FREELANCER MATCHING
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Given a project (with categories loaded), rank freelancers by how many
     * portfolio items / completed projects share the same categories.
     *
     * Returns a Collection of arrays:
     *   ['freelancer' => User, 'score' => int, 'reason' => string]
     * sorted by score DESC, capped at $limit.
     */
    public function suggestFreelancers(Project $project, int $limit = 3): Collection
    {
        $categoryIds = $project->categories->pluck('id');

        if ($categoryIds->isEmpty()) {
            return collect();
        }

        $freelancers = User::where('role', UserRole::FREELANCER)
            ->get();

        return $freelancers
            ->map(function (User $freelancer) use ($categoryIds) {
                // Score = number of completed projects the freelancer has
                // that share at least one category with this project.
                $completedMatches = Project::where('freelancer_id', $freelancer->id)
                    ->where('status', 'completed')
                    ->whereHas('categories', fn ($q) => $q->whereIn('project_categories.id', $categoryIds))
                    ->count();

                // Secondary score: active projects in the same categories.
                $activeMatches = Project::where('freelancer_id', $freelancer->id)
                    ->whereIn('status', ['assigned', 'in_progress'])
                    ->whereHas('categories', fn ($q) => $q->whereIn('project_categories.id', $categoryIds))
                    ->count();

                $score  = ($completedMatches * 2) + $activeMatches;
                $reason = match (true) {
                    $completedMatches >= 3 => "Delivered {$completedMatches} similar projects",
                    $completedMatches >= 1 => "Has {$completedMatches} completed project(s) in this area",
                    $activeMatches  >= 1   => 'Currently working on a similar project',
                    default                => 'Available freelancer',
                };

                return [
                    'freelancer' => $freelancer,
                    'score'      => $score,
                    'reason'     => $reason,
                ];
            })
            ->filter(fn ($r) => $r['score'] > 0 || $freelancers->count() <= $limit)
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. URGENCY DETECTION
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Return true if the message text contains urgency keywords.
     */
    public function isUrgent(string $text): bool
    {
        $lower = mb_strtolower($text);

        foreach (self::URGENT_KEYWORDS as $keyword) {
            if (str_contains($lower, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the matched keyword(s), or an empty array.
     */
    public function urgencyKeywords(string $text): array
    {
        $lower   = mb_strtolower($text);
        $matched = [];

        foreach (self::URGENT_KEYWORDS as $keyword) {
            if (str_contains($lower, $keyword)) {
                $matched[] = $keyword;
            }
        }

        return $matched;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. DAILY SUMMARY
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Compile today's platform statistics for the AI Insights card.
     *
     * Returns an array with keys:
     *   new_projects_today, pending_invoices, unread_messages,
     *   active_freelancers, urgent_messages_today, insights[]
     */
    public function dailySummary(): array
    {
        $today = now()->startOfDay();

        $newProjectsToday = Project::where('created_at', '>=', $today)->count();

        $pendingInvoices = Invoice::whereIn('status', ['unpaid', 'partial'])->count();

        // Unread = messages sent today not by admin, in active conversations
        $unreadMessages = Message::where('created_at', '>=', $today)
            ->whereHas('sender', fn ($q) => $q->where('role', '!=', UserRole::ADMIN->value))
            ->count();

        $activeFreelancers = User::where('role', UserRole::FREELANCER)
            ->whereIn('id', Project::whereIn('status', ['assigned', 'in_progress'])
                ->whereNotNull('freelancer_id')
                ->pluck('freelancer_id'))
            ->count();

        $urgentMessagesToday = Message::where('created_at', '>=', $today)
            ->where(function ($q) {
                foreach (self::URGENT_KEYWORDS as $kw) {
                    $q->orWhere('message', 'like', "%{$kw}%");
                }
            })
            ->count();

        $insights = $this->buildInsights(
            $newProjectsToday,
            $pendingInvoices,
            $unreadMessages,
            $activeFreelancers,
            $urgentMessagesToday
        );

        return compact(
            'newProjectsToday',
            'pendingInvoices',
            'unreadMessages',
            'activeFreelancers',
            'urgentMessagesToday',
            'insights'
        );
    }

    /**
     * Derive human-readable insight bullets from the daily numbers.
     *
     * Each insight: ['level' => 'info|warn|alert', 'text' => string]
     */
    private function buildInsights(
        int $newProjects,
        int $pendingInvoices,
        int $unread,
        int $activeFreelancers,
        int $urgentMessages
    ): array {
        $insights = [];

        if ($newProjects > 0) {
            $insights[] = [
                'level' => 'info',
                'text'  => "{$newProjects} new project(s) submitted today — assign freelancers promptly.",
            ];
        } else {
            $insights[] = [
                'level' => 'info',
                'text'  => 'No new projects today. A good day to clear pending invoices.',
            ];
        }

        if ($pendingInvoices > 0) {
            $level = $pendingInvoices >= 5 ? 'alert' : 'warn';
            $insights[] = [
                'level' => $level,
                'text'  => "{$pendingInvoices} invoice(s) awaiting payment. Follow up with clients.",
            ];
        }

        if ($urgentMessages > 0) {
            $insights[] = [
                'level' => 'alert',
                'text'  => "{$urgentMessages} message(s) marked urgent today — check conversations now.",
            ];
        }

        if ($activeFreelancers === 0) {
            $insights[] = [
                'level' => 'warn',
                'text'  => 'No freelancers are currently on active projects.',
            ];
        } else {
            $insights[] = [
                'level' => 'info',
                'text'  => "{$activeFreelancers} freelancer(s) currently active on projects.",
            ];
        }

        if ($unread > 0) {
            $insights[] = [
                'level' => 'warn',
                'text'  => "{$unread} new message(s) received today — review conversations.",
            ];
        }

        return $insights;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. INACTIVE FREELANCER DETECTION
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Return freelancers on active projects who have sent no message
     * in the last $hours hours.
     *
     * Each result: ['freelancer' => User, 'project' => Project, 'hours_silent' => int]
     */
    public function inactiveFreelancers(int $hours = 24): Collection
    {
        $cutoff = now()->subHours($hours);

        // Active projects that have an assigned freelancer
        $activeProjects = Project::with(['freelancer'])
            ->whereIn('status', ['assigned', 'in_progress'])
            ->whereNotNull('freelancer_id')
            ->get();

        return $activeProjects->filter(function (Project $project) use ($cutoff) {
            $lastMessage = Message::where('project_id', $project->id)
                ->where('sender_id', $project->freelancer_id)
                ->latest()
                ->first();

            // Inactive if they never messaged OR last message is older than cutoff
            return $lastMessage === null || $lastMessage->created_at->lt($cutoff);
        })->map(function (Project $project) {
            $lastMessage = Message::where('project_id', $project->id)
                ->where('sender_id', $project->freelancer_id)
                ->latest()
                ->first();

            $hoursSilent = $lastMessage
                ? (int) $lastMessage->created_at->diffInHours(now())
                : null;

            return [
                'freelancer'   => $project->freelancer,
                'project'      => $project,
                'hours_silent' => $hoursSilent,
            ];
        })->values();
    }
}
