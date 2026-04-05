<?php

namespace App\Listeners;

use App\Events\ProjectFileUploaded;
use App\Notifications\UserAlertNotification;

class NotifyClientOfFileUpload
{
    public function handle(ProjectFileUploaded $event): void
    {
        $project = $event->project;

        $project->loadMissing('client');

        $client = $project->client;
        if (! $client) {
            return;
        }

        $count       = $event->fileCount;
        $uploaderName = $event->uploadedBy->name;
        $noun        = $count === 1 ? 'file' : 'files';

        $client->notify(new UserAlertNotification(
            title:      'New deliverable uploaded',
            message:    "{$uploaderName} uploaded {$count} new {$noun} to your project \"{$project->title}\".",
            subject:    'New file uploaded on your project',
            actionUrl:  route('client.projects.show', $project),
            actionText: 'View Project',
            projectId:  $project->id,
        ));
    }
}
