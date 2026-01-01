<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Middleware checks are handled via route definition
    }

    /**
     * Display a listing of roles.
     */
    public function index(): JsonResponse
    {
        $roles = Role::withCount(['users', 'permissions'])->get();

        return response()->json([
            'data' => $roles,
        ]);
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        // Assign permissions if provided
        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        // Log activity
        ActivityLog::log('created', $role, null, $role->toArray(), 'Role created via API');

        return response()->json([
            'message' => 'Role created successfully',
            'data' => $role->load('permissions'),
        ], 201);
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): JsonResponse
    {
        return response()->json([
            'data' => $role->load('permissions'),
        ]);
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $oldValues = $role->toArray();

        $role->update($validated);

        // Log activity
        ActivityLog::log('updated', $role, $oldValues, $role->fresh()->toArray(), 'Role updated via API');

        return response()->json([
            'message' => 'Role updated successfully',
            'data' => $role->fresh()->load('permissions'),
        ]);
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role): JsonResponse
    {
        // Prevent deletion of admin role
        if ($role->slug === 'admin') {
            return response()->json([
                'message' => 'Cannot delete the admin role.',
            ], 403);
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete role with assigned users.',
            ], 403);
        }

        $oldValues = $role->toArray();

        // Log activity before deletion
        ActivityLog::log('deleted', $role, $oldValues, null, 'Role deleted via API');

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully',
        ]);
    }

    /**
     * Get role permissions.
     */
    public function permissions(Role $role): JsonResponse
    {
        return response()->json([
            'data' => $role->permissions,
        ]);
    }

    /**
     * Sync role permissions.
     */
    public function syncPermissions(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $oldPermissions = $role->permissions->pluck('id')->toArray();

        $role->syncPermissions($validated['permissions']);

        // Log activity
        ActivityLog::log(
            'permissions_synced',
            $role,
            ['permissions' => $oldPermissions],
            ['permissions' => $validated['permissions']],
            'Role permissions updated via API'
        );

        return response()->json([
            'message' => 'Permissions updated successfully',
            'data' => $role->fresh()->load('permissions'),
        ]);
    }
}
