<?php

namespace App\Listeners;

use App\Events\ProjectAssigned;
use App\Notifications\UserAlertNotification;

class NotifyFreelancerOfAssignment
{
    public function handle(ProjectAssigned $event): void
    {
        $event->freelancer->notify(new UserAlertNotification(
            title:      'New project assigned',
            message:    "You have been assigned to project \"{$event->project->title}\". Head over to start reviewing the details.",
            subject:    'You have a new project assignment',
            actionUrl:  route('projects.redirect', $event->project),
            actionText: 'Open Project',
            projectId:  $event->project->id,
        ));
    }
}
