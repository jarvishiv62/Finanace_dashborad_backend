<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\FinancialRecord;
use App\Models\User;

class FinancialRecordPolicy
{
    /**
     * Why we need both Middleware AND Policy:
     *
     * Middleware handles ROUTE-LEVEL access — "can this role even reach this endpoint?"
     * Policy handles RECORD-LEVEL access — "can this specific user act on this specific record?"
     *
     * Middleware alone cannot answer "is this the analyst who created this record?"
     * Policy alone would require duplicating role checks everywhere.
     * Together they form a clean two-layer access control system.
     */

    /**
     * Any authenticated user can view a single record.
     * (Viewers can read — that is their only permitted action.)
     */
    public function view(User $user, FinancialRecord $record): bool
    {
        return true;
    }

    /**
     * Admins and analysts can create records.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(RoleEnum::Admin, RoleEnum::Analyst);
    }

    /**
     * Admin can update any record.
     * Analyst can only update records they created themselves.
     *
     * This is the key record-ownership rule that middleware cannot enforce.
     */
    public function update(User $user, FinancialRecord $record): bool
    {
        if ($user->role === RoleEnum::Admin) {
            return true;
        }

        if ($user->role === RoleEnum::Analyst) {
            return $record->user_id === $user->id;
        }

        return false;
    }

    /**
     * Only admins can delete (soft-delete) records.
     */
    public function delete(User $user, FinancialRecord $record): bool
    {
        return $user->role === RoleEnum::Admin;
    }

    /**
     * Only admins can restore soft-deleted records.
     */
    public function restore(User $user, FinancialRecord $record): bool
    {
        return $user->role === RoleEnum::Admin;
    }

    /**
     * Only admins can permanently hard-delete — not exposed via API currently.
     */
    public function forceDelete(User $user, FinancialRecord $record): bool
    {
        return false; // Intentionally disabled — financial data must not be destroyed
    }
}