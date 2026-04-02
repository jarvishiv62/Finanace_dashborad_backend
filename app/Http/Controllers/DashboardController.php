<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {
    }

    /**
     * Return overall financial summary figures.
     */
    public function summary(): JsonResponse
    {
        return ApiResponse::success(
            $this->dashboardService->getSummary(),
            'Dashboard summary retrieved successfully.'
        );
    }

    /**
     * Return monthly income vs expense trends.
     * Query param: ?months=6 (default 6, max 24)
     */
    public function trends(Request $request): JsonResponse
    {
        $months = min((int) $request->query('months', 6), 24);

        return ApiResponse::success(
            $this->dashboardService->getTrends($months),
            "Monthly trends for the last {$months} months retrieved successfully."
        );
    }

    /**
     * Return category-wise income and expense totals.
     */
    public function categories(): JsonResponse
    {
        return ApiResponse::success(
            $this->dashboardService->getCategoryBreakdown(),
            'Category breakdown retrieved successfully.'
        );
    }

    /**
     * Return the 10 most recent financial records.
     */
    public function recent(): JsonResponse
    {
        return ApiResponse::success(
            $this->dashboardService->getRecentActivity(10),
            'Recent activity retrieved successfully.'
        );
    }
}