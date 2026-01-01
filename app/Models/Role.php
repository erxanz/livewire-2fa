<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Auto-generate slug dari name jika tidak diisi
        static::creating(function ($role) {
            if (empty($role->slug)) {
                $role->slug = Str::slug($role->name);
            }
        });
    }

    /**
     * Get all users that have this role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all permissions for this role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission')->withTimestamps();
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('slug', $permission)->exists();
    }

    /**
     * Check if role has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        return $this->permissions()->whereIn('slug', $permissions)->exists();
    }

    /**
     * Check if role has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        return $this->permissions()->whereIn('slug', $permissions)->count() === count($permissions);
    }

    /**
     * Give permission to role.
     */
    public function givePermission(string|Permission $permission): self
    {
        if (is_string($permission)) {
            $permission = Permission::findBySlug($permission);
        }

        if ($permission && !$this->hasPermission($permission->slug)) {
            $this->permissions()->attach($permission->id);
        }

        return $this;
    }

    /**
     * Give multiple permissions to role.
     */
    public function givePermissions(array $permissions): self
    {
        foreach ($permissions as $permission) {
            $this->givePermission($permission);
        }

        return $this;
    }

    /**
     * Revoke permission from role.
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
     * Sync permissions for role.
     */
    public function syncPermissions(array $permissionIds): self
    {
        $this->permissions()->sync($permissionIds);

        return $this;
    }

    /**
     * Get permission slugs.
     */
    public function getPermissionSlugs(): array
    {
        return $this->permissions()->pluck('slug')->toArray();
    }

    /**
     * Find a role by its slug.
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Find a role by its name.
     */
    public static function findByName(string $name): ?self
    {
        return static::where('name', $name)->first();
    }

    /**
     * Get the admin role.
     */
    public static function admin(): ?self
    {
        return static::findBySlug('admin');
    }

    /**
     * Get the user role.
     */
    public static function user(): ?self
    {
        return static::findBySlug('user');
    }
}
