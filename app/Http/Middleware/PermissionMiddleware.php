<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permissions  Comma-separated permission slugs
     * @param  string  $requireAll  'all' to require all permissions, default is 'any'
     */
    public function handle(Request $request, Closure $next, string $permissions, string $requireAll = 'any'): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized. Please login first.');
        }

        $permissionArray = explode('|', $permissions);

        if ($requireAll === 'all') {
            // User must have ALL permissions
            if (!$user->hasAllPermissions($permissionArray)) {
                abort(403, 'You do not have the required permissions to access this resource.');
            }
        } else {
            // User must have ANY of the permissions
            if (!$user->hasAnyPermission($permissionArray)) {
                abort(403, 'You do not have permission to access this resource.');
            }
        }

        return $next($request);
    }
}
