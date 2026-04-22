<?php

namespace App\Enums;

enum AdminRole: string
{
    case Admin      = 'admin';
    case Superadmin = 'superadmin';

    public function label(): string
    {
        return match($this) {
            self::Admin      => 'Администратор',
            self::Superadmin => 'Супер-администратор',
        };
    }

    public function emoji(): string
    {
        return match($this) {
            self::Admin      => '👤',
            self::Superadmin => '👑',
        };
    }

    public function canAddAdmins(): bool
    {
        return $this === self::Superadmin;
    }
}
