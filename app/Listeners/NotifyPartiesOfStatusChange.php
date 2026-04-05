<?php

namespace App\Listeners;

use App\Enums\UserRole;
use App\Events\ProjectStatusChanged;
use App\Models\User;
use App\Notifications\UserAlertNotification;

class NotifyPartiesOfStatusChange
{
    public function handle(ProjectStatusChanged $event): void
    {
        // Nothing to do if status did not change
        if ($event->newStatus === $event->oldStatus) {
            return;
        }

        $project     = $event->project;
        $statusLabel = ucwords(str_replace('_', ' ', $event->newStatus));

        $project->loadMissing(['client', 'freelancer']);

        match ($event->newStatus) {
            'in_progress' => $this->notifyClient($project, $statusLabel),
            'completed'   => $this->notifyClientAndAdmins($project, $statusLabel, $event->triggeredByUserId),
            default       => null,
        };
    }

    // ── private helpers ──────────────────────────────────────────────────────

    private function notifyClient(Project $project, string $statusLabel): void
    {
        $client = $project->client;
        if (! $client) {
            return;
        }

        $freelancerName = $project->freelancer?->name ?? 'Your freelancer';

        $client->notify(new UserAlertNotification(
            title:      'Project status update',
            message:    "{$freelancerName} has started working on \"{$project->title}\". Status is now {$statusLabel}.",
            subject:    'Your project is now In Progress',
            actionUrl:  route('client.projects.show', $project),
            actionText: 'View Project',
            projectId:  $project->id,
        ));
    }

    private function notifyClientAndAdmins(Project $project, string $statusLabel, ?int $triggeredById): void
    {
        // Notify client
        $project->client?->notify(new UserAlertNotification(
            title:      'Project completed',
            message:    "Great news! Your project \"{$project->title}\" has been marked as {$statusLabel}. Please review the deliverables.",
            subject:    'Your project is now Completed',
            actionUrl:  route('client.projects.show', $project),
            actionText: 'Review Project',
            projectId:  $project->id,
        ));

        // Notify all admins (skip the one who triggered the change)
        User::where('role', UserRole::ADMIN)
            ->when($triggeredById, fn ($q) => $q->where('id', '!=', $triggeredById))
            ->get()
            ->each(fn (User $admin) => $admin->notify(new UserAlertNotification(
                title:      'Project completed',
                message:    "Project \"{$project->title}\" has been marked as {$statusLabel}. Review it to close outstanding invoices.",
                subject:    'A project has been completed',
                actionUrl:  route('admin.projects.show', $project),
                actionText: 'Review Project',
                projectId:  $project->id,
            )));
    }
}
