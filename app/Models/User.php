<?php

namespace App\Models;

use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => RoleEnum::class,
        'status' => StatusEnum::class,
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function financialRecords(): HasMany
    {
        return $this->hasMany(FinancialRecord::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === RoleEnum::Admin;
    }

    public function isAnalyst(): bool
    {
        return $this->role === RoleEnum::Analyst;
    }

    public function isViewer(): bool
    {
        return $this->role === RoleEnum::Viewer;
    }

    public function isActive(): bool
    {
        return $this->status === StatusEnum::Active;
    }

    public function hasRole(string|RoleEnum ...$roles): bool
    {
        $roleValues = array_map(
            fn($r) => $r instanceof RoleEnum ? $r->value : $r,
            $roles
        );

        return in_array($this->role->value, $roleValues);
    }
}