<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Return high-level financial summary figures.
     *
     * Uses a single query with conditional SUM to avoid multiple round-trips.
     * SUM(CASE WHEN ...) is standard SQL — readable, efficient, and portable.
     */
    public function getSummary(): array
    {
        $overall = DB::table('financial_records')
            ->whereNull('deleted_at')
            ->selectRaw("
                SUM(CASE WHEN type = 'income'  THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expenses,
                COUNT(*) as total_records
            ")
            ->first();

        $thisMonth = DB::table('financial_records')
            ->whereNull('deleted_at')
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->selectRaw("
                SUM(CASE WHEN type = 'income'  THEN amount ELSE 0 END) as this_month_income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as this_month_expenses
            ")
            ->first();

        $totalIncome = (float) ($overall->total_income ?? 0);
        $totalExpenses = (float) ($overall->total_expenses ?? 0);

        return [
            'total_income' => round($totalIncome, 2),
            'total_expenses' => round($totalExpenses, 2),
            'net_balance' => round($totalIncome - $totalExpenses, 2),
            'total_records' => (int) ($overall->total_records ?? 0),
            'this_month_income' => round((float) ($thisMonth->this_month_income ?? 0), 2),
            'this_month_expenses' => round((float) ($thisMonth->this_month_expenses ?? 0), 2),
        ];
    }

    /**
     * Return monthly income vs expense trends.
     *
     * A single SQL query groups by month and type — no PHP loops, no N+1.
     * We manually add WHERE deleted_at IS NULL because DB facade does not
     * respect Eloquent's SoftDeletes global scope.
     *
     * The result is then reshaped in PHP into a frontend-friendly format:
     * each month becomes one object with both income and expense keys.
     */
    public function getTrends(int $months = 6): array
    {
        $dateFormat = DB::getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', date)"
            : "DATE_FORMAT(date, '%Y-%m')";

        $rows = DB::table('financial_records')
            ->whereNull('deleted_at')
            ->where('date', '>=', date('Y-m-d', strtotime("-{$months} months")))
            ->selectRaw("
                {$dateFormat} as month,
                type,
                SUM(amount)  as total,
                COUNT(*)     as count
            ")
            ->groupBy('month', 'type')
            ->orderBy('month', 'asc')
            ->get();

        // Reshape flat rows into one entry per month
        $trends = [];
        foreach ($rows as $row) {
            if (!isset($trends[$row->month])) {
                $trends[$row->month] = [
                    'month' => $row->month,
                    'income' => 0.00,
                    'expense' => 0.00,
                    'count' => 0,
                ];
            }
            $trends[$row->month][$row->type] = round((float) $row->total, 2);
            $trends[$row->month]['count'] += (int) $row->count;
        }

        return array_values($trends);
    }

    /**
     * Return totals broken down by category and type.
     *
     * Single query with GROUP BY category, type.
     * Ordered by total descending so the biggest categories appear first.
     */
    public function getCategoryBreakdown(): array
    {
        $rows = DB::table('financial_records')
            ->whereNull('deleted_at')
            ->selectRaw("
                category,
                type,
                SUM(amount) as total,
                COUNT(*)    as count
            ")
            ->groupBy('category', 'type')
            ->orderByRaw('total DESC')
            ->get();

        // Group by category so frontend gets a single object per category
        $breakdown = [];
        foreach ($rows as $row) {
            if (!isset($breakdown[$row->category])) {
                $breakdown[$row->category] = [
                    'category' => $row->category,
                    'income' => 0.00,
                    'expense' => 0.00,
                    'total' => 0.00,
                ];
            }
            $breakdown[$row->category][$row->type] = round((float) $row->total, 2);
            $breakdown[$row->category]['total'] += round((float) $row->total, 2);
        }

        // Sort by combined total descending
        usort($breakdown, fn($a, $b) => $b['total'] <=> $a['total']);

        return array_values($breakdown);
    }

    /**
     * Return the most recent N financial records with their owner.
     */
    public function getRecentActivity(int $limit = 10): \Illuminate\Support\Collection
    {
        return DB::table('financial_records')
            ->join('users', 'financial_records.user_id', '=', 'users.id')
            ->whereNull('financial_records.deleted_at')
            ->select(
                'financial_records.id',
                'financial_records.amount',
                'financial_records.type',
                'financial_records.category',
                'financial_records.date',
                'financial_records.notes',
                'financial_records.created_at',
                'users.id   as user_id',
                'users.name as user_name',
            )
            ->orderBy('financial_records.date', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'amount' => round((float) $row->amount, 2),
                    'type' => $row->type,
                    'category' => $row->category,
                    'date' => $row->date,
                    'notes' => $row->notes,
                    'created_at' => $row->created_at,
                    'user' => [
                        'id' => $row->user_id,
                        'name' => $row->user_name,
                    ],
                ];
            });
    }
}