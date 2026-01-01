<?php

namespace App\Http\Middleware;

use App\Models\Module;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModuleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $moduleSlug  The module slug to check
     */
    public function handle(Request $request, Closure $next, string $moduleSlug): Response
    {
        if (!Module::isActive($moduleSlug)) {
            abort(404, 'This feature is currently not available.');
        }

        return $next($request);
    }
}
