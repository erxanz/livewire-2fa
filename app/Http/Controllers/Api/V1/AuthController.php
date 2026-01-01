<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and create token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->isActive()) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        // Update last login
        $user->updateLastLogin();

        // Log activity
        ActivityLog::log('login', $user, null, null, 'User logged in via API');

        // Create token
        $deviceName = $request->device_name ?? 'api-token';
        $token = $user->createToken($deviceName);

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleName(),
                'permissions' => $user->getAllPermissions(),
            ],
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        // Check if registration is enabled
        if (!settings('features.registration', true)) {
            return response()->json([
                'message' => 'Registration is currently disabled.',
            ], 403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'is_active' => true,
        ]);

        // Assign default user role
        $user->assignRole('user');

        // Log activity
        ActivityLog::log('registered', $user, null, null, 'User registered via API');

        // Create token
        $deviceName = $request->device_name ?? 'api-token';
        $token = $user->createToken($deviceName);

        return response()->json([
            'message' => 'Registration successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleName(),
            ],
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Get current authenticated user.
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'role' => [
                'id' => $user->role?->id,
                'name' => $user->role?->name,
                'slug' => $user->role?->slug,
            ],
            'permissions' => $user->getAllPermissions(),
            'is_active' => $user->is_active,
            'last_login_at' => $user->last_login_at,
            'created_at' => $user->created_at,
        ]);
    }

    /**
     * Logout current device.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        // Log activity
        ActivityLog::log('logout', $user, null, null, 'User logged out via API');

        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Logout all devices.
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $user = $request->user();

        // Log activity
        ActivityLog::log('logout_all', $user, null, null, 'User logged out from all devices');

        // Revoke all tokens
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Logged out from all devices successfully',
        ]);
    }
}
