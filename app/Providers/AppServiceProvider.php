<?php

namespace App\Providers;

use App\Models\Module;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerBladeDirectives();
        $this->shareGlobalVariables();
    }

    /**
     * Register all Blade directives.
     */
    protected function registerBladeDirectives(): void
    {
        // Blade directive untuk cek role
        // Penggunaan: @role('admin') ... @endrole
        Blade::if('role', function (string|array $roles) {
            /** @var User|null $user */
            $user = Auth::user();

            return $user && $user->hasRole($roles);
        });

        // Blade directive untuk cek salah satu dari beberapa role
        // Penggunaan: @hasanyrole(['admin', 'editor']) ... @endhasanyrole
        Blade::if('hasanyrole', function (array $roles) {
            /** @var User|null $user */
            $user = Auth::user();

            return $user && $user->hasAnyRole($roles);
        });

        // Blade directive untuk admin
        // Penggunaan: @admin ... @endadmin
        Blade::if('admin', function () {
            /** @var User|null $user */
            $user = Auth::user();

            return $user && $user->isAdmin();
        });

        // Blade directive untuk cek permission
        // Penggunaan: @permission('create-users') ... @endpermission
        Blade::if('permission', function (string|array $permissions) {
            /** @var User|null $user */
            $user = Auth::user();

            if (!$user) {
                return false;
            }

            if (is_array($permissions)) {
                return $user->hasAnyPermission($permissions);
            }

            return $user->hasPermission($permissions);
        });

        // Blade directive untuk cek semua permission
        // Penggunaan: @permissions(['create-users', 'edit-users']) ... @endpermissions
        Blade::if('permissions', function (array $permissions) {
            /** @var User|null $user */
            $user = Auth::user();

            return $user && $user->hasAllPermissions($permissions);
        });

        // Blade directive untuk cek module aktif
        // Penggunaan: @module('backup') ... @endmodule
        Blade::if('module', function (string $moduleSlug) {
            return Module::isActive($moduleSlug);
        });
    }

    /**
     * Share global variables to all views.
     */
    protected function shareGlobalVariables(): void
    {
        View::composer('*', function ($view) {
            // Share app settings if settings table exists
            try {
                $view->with('appName', function_exists('app_name') ? app_name() : config('app.name'));
                $view->with('appLogo', function_exists('app_logo') ? app_logo() : null);
            } catch (\Exception $e) {
                $view->with('appName', config('app.name'));
                $view->with('appLogo', null);
            }
        });
    }
}
