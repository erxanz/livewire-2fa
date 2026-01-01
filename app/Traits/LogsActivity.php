<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    /**
     * Attributes to exclude from activity logs
     * @var array
     */
    protected static array $excludedFromLog = [];

    /**
     * Disable activity logging for this model
     * @var bool
     */
    protected static bool $disableActivityLog = false;

    /**
     * Events to log for this model
     * @var array
     */
    protected static array $loggedEvents = ['created', 'updated', 'deleted'];

    /**
     * Boot the trait.
     */
    protected static function bootLogsActivity(): void
    {
        static::created(function (Model $model) {
            if (static::shouldLogActivity('created')) {
                ActivityLog::log(
                    'created',
                    $model,
                    null,
                    $model->getAttributes(),
                    static::getActivityDescription('created', $model)
                );
            }
        });

        static::updated(function (Model $model) {
            if (static::shouldLogActivity('updated')) {
                $dirty = $model->getDirty();

                // Don't log if no actual changes
                if (empty($dirty)) {
                    return;
                }

                // Get old values for changed attributes
                $oldValues = [];
                foreach (array_keys($dirty) as $key) {
                    $oldValues[$key] = $model->getOriginal($key);
                }

                ActivityLog::log(
                    'updated',
                    $model,
                    $oldValues,
                    $dirty,
                    static::getActivityDescription('updated', $model)
                );
            }
        });

        static::deleted(function (Model $model) {
            if (static::shouldLogActivity('deleted')) {
                ActivityLog::log(
                    'deleted',
                    $model,
                    $model->getAttributes(),
                    null,
                    static::getActivityDescription('deleted', $model)
                );
            }
        });
    }

    /**
     * Get attributes to exclude from logging.
     */
    protected static function getExcludedAttributes(): array
    {
        if (!empty(static::$excludedFromLog)) {
            return static::$excludedFromLog;
        }
        return ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];
    }

    /**
     * Check if activity should be logged.
     */
    protected static function shouldLogActivity(string $event): bool
    {
        // Check if logging is disabled for this model
        if (static::$disableActivityLog) {
            return false;
        }

        // Check if specific events should be logged
        return in_array($event, static::$loggedEvents);
    }

    /**
     * Get activity description.
     */
    protected static function getActivityDescription(string $event, Model $model): ?string
    {
        $modelName = class_basename($model);
        $identifier = $model->getKey();

        return match ($event) {
            'created' => "{$modelName} #{$identifier} was created",
            'updated' => "{$modelName} #{$identifier} was updated",
            'deleted' => "{$modelName} #{$identifier} was deleted",
            default => null,
        };
    }

    /**
     * Log a custom activity.
     */
    public function logActivity(string $action, ?array $oldValues = null, ?array $newValues = null, ?string $description = null): ActivityLog
    {
        return ActivityLog::log($action, $this, $oldValues, $newValues, $description);
    }

    /**
     * Get all activity logs for this model.
     */
    public function activityLogs()
    {
        return ActivityLog::forModel(static::class, $this->getKey())->latest();
    }
}
