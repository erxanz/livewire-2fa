<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'group',
        'description',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($permission) {
            if (empty($permission->slug)) {
                $permission->slug = Str::slug($permission->name);
            }
        });
    }

    /**
     * Get all roles that have this permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permission')->withTimestamps();
    }

    /**
     * Get all users that have this permission directly.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_permission')->withTimestamps();
    }

    /**
     * Find permission by slug.
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Get permissions grouped by group.
     */
    public static function getGrouped(): array
    {
        return static::orderBy('group')->orderBy('name')->get()->groupBy('group')->toArray();
    }

    /**
     * Create permission if not exists.
     */
    public static function findOrCreate(string $name, ?string $group = null, ?string $description = null): self
    {
        $slug = Str::slug($name);

        return static::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'group' => $group,
                'description' => $description,
            ]
        );
    }
}
