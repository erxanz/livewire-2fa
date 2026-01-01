<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions grouped by category
        $permissions = [
            // User Management
            'users' => [
                ['name' => 'View Users', 'description' => 'Can view list of users'],
                ['name' => 'Create Users', 'description' => 'Can create new users'],
                ['name' => 'Edit Users', 'description' => 'Can edit existing users'],
                ['name' => 'Delete Users', 'description' => 'Can delete users'],
                ['name' => 'Manage User Roles', 'description' => 'Can assign/remove roles from users'],
                ['name' => 'Manage User Permissions', 'description' => 'Can assign/remove permissions from users'],
            ],
            // Role Management
            'roles' => [
                ['name' => 'View Roles', 'description' => 'Can view list of roles'],
                ['name' => 'Create Roles', 'description' => 'Can create new roles'],
                ['name' => 'Edit Roles', 'description' => 'Can edit existing roles'],
                ['name' => 'Delete Roles', 'description' => 'Can delete roles'],
                ['name' => 'Manage Role Permissions', 'description' => 'Can assign/remove permissions from roles'],
            ],
            // Permission Management
            'permissions' => [
                ['name' => 'View Permissions', 'description' => 'Can view list of permissions'],
                ['name' => 'Create Permissions', 'description' => 'Can create new permissions'],
                ['name' => 'Edit Permissions', 'description' => 'Can edit existing permissions'],
                ['name' => 'Delete Permissions', 'description' => 'Can delete permissions'],
            ],
            // Settings Management
            'settings' => [
                ['name' => 'View Settings', 'description' => 'Can view application settings'],
                ['name' => 'Edit Settings', 'description' => 'Can edit application settings'],
            ],
            // Module Management
            'modules' => [
                ['name' => 'View Modules', 'description' => 'Can view list of modules'],
                ['name' => 'Manage Modules', 'description' => 'Can enable/disable modules'],
            ],
            // Activity Log
            'activity-logs' => [
                ['name' => 'View Activity Logs', 'description' => 'Can view activity logs'],
                ['name' => 'Delete Activity Logs', 'description' => 'Can delete activity logs'],
            ],
            // Notifications
            'notifications' => [
                ['name' => 'Send Notifications', 'description' => 'Can send notifications to users'],
                ['name' => 'Manage Notifications', 'description' => 'Can manage notification settings'],
            ],
            // Backup
            'backups' => [
                ['name' => 'View Backups', 'description' => 'Can view list of backups'],
                ['name' => 'Create Backups', 'description' => 'Can create new backups'],
                ['name' => 'Download Backups', 'description' => 'Can download backup files'],
                ['name' => 'Delete Backups', 'description' => 'Can delete backups'],
                ['name' => 'Restore Backups', 'description' => 'Can restore from backups'],
            ],
            // API Management
            'api' => [
                ['name' => 'View API Tokens', 'description' => 'Can view API tokens'],
                ['name' => 'Create API Tokens', 'description' => 'Can create API tokens'],
                ['name' => 'Revoke API Tokens', 'description' => 'Can revoke API tokens'],
            ],
        ];

        // Create all permissions
        foreach ($permissions as $group => $items) {
            foreach ($items as $permission) {
                Permission::findOrCreate(
                    $permission['name'],
                    $group,
                    $permission['description']
                );
            }
        }

        // Assign all permissions to Admin role
        $adminRole = Role::findBySlug('admin');
        if ($adminRole) {
            $allPermissions = Permission::all()->pluck('id')->toArray();
            $adminRole->syncPermissions($allPermissions);
        }

        // Assign limited permissions to Editor role
        $editorRole = Role::findBySlug('editor');
        if ($editorRole) {
            $editorPermissions = Permission::whereIn('slug', [
                'view-users',
                'view-roles',
                'view-permissions',
                'view-activity-logs',
            ])->pluck('id')->toArray();
            $editorRole->syncPermissions($editorPermissions);
        }

        // Assign limited permissions to Moderator role
        $moderatorRole = Role::findBySlug('moderator');
        if ($moderatorRole) {
            $moderatorPermissions = Permission::whereIn('slug', [
                'view-users',
                'edit-users',
                'view-activity-logs',
                'send-notifications',
            ])->pluck('id')->toArray();
            $moderatorRole->syncPermissions($moderatorPermissions);
        }
    }
}
