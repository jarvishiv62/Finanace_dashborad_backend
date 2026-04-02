<?php

namespace App\Enums;

enum RoleEnum: string
{
    case Viewer = 'viewer';
    case Analyst = 'analyst';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            RoleEnum::Viewer => 'Viewer',
            RoleEnum::Analyst => 'Analyst',
            RoleEnum::Admin => 'Admin',
        };
    }

    public function canCreateRecords(): bool
    {
        return in_array($this, [self::Analyst, self::Admin]);
    }

    public function canManageUsers(): bool
    {
        return $this === self::Admin;
    }

    public function canViewDashboard(): bool
    {
        return in_array($this, [self::Analyst, self::Admin]);
    }
}