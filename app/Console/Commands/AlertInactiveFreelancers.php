<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\InactiveFreelancerAlert;
use App\Services\AiAssistant;
use Illuminate\Console\Command;

class AlertInactiveFreelancers extends Command
{
    protected $signature   = 'ai:alert-inactive-freelancers
                              {--hours=24 : Number of hours of silence before alerting}';

    protected $description = 'Notify admins about freelancers who have not responded on active projects.';

    public function handle(AiAssistant $ai): int
    {
        $hours    = (int) $this->option('hours');
        $inactive = $ai->inactiveFreelancers($hours);

        if ($inactive->isEmpty()) {
            $this->info("No inactive freelancers found (threshold: {$hours}h).");
            return self::SUCCESS;
        }

        $admins = User::where('role', UserRole::ADMIN)->get();

        foreach ($inactive as $record) {
            $notification = new InactiveFreelancerAlert(
                $record['freelancer'],
                $record['project'],
                $record['hours_silent']
            );

            foreach ($admins as $admin) {
                $admin->notify($notification);
            }

            $label = $record['hours_silent'] !== null
                ? "{$record['hours_silent']}h silent"
                : 'never responded';

            $this->line(
                "  Alerted admins about <comment>{$record['freelancer']->name}</comment> on project #{$record['project']->id} ({$label})."
            );
        }

        $this->info("Sent {$inactive->count()} alert(s) to " . $admins->count() . ' admin(s).');

        return self::SUCCESS;
    }
}
