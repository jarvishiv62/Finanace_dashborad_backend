<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialRecordController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes return JSON. No logic lives here — routes only point to
| controller methods. Middleware handles auth and role enforcement.
|
*/

// ── Public Auth Routes ─────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// ── Authenticated Routes ───────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'active.user'])->group(function () {

    // Auth — self-service
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // ── User Management — Admin Only ───────────────────────────────────────
    Route::middleware('role:admin')->prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::patch('/{user}/status', [UserController::class, 'updateStatus']);
        Route::patch('/{user}/role', [UserController::class, 'updateRole']);
    });

    // ── Financial Records ──────────────────────────────────────────────────

    // Read — all authenticated roles (viewer, analyst, admin)
    Route::get('/records', [FinancialRecordController::class, 'index']);
    Route::get('/records/{record}', [FinancialRecordController::class, 'show']);

    // Create & Update — analyst and admin only
    Route::middleware('role:admin,analyst')->group(function () {
        Route::post('/records', [FinancialRecordController::class, 'store']);
        Route::put('/records/{record}', [FinancialRecordController::class, 'update']);
        Route::patch('/records/{record}', [FinancialRecordController::class, 'update']);
    });

    // Delete, Trashed, Restore — admin only
    Route::middleware('role:admin')->group(function () {
        Route::delete('/records/{record}', [FinancialRecordController::class, 'destroy']);
        Route::get('/records/admin/trashed', [FinancialRecordController::class, 'trashed']);
        Route::post('/records/{id}/restore', [FinancialRecordController::class, 'restore']);
    });

    // ── Dashboard — Analyst and Admin Only ────────────────────────────────
    Route::middleware('role:admin,analyst')->prefix('dashboard')->group(function () {
        Route::get('/summary', [DashboardController::class, 'summary']);
        Route::get('/trends', [DashboardController::class, 'trends']);
        Route::get('/categories', [DashboardController::class, 'categories']);
        Route::get('/recent', [DashboardController::class, 'recent']);
    });
});