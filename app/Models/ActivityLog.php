<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the user that performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject model.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo('model');
    }

    /**
     * Log an activity.
     */
    public static function log(
        string $action,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): self {
        $user = Auth::user();
        return static::create([
            'user_id' => $user?->id,
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $description,
        ]);
    }

    /**
     * Scope to filter by action.
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by model type.
     */
    public function scopeForModel($query, string $modelType, ?int $modelId = null)
    {
        $query->where('model_type', $modelType);

        if ($modelId) {
            $query->where('model_id', $modelId);
        }

        return $query;
    }

    /**
     * Scope to get recent logs.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get human-readable action name.
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'login' => 'Logged In',
            'logout' => 'Logged Out',
            'password_reset' => 'Password Reset',
            'role_assigned' => 'Role Assigned',
            'permission_granted' => 'Permission Granted',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    /**
     * Get the changes between old and new values.
     */
    public function getChangesAttribute(): array
    {
        if (!$this->old_values || !$this->new_values) {
            return [];
        }

        $changes = [];
        foreach ($this->new_values as $key => $newValue) {
            $oldValue = $this->old_values[$key] ?? null;
            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    /**
     * Get short model name without namespace.
     */
    public function getModelNameAttribute(): ?string
    {
        if (!$this->model_type) {
            return null;
        }

        return class_basename($this->model_type);
    }
}
