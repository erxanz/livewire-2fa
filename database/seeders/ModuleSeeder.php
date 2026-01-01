<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'name' => 'User Management',
                'slug' => 'user-management',
                'description' => 'Manage users, roles, and permissions',
                'is_active' => true,
                'settings' => [
                    'allow_self_registration' => true,
                    'require_email_verification' => true,
                    'password_min_length' => 8,
                ],
                'order' => 1,
            ],
            [
                'name' => 'Role Management',
                'slug' => 'role-management',
                'description' => 'Manage roles and assign permissions',
                'is_active' => true,
                'settings' => null,
                'order' => 2,
            ],
            [
                'name' => 'Permission Management',
                'slug' => 'permission-management',
                'description' => 'Manage fine-grained permissions',
                'is_active' => true,
                'settings' => null,
                'order' => 3,
            ],
            [
                'name' => 'Settings',
                'slug' => 'settings',
                'description' => 'Application settings management',
                'is_active' => true,
                'settings' => null,
                'order' => 4,
            ],
            [
                'name' => 'Activity Log',
                'slug' => 'activity-log',
                'description' => 'Track user activities and changes',
                'is_active' => true,
                'settings' => [
                    'retention_days' => 90,
                    'log_login' => true,
                    'log_logout' => true,
                ],
                'order' => 5,
            ],
            [
                'name' => 'Notifications',
                'slug' => 'notifications',
                'description' => 'In-app and email notification system',
                'is_active' => true,
                'settings' => [
                    'enable_email' => true,
                    'enable_in_app' => true,
                ],
                'order' => 6,
            ],
            [
                'name' => 'Backup System',
                'slug' => 'backup',
                'description' => 'Database and file backup management',
                'is_active' => true,
                'settings' => [
                    'auto_backup' => false,
                    'backup_frequency' => 'daily',
                    'retention_count' => 7,
                ],
                'order' => 7,
            ],
            [
                'name' => 'API Access',
                'slug' => 'api',
                'description' => 'RESTful API access for external integrations',
                'is_active' => true,
                'settings' => [
                    'rate_limit' => 60,
                    'rate_limit_period' => 1, // minutes
                ],
                'order' => 8,
            ],
            [
                'name' => 'Reports',
                'slug' => 'reports',
                'description' => 'Generate and export reports',
                'is_active' => false,
                'settings' => [
                    'export_formats' => ['pdf', 'excel', 'csv'],
                ],
                'order' => 9,
            ],
            [
                'name' => 'Audit Trail',
                'slug' => 'audit-trail',
                'description' => 'Complete audit trail for compliance',
                'is_active' => false,
                'settings' => [
                    'track_ip' => true,
                    'track_user_agent' => true,
                ],
                'order' => 10,
            ],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['slug' => $module['slug']],
                $module
            );
        }
    }
}
