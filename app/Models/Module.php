<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'settings',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'order' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($module) {
            if (empty($module->slug)) {
                $module->slug = Str::slug($module->name);
            }
        });
    }

    /**
     * Scope to get only active modules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by order column.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Check if module is active by slug.
     */
    public static function isActive(string $slug): bool
    {
        $module = static::where('slug', $slug)->first();

        return $module ? $module->is_active : false;
    }

    /**
     * Find module by slug.
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Activate module.
     */
    public function activate(): bool
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Deactivate module.
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Toggle module status.
     */
    public function toggle(): bool
    {
        return $this->update(['is_active' => !$this->is_active]);
    }

    /**
     * Get module setting.
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Set module setting.
     */
    public function setSetting(string $key, mixed $value): bool
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);

        return $this->update(['settings' => $settings]);
    }

    /**
     * Get all active module slugs.
     */
    public static function getActiveSlugs(): array
    {
        return static::active()->pluck('slug')->toArray();
    }

    /**
     * Create module if not exists.
     */
    public static function findOrCreate(string $name, ?string $description = null, bool $isActive = true): self
    {
        $slug = Str::slug($name);

        return static::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'description' => $description,
                'is_active' => $isActive,
                'order' => static::max('order') + 1,
            ]
        );
    }
}
