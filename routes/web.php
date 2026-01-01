<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Routes yang hanya bisa diakses oleh user dengan role 'admin'.
| Menggunakan middleware 'role:admin' untuk membatasi akses.
|
*/
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::view('/users', 'admin.users')->name('users');
    Route::view('/roles', 'admin.roles')->name('roles');
});

/*
|--------------------------------------------------------------------------
| Example Routes dengan Multiple Roles
|--------------------------------------------------------------------------
|
| Contoh penggunaan middleware role dengan multiple roles.
| User dengan salah satu role yang disebutkan akan mendapat akses.
|
*/
// Route::middleware(['auth', 'verified', 'role:admin,editor'])->prefix('content')->name('content.')->group(function () {
//     Route::view('/', 'content.dashboard')->name('dashboard');
// });

// Route::middleware(['auth', 'verified', 'role:admin,moderator'])->prefix('moderation')->name('moderation.')->group(function () {
//     Route::view('/', 'moderation.dashboard')->name('dashboard');
// });
