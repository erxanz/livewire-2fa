<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Middleware checks are handled via route definition
    }

    /**
     * Display a listing of permissions.
     */
    public function index(): JsonResponse
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get();

        // Group by category
        $grouped = $permissions->groupBy('group');

        return response()->json([
            'data' => $permissions,
            'grouped' => $grouped,
        ]);
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission): JsonResponse
    {
        return response()->json([
            'data' => $permission->load(['roles', 'users']),
        ]);
    }
}
