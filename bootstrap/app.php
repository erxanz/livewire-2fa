<?php

use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\ModuleMiddleware;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register middleware aliases
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'module' => ModuleMiddleware::class,
            'active' => EnsureUserIsActive::class,
        ]);

        // Add active check to web middleware group
        $middleware->appendToGroup('web', EnsureUserIsActive::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
