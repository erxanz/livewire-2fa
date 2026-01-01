<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'app.name',
                'value' => 'Laravel Starter Template',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Application Name',
                'description' => 'The name of your application',
                'is_public' => true,
            ],
            [
                'key' => 'app.description',
                'value' => 'A powerful Laravel starter template with role-based access control',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Application Description',
                'description' => 'A short description of your application',
                'is_public' => true,
            ],
            [
                'key' => 'app.logo',
                'value' => null,
                'type' => 'string',
                'group' => 'general',
                'label' => 'Application Logo',
                'description' => 'URL or path to your application logo',
                'is_public' => true,
            ],
            [
                'key' => 'app.favicon',
                'value' => null,
                'type' => 'string',
                'group' => 'general',
                'label' => 'Favicon',
                'description' => 'URL or path to your favicon',
                'is_public' => true,
            ],
            [
                'key' => 'app.timezone',
                'value' => 'Asia/Jakarta',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Timezone',
                'description' => 'Default timezone for the application',
                'is_public' => false,
            ],
            [
                'key' => 'app.date_format',
                'value' => 'd M Y',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Date Format',
                'description' => 'Default date format (PHP date format)',
                'is_public' => true,
            ],
            [
                'key' => 'app.datetime_format',
                'value' => 'd M Y H:i',
                'type' => 'string',
                'group' => 'general',
                'label' => 'DateTime Format',
                'description' => 'Default datetime format (PHP date format)',
                'is_public' => true,
            ],

            // Contact Settings
            [
                'key' => 'contact.email',
                'value' => 'admin@example.com',
                'type' => 'string',
                'group' => 'contact',
                'label' => 'Contact Email',
                'description' => 'Primary contact email address',
                'is_public' => true,
            ],
            [
                'key' => 'contact.phone',
                'value' => null,
                'type' => 'string',
                'group' => 'contact',
                'label' => 'Contact Phone',
                'description' => 'Primary contact phone number',
                'is_public' => true,
            ],
            [
                'key' => 'contact.address',
                'value' => null,
                'type' => 'string',
                'group' => 'contact',
                'label' => 'Address',
                'description' => 'Company or organization address',
                'is_public' => true,
            ],

            // Social Media Settings
            [
                'key' => 'social.facebook',
                'value' => null,
                'type' => 'string',
                'group' => 'social',
                'label' => 'Facebook URL',
                'description' => 'Facebook page URL',
                'is_public' => true,
            ],
            [
                'key' => 'social.twitter',
                'value' => null,
                'type' => 'string',
                'group' => 'social',
                'label' => 'Twitter URL',
                'description' => 'Twitter profile URL',
                'is_public' => true,
            ],
            [
                'key' => 'social.instagram',
                'value' => null,
                'type' => 'string',
                'group' => 'social',
                'label' => 'Instagram URL',
                'description' => 'Instagram profile URL',
                'is_public' => true,
            ],

            // Feature Flags
            [
                'key' => 'features.registration',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'features',
                'label' => 'User Registration',
                'description' => 'Allow new users to register',
                'is_public' => false,
            ],
            [
                'key' => 'features.email_verification',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'features',
                'label' => 'Email Verification',
                'description' => 'Require email verification for new users',
                'is_public' => false,
            ],
            [
                'key' => 'features.two_factor_auth',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'features',
                'label' => 'Two-Factor Authentication',
                'description' => 'Enable two-factor authentication',
                'is_public' => false,
            ],
            [
                'key' => 'features.api_access',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'features',
                'label' => 'API Access',
                'description' => 'Enable API access for users',
                'is_public' => false,
            ],

            // Maintenance Settings
            [
                'key' => 'maintenance.enabled',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'maintenance',
                'label' => 'Maintenance Mode',
                'description' => 'Put application in maintenance mode',
                'is_public' => false,
            ],
            [
                'key' => 'maintenance.message',
                'value' => 'We are currently performing scheduled maintenance. Please check back soon.',
                'type' => 'string',
                'group' => 'maintenance',
                'label' => 'Maintenance Message',
                'description' => 'Message to display during maintenance',
                'is_public' => false,
            ],

            // Pagination Settings
            [
                'key' => 'pagination.per_page',
                'value' => '15',
                'type' => 'integer',
                'group' => 'pagination',
                'label' => 'Items Per Page',
                'description' => 'Default number of items per page in lists',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
