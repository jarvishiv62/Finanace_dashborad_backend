<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use App\Helpers\ApiResponse;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Http\Requests\UpdateUserStatusRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * List all users — paginated, filterable by role and status.
     * Admin only.
     */
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->when($request->query('role'), fn($q, $role) => $q->where('role', $role))
            ->when($request->query('status'), fn($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate($request->query('per_page', 15));

        return ApiResponse::paginated(
            $users->through(fn($user) => new UserResource($user)),
            'Users retrieved successfully.'
        );
    }

    /**
     * Create a new user — admin can assign any role.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role' => RoleEnum::from($request->validated('role')),
            'status' => StatusEnum::Active,
        ]);

        return ApiResponse::success(
            new UserResource($user),
            'User created successfully.',
            201
        );
    }

    /**
     * Show a single user.
     */
    public function show(User $user): JsonResponse
    {
        return ApiResponse::success(
            new UserResource($user),
            'User retrieved successfully.'
        );
    }

    /**
     * Update a user's status (active/inactive).
     *
     * Guards:
     * 1. Admin cannot deactivate themselves.
     * 2. Cannot deactivate the last active admin in the system.
     */
    public function updateStatus(UpdateUserStatusRequest $request, User $user): JsonResponse
    {
        $authUser = $request->user();
        $newStatus = StatusEnum::from($request->validated('status'));

        // Guard: admin cannot deactivate themselves
        if ($authUser->id === $user->id && $newStatus === StatusEnum::Inactive) {
            return ApiResponse::error('You cannot deactivate your own account.', 422);
        }

        // Guard: cannot deactivate the last active admin
        if (
            $user->role === RoleEnum::Admin &&
            $newStatus === StatusEnum::Inactive
        ) {
            $activeAdminCount = User::where('role', RoleEnum::Admin->value)
                ->where('status', StatusEnum::Active->value)
                ->count();

            if ($activeAdminCount <= 1) {
                return ApiResponse::error(
                    'Cannot deactivate the last active admin account.',
                    422
                );
            }
        }

        $user->update(['status' => $newStatus]);

        return ApiResponse::success(
            new UserResource($user->fresh()),
            'User status updated successfully.'
        );
    }

    /**
     * Update a user's role.
     *
     * Guards:
     * 1. Admin cannot demote themselves.
     * 2. Cannot remove the last admin's admin role.
     */
    public function updateRole(UpdateUserRoleRequest $request, User $user): JsonResponse
    {
        $authUser = $request->user();
        $newRole = RoleEnum::from($request->validated('role'));

        // Guard: admin cannot change their own role
        if ($authUser->id === $user->id) {
            return ApiResponse::error('You cannot change your own role.', 422);
        }

        // Guard: cannot demote the last admin
        if (
            $user->role === RoleEnum::Admin &&
            $newRole !== RoleEnum::Admin
        ) {
            $adminCount = User::where('role', RoleEnum::Admin->value)->count();

            if ($adminCount <= 1) {
                return ApiResponse::error(
                    'Cannot demote the last admin account.',
                    422
                );
            }
        }

        $user->update(['role' => $newRole]);

        return ApiResponse::success(
            new UserResource($user->fresh()),
            'User role updated successfully.'
        );
    }
}