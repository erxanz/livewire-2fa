<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the role that owns the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get permissions assigned directly to user.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permission')->withTimestamps();
    }

    /**
     * Get user's in-app notifications.
     */
    public function inAppNotifications(): HasMany
    {
        return $this->hasMany(InAppNotification::class);
    }

    /**
     * Get user's activity logs.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Check if user has a specific role by slug or name.
     *
     * @param string|array $roles Role slug(s) atau name(s)
     */
    public function hasRole(string|array $roles): bool
    {
        if (!$this->role) {
            return false;
        }

        $roles = is_array($roles) ? $roles : [$roles];

        foreach ($roles as $role) {
            if ($this->role->slug === $role || $this->role->name === $role) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user has any of the given roles.
     *
     * @param array $roles Array of role slugs or names
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->hasRole($roles);
    }

    /**
     * Check if user has a specific permission (via role or direct).
     */
    public function hasPermission(string $permission): bool
    {
        // Check direct permission
        if ($this->permissions()->where('slug', $permission)->exists()) {
            return true;
        }

        // Check role permission
        if ($this->role && $this->role->hasPermission($permission)) {
            return true;
        }

        return false;
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Give permission directly to user.
     */
    public function givePermission(string|Permission $permission): self
    {
        if (is_string($permission)) {
            $permission = Permission::findBySlug($permission);
        }

        if ($permission && !$this->permissions()->where('slug', $permission->slug)->exists()) {
            $this->permissions()->attach($permission->id);
        }

        return $this;
    }

    /**
     * Revoke permission from user.
     */
    public function revokePermission(string|Permission $permission): self
    {
        if (is_string($permission)) {
            $permission = Permission::findBySlug($permission);
        }

        if ($permission) {
            $this->permissions()->detach($permission->id);
        }

        return $this;
    }

    /**
     * Sync user's direct permissions.
     */
    public function syncPermissions(array $permissionIds): self
    {
        $this->permissions()->sync($permissionIds);

        return $this;
    }

    /**
     * Get all permissions (from role + direct).
     */
    public function getAllPermissions(): array
    {
        $permissions = $this->permissions()->pluck('slug')->toArray();

        if ($this->role) {
            $rolePermissions = $this->role->getPermissionSlugs();
            $permissions = array_unique(array_merge($permissions, $rolePermissions));
        }

        return $permissions;
    }

    /**
     * Get the user's role name.
     */
    public function getRoleName(): ?string
    {
        return $this->role?->name;
    }

    /**
     * Get the user's role slug.
     */
    public function getRoleSlug(): ?string
    {
        return $this->role?->slug;
    }

    /**
     * Assign a role to the user by slug or Role instance.
     *
     * @param string|Role $role Role slug atau Role instance
     */
    public function assignRole(string|Role $role): self
    {
        if (is_string($role)) {
            $role = Role::findBySlug($role);
        }

        if ($role) {
            $this->role_id = $role->id;
            $this->save();
        }

        return $this;
    }

    /**
     * Remove role from user.
     */
    public function removeRole(): self
    {
        $this->role_id = null;
        $this->save();

        return $this;
    }

    /**
     * Scope to get only active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->is_active ?? true;
    }

    /**
     * Activate user.
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Deactivate user.
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Update last login info.
     */
    public function updateLastLogin(): bool
    {
        return $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }

    /**
     * Get unread notifications count.
     */
    public function getUnreadNotificationsCount(): int
    {
        return $this->inAppNotifications()->unread()->count();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsAsRead(): int
    {
        return $this->inAppNotifications()
            ->unread()
            ->update(['read_at' => now()]);
    }
}
