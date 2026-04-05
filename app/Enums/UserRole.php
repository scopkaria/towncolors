<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case CLIENT = 'client';
    case FREELANCER = 'freelancer';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::CLIENT => 'Client',
            self::FREELANCER => 'Freelancer',
        };
    }

    public function dashboardPath(): string
    {
        return '/'.$this->value;
    }
}