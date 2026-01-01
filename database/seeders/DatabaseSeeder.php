<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles, permissions, settings, dan modules
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            SettingSeeder::class,
            ModuleSeeder::class,
        ]);

        // Dapatkan role untuk user
        $adminRole = Role::findBySlug('admin');
        $userRole = Role::findBySlug('user');

        // Buat admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => 'password',
                'email_verified_at' => now(),
                'role_id' => $adminRole?->id,
                'is_active' => true,
            ]
        );

        // Buat test user biasa
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'email_verified_at' => now(),
                'role_id' => $userRole?->id,
                'is_active' => true,
            ]
        );
    }
}
