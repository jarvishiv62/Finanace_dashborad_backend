<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\FilterRecordsRequest;
use App\Http\Requests\StoreFinancialRecordRequest;
use App\Http\Requests\UpdateFinancialRecordRequest;
use App\Http\Resources\FinancialRecordResource;
use App\Models\FinancialRecord;
use App\Services\FinancialRecordService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinancialRecordController extends Controller
{
    public function __construct(
        private readonly FinancialRecordService $recordService
    ) {
    }

    /**
     * List records with optional filters — all authenticated roles can access.
     */
    public function index(FilterRecordsRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 15);

        $paginator = $this->recordService->getFilteredRecords($filters, $perPage);

        return ApiResponse::paginated(
            $paginator->through(fn($record) => new FinancialRecordResource($record)),
            'Records retrieved successfully.'
        );
    }

    /**
     * Show a single record — all authenticated roles.
     */
    public function show(FinancialRecord $record): JsonResponse
    {
        $this->authorize('view', $record);

        $record->load('user');

        return ApiResponse::success(
            new FinancialRecordResource($record),
            'Record retrieved successfully.'
        );
    }

    /**
     * Create a record — admin and analyst only (enforced by middleware + policy).
     */
    public function store(StoreFinancialRecordRequest $request): JsonResponse
    {
        $this->authorize('create', FinancialRecord::class);

        $record = $this->recordService->createRecord(
            $request->validated(),
            $request->user()
        );

        return ApiResponse::success(
            new FinancialRecordResource($record),
            'Record created successfully.',
            201
        );
    }

    /**
     * Update a record.
     * Policy enforces: admin updates any, analyst only updates their own.
     */
    public function update(UpdateFinancialRecordRequest $request, FinancialRecord $record): JsonResponse
    {
        // Check authorization manually
        if (!$request->user()->can('update', $record)) {
            return ApiResponse::error('You do not have permission to perform this action.', 403);
        }

        $updated = $this->recordService->updateRecord($record, $request->validated());

        return ApiResponse::success(
            new FinancialRecordResource($updated),
            'Record updated successfully.'
        );
    }

    /**
     * Soft-delete a record — admin only.
     */
    public function destroy(FinancialRecord $record): JsonResponse
    {
        $this->authorize('delete', $record);

        $this->recordService->deleteRecord($record);

        return ApiResponse::success(null, 'Record deleted successfully.');
    }

    /**
     * List soft-deleted records — admin only.
     */
    public function trashed(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $paginator = $this->recordService->getTrashedRecords($perPage);

        return ApiResponse::paginated(
            $paginator->through(fn($record) => new FinancialRecordResource($record)),
            'Trashed records retrieved successfully.'
        );
    }

    /**
     * Restore a soft-deleted record — admin only.
     */
    public function restore(int $id): JsonResponse
    {
        $record = $this->recordService->restoreRecord($id);

        return ApiResponse::success(
            new FinancialRecordResource($record),
            'Record restored successfully.'
        );
    }
}