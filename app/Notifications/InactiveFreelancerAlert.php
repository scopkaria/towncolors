<?php

namespace App\Notifications;

use App\Models\Project;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InactiveFreelancerAlert extends Notification
{
    use Queueable;

    public function __construct(
        public readonly User    $freelancer,
        public readonly Project $project,
        public readonly ?int    $hoursSilent
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $hours = $this->hoursSilent !== null
            ? "{$this->hoursSilent} hour(s)"
            : 'an extended period';

        return (new MailMessage)
            ->subject('⚠️ Inactive Freelancer Alert')
            ->greeting('Hello Admin,')
            ->line("Freelancer **{$this->freelancer->name}** has been silent for {$hours} on project **\"{$this->project->title}\"**.")
            ->line('Please check the conversation or follow up with the freelancer.')
            ->action('View Project', url(route('admin.projects.show', $this->project)))
            ->line('This alert was generated automatically by the AI Assistant.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'            => 'inactive_freelancer',
            'freelancer_id'   => $this->freelancer->id,
            'freelancer_name' => $this->freelancer->name,
            'project_id'      => $this->project->id,
            'project_title'   => $this->project->title,
            'hours_silent'    => $this->hoursSilent,
            'message'         => "Freelancer {$this->freelancer->name} has been silent on \"{$this->project->title}\".",
        ];
    }
}
