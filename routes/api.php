<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialRecordController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes return JSON. No logic lives here — routes only point to
| controller methods. Middleware handles auth and role enforcement.
|
*/

// ── Health Check Route ─────────────────────────────────────────────────────
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
        'environment' => app()->environment(),
        'app_key' => config('app.key') ? 'set' : 'missing',
        'sanctum_config' => config('sanctum') ? 'loaded' : 'missing'
    ]);
});

// ── Test Route (no auth) ─────────────────────────────────────────────────────
Route::get('/test', function () {
    return response()->json([
        'message' => 'Test endpoint working!',
        'timestamp' => now()->toISOString(),
    ]);
});

// ── Debug Route (remove in production) ───────────────────────────────────────
Route::get('/debug', function () {
    return response()->json([
        'env_vars' => [
            'APP_KEY' => config('app.key') ? 'set' : 'missing',
            'APP_URL' => config('app.url'),
            'DB_CONNECTION' => config('database.default'),
            'APP_ENV' => config('app.env'),
        ],
        'last_error' => error_get_last(),
        'laravel_version' => app()->version(),
        'loaded_config_files' => [
            'app' => file_exists(config_path('app.php')),
            'database' => file_exists(config_path('database.php')),
        ],
        'current_working_directory' => getcwd(),
        'env_file_exists' => file_exists(base_path('.env')),
        'env_file_contents' => file_exists(base_path('.env')) ? 'exists' : 'missing',
    ]);
});

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

    // Delete, Trashed, Restore — admin only (must come before {record} routes)
    Route::middleware('role:admin')->group(function () {
        Route::get('/records/trashed', [FinancialRecordController::class, 'trashed']);
        Route::delete('/records/{record}', [FinancialRecordController::class, 'destroy']);
        Route::post('/records/{id}/restore', [FinancialRecordController::class, 'restore']);
    });

    // Read — all authenticated roles (viewer, analyst, admin)
    Route::get('/records', [FinancialRecordController::class, 'index']);
    Route::get('/records/{record}', [FinancialRecordController::class, 'show']);

    // Create & Update — analyst and admin only
    Route::middleware('role:admin,analyst')->group(function () {
        Route::post('/records', [FinancialRecordController::class, 'store']);
        Route::put('/records/{record}', [FinancialRecordController::class, 'update']);
        Route::patch('/records/{record}', [FinancialRecordController::class, 'update']);
    });

    // ── Dashboard — Analyst and Admin Only ────────────────────────────────
    Route::middleware('role:admin,analyst')->prefix('dashboard')->group(function () {
        Route::get('/summary', [DashboardController::class, 'summary']);
        Route::get('/trends', [DashboardController::class, 'trends']);
        Route::get('/categories', [DashboardController::class, 'categories']);
        Route::get('/recent', [DashboardController::class, 'recent']);
    });
});