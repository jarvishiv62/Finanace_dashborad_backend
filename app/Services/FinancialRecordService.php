<?php

namespace App\Services;

use App\Models\FinancialRecord;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class FinancialRecordService
{
    /**
     * Return a paginated, filtered list of financial records.
     *
     * Filters are chained using named query scopes defined on the model.
     * Each scope guards internally — passing null simply skips that filter.
     * This means the service reads like plain English and stays easy to extend.
     */
    public function getFilteredRecords(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return FinancialRecord::with('user')
            ->ofType($filters['type'] ?? null)
            ->inCategory($filters['category'] ?? null)
            ->betweenDates($filters['date_from'] ?? null, $filters['date_to'] ?? null)
            ->search($filters['search'] ?? null)
            ->latest('date')
            ->paginate($perPage);
    }

    /**
     * Return a single record with its owner loaded.
     */
    public function findRecord(int $id): FinancialRecord
    {
        // findOrFail throws ModelNotFoundException which is caught globally
        // in bootstrap/app.php and returned as a clean 404 response
        return FinancialRecord::with('user')->findOrFail($id);
    }

    /**
     * Create a new financial record.
     * user_id is always set from the authenticated user — never from the request body.
     * This prevents a user from assigning a record to someone else.
     */
    public function createRecord(array $data, User $user): FinancialRecord
    {
        $record = FinancialRecord::create([
            'user_id' => $user->id,
            'amount' => $data['amount'],
            'type' => $data['type'],
            'category' => $data['category'],
            'date' => $data['date'],
            'notes' => $data['notes'] ?? null,
        ]);

        return $record->load('user');
    }

    /**
     * Update an existing record.
     * Only the fields present in $data are updated (partial update safe).
     */
    public function updateRecord(FinancialRecord $record, array $data): FinancialRecord
    {
        $record->update($data);

        return $record->fresh('user');
    }

    /**
     * Soft-delete a financial record.
     * Sets deleted_at — the record remains in the database for audit purposes.
     * Financial records should never be permanently destroyed.
     */
    public function deleteRecord(FinancialRecord $record): bool
    {
        return $record->delete();
    }

    /**
     * Return paginated soft-deleted records (admin only).
     */
    public function getTrashedRecords(int $perPage = 15): LengthAwarePaginator
    {
        return FinancialRecord::onlyTrashed()
            ->with('user')
            ->latest('deleted_at')
            ->paginate($perPage);
    }

    /**
     * Restore a soft-deleted record (admin only).
     */
    public function restoreRecord(int $id): FinancialRecord
    {
        $record = FinancialRecord::onlyTrashed()->findOrFail($id);
        $record->restore();

        return $record->load('user');
    }
}