<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrator dengan akses penuh ke sistem',
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Pengguna standar dengan akses terbatas',
            ],
            [
                'name' => 'Editor',
                'slug' => 'editor',
                'description' => 'Editor konten dengan akses ke pengelolaan konten',
            ],
            [
                'name' => 'Moderator',
                'slug' => 'moderator',
                'description' => 'Moderator dengan akses moderasi konten',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
