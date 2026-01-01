<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InAppNotification extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'action_url',
        'action_label',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get only unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope to get only read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope to filter by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): bool
    {
        if ($this->read_at) {
            return false;
        }

        return $this->update(['read_at' => now()]);
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(): bool
    {
        return $this->update(['read_at' => null]);
    }

    /**
     * Check if notification is read.
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Check if notification is unread.
     */
    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    /**
     * Send notification to a user.
     */
    public static function send(
        User $user,
        string $title,
        string $message,
        string $type = 'info',
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        ?array $data = null
    ): self {
        return static::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'action_label' => $actionLabel,
            'data' => $data,
        ]);
    }

    /**
     * Send notification to multiple users.
     */
    public static function sendToMany(
        array $userIds,
        string $title,
        string $message,
        string $type = 'info',
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        ?array $data = null
    ): int {
        $notifications = [];
        $now = now();

        foreach ($userIds as $userId) {
            $notifications[] = [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'action_url' => $actionUrl,
                'action_label' => $actionLabel,
                'data' => $data ? json_encode($data) : null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return static::insert($notifications) ? count($notifications) : 0;
    }

    /**
     * Get icon based on type.
     */
    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'success' => 'check-circle',
            'warning' => 'exclamation-triangle',
            'error', 'danger' => 'x-circle',
            'info' => 'information-circle',
            default => 'bell',
        };
    }

    /**
     * Get color based on type.
     */
    public function getColorAttribute(): string
    {
        return match ($this->type) {
            'success' => 'green',
            'warning' => 'yellow',
            'error', 'danger' => 'red',
            'info' => 'blue',
            default => 'gray',
        };
    }
}
