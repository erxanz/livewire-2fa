<?php

use App\Models\Module;
use App\Models\Setting;

if (!function_exists('settings')) {
    /**
     * Get or set a setting value.
     *
     * @param string|null $key Setting key
     * @param mixed $default Default value if key not found
     * @return mixed Setting value or Setting model
     */
    function settings(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return app(Setting::class);
        }

        return Setting::getValue($key, $default);
    }
}

if (!function_exists('set_setting')) {
    /**
     * Set a setting value.
     *
     * @param string $key Setting key
     * @param mixed $value Setting value
     * @param string|null $type Value type (string, boolean, integer, float, json)
     * @return Setting
     */
    function set_setting(string $key, mixed $value, ?string $type = null): Setting
    {
        return Setting::setValue($key, $value, $type);
    }
}

if (!function_exists('module_active')) {
    /**
     * Check if a module is active.
     *
     * @param string $slug Module slug
     * @return bool
     */
    function module_active(string $slug): bool
    {
        return Module::isActive($slug);
    }
}

if (!function_exists('module')) {
    /**
     * Get a module by slug.
     *
     * @param string $slug Module slug
     * @return Module|null
     */
    function module(string $slug): ?Module
    {
        return Module::findBySlug($slug);
    }
}

if (!function_exists('log_activity')) {
    /**
     * Log an activity.
     *
     * @param string $action Action name (created, updated, deleted, etc.)
     * @param \Illuminate\Database\Eloquent\Model|null $model Related model
     * @param array|null $oldValues Old values before change
     * @param array|null $newValues New values after change
     * @param string|null $description Optional description
     * @return \App\Models\ActivityLog
     */
    function log_activity(
        string $action,
        ?\Illuminate\Database\Eloquent\Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): \App\Models\ActivityLog {
        return \App\Models\ActivityLog::log($action, $model, $oldValues, $newValues, $description);
    }
}

if (!function_exists('notify_user')) {
    /**
     * Send in-app notification to a user.
     *
     * @param \App\Models\User $user Target user
     * @param string $title Notification title
     * @param string $message Notification message
     * @param string $type Type: info, success, warning, error
     * @param string|null $actionUrl Action URL
     * @param string|null $actionLabel Action button label
     * @return \App\Models\InAppNotification
     */
    function notify_user(
        \App\Models\User $user,
        string $title,
        string $message,
        string $type = 'info',
        ?string $actionUrl = null,
        ?string $actionLabel = null
    ): \App\Models\InAppNotification {
        return \App\Models\InAppNotification::send($user, $title, $message, $type, $actionUrl, $actionLabel);
    }
}

if (!function_exists('app_name')) {
    /**
     * Get the application name from settings.
     *
     * @return string
     */
    function app_name(): string
    {
        return settings('app.name', config('app.name', 'Laravel'));
    }
}

if (!function_exists('app_logo')) {
    /**
     * Get the application logo URL from settings.
     *
     * @return string|null
     */
    function app_logo(): ?string
    {
        return settings('app.logo');
    }
}

if (!function_exists('app_favicon')) {
    /**
     * Get the application favicon URL from settings.
     *
     * @return string|null
     */
    function app_favicon(): ?string
    {
        return settings('app.favicon');
    }
}
