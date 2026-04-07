<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case CLIENT = 'client';
    case FREELANCER = 'freelancer';
    case SUPPORT_AGENT = 'support_agent';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::CLIENT => 'Client',
            self::FREELANCER => 'Freelancer',
            self::SUPPORT_AGENT => 'Support Agent',
        };
    }

    public function dashboardPath(): string
    {
        return match ($this) {
            self::SUPPORT_AGENT => '/admin',
            default => '/'.$this->value,
        };
    }
}