<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Role slugs yang diizinkan (bisa multiple, dipisah koma)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        // Check if user has one of the required roles
        if (!$request->user()->hasAnyRole($roles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. You do not have the required role.'], 403);
            }

            abort(403, 'Unauthorized. You do not have the required role.');
        }

        return $next($request);
    }
}
