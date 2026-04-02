<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use App\Helpers\ApiResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     * All self-registered users receive the 'viewer' role by default.
     * Only admins can create analyst or admin accounts via /api/users.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role' => RoleEnum::Viewer,
            'status' => StatusEnum::Active,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return ApiResponse::success(
            [
                'user' => new UserResource($user),
                'token' => $token,
            ],
            'Account created successfully.',
            201
        );
    }

    /**
     * Authenticate and return a token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (!$user || !Hash::check($request->validated('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check account status before issuing token
        if ($user->status === StatusEnum::Inactive) {
            return ApiResponse::error(
                'Your account has been deactivated. Please contact an administrator.',
                403
            );
        }

        // Revoke all previous tokens on login — one active session at a time
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return ApiResponse::success(
            [
                'user' => new UserResource($user),
                'token' => $token,
            ],
            'Logged in successfully.'
        );
    }

    /**
     * Revoke the current token only — not all user tokens.
     * This allows multiple device sessions if needed.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Logged out successfully.');
    }

    /**
     * Return the currently authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(
            new UserResource($request->user()),
            'Authenticated user retrieved.'
        );
    }
}