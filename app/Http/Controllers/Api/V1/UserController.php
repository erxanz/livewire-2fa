<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Middleware checks are handled via route definition
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $roleId = $request->input('role_id');
        $isActive = $request->input('is_active');

        $query = User::with('role')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($roleId, fn($q) => $q->where('role_id', $roleId))
            ->when($isActive !== null, fn($q) => $q->where('is_active', filter_var($isActive, FILTER_VALIDATE_BOOLEAN)))
            ->latest();

        $users = $query->paginate($perPage);

        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::defaults()],
            'role_id' => ['nullable', 'exists:roles,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role_id' => $validated['role_id'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Log activity
        ActivityLog::log('created', $user, null, $user->toArray(), 'User created via API');

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user->load('role'),
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'data' => $user->load('role'),
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['sometimes', 'nullable', Password::defaults()],
            'role_id' => ['nullable', 'exists:roles,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $oldValues = $user->toArray();

        if (isset($validated['password']) && $validated['password']) {
            $validated['password'] = $validated['password'];
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        // Log activity
        ActivityLog::log('updated', $user, $oldValues, $user->fresh()->toArray(), 'User updated via API');

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user->fresh()->load('role'),
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): JsonResponse
    {
        // Prevent self-deletion
        $currentUser = Auth::user();
        if ($user->id === $currentUser?->id) {
            return response()->json([
                'message' => 'You cannot delete your own account.',
            ], 403);
        }

        $oldValues = $user->toArray();

        // Log activity before deletion
        ActivityLog::log('deleted', $user, $oldValues, null, 'User deleted via API');

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Toggle user active status.
     */
    public function toggleActive(User $user): JsonResponse
    {
        // Check permission
        $currentUser = Auth::user();
        if (!$currentUser instanceof User || !$currentUser->hasPermission('edit-users')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Prevent self-deactivation
        if ($user->id === $currentUser?->id) {
            return response()->json([
                'message' => 'You cannot deactivate your own account.',
            ], 403);
        }

        $oldStatus = $user->is_active;
        $user->update(['is_active' => !$user->is_active]);

        // Log activity
        $action = $user->is_active ? 'activated' : 'deactivated';
        ActivityLog::log($action, $user, ['is_active' => $oldStatus], ['is_active' => $user->is_active]);

        return response()->json([
            'message' => 'User ' . $action . ' successfully',
            'data' => $user->fresh()->load('role'),
        ]);
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user): JsonResponse
    {
        // Check permission
        $currentUser = Auth::user();
        if (!$currentUser instanceof User || !$currentUser->hasPermission('edit-users')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'password' => ['required', Password::defaults()],
        ]);

        $user->update([
            'password' => $validated['password'],
        ]);

        // Revoke all tokens
        $user->tokens()->delete();

        // Log activity
        ActivityLog::log('password_reset', $user, null, null, 'User password reset via API');

        return response()->json([
            'message' => 'Password reset successfully',
        ]);
    }
}
