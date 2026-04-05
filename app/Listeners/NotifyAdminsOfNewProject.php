<?php

namespace App\Listeners;

use App\Enums\UserRole;
use App\Events\ProjectCreated;
use App\Models\User;
use App\Notifications\UserAlertNotification;

class NotifyAdminsOfNewProject
{
    public function handle(ProjectCreated $event): void
    {
        $project = $event->project;

        // Eager-load client if not already loaded
        $project->loadMissing('client');

        $clientName = $project->client?->name ?? 'A client';

        User::where('role', UserRole::ADMIN)->get()->each(
            fn (User $admin) => $admin->notify(new UserAlertNotification(
                title:      'New project submitted',
                message:    "{$clientName} submitted a new project: \"{$project->title}\". Please review and assign a freelancer.",
                subject:    'New Project — Action Required',
                actionUrl:  route('admin.projects.show', $project),
                actionText: 'Review Project',
                projectId:  $project->id,
            ))
        );
    }
}
