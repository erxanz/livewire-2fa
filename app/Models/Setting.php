<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get setting value with proper casting.
     */
    public function getCastedValueAttribute(): mixed
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'float' => (float) $this->value,
            'array', 'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    /**
     * Get a setting value by key.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::find($key);

        if (!$setting) {
            return $default;
        }

        return $setting->casted_value;
    }

    /**
     * Set a setting value.
     */
    public static function setValue(string $key, mixed $value, ?string $type = null): self
    {
        $setting = static::find($key);

        if ($setting) {
            // Convert array/json to string
            if (is_array($value)) {
                $value = json_encode($value);
                $type = $type ?? 'json';
            }

            $setting->update([
                'value' => $value,
                'type' => $type ?? $setting->type,
            ]);

            return $setting;
        }

        // Create new setting
        if (is_array($value)) {
            $value = json_encode($value);
            $type = 'json';
        }

        return static::create([
            'key' => $key,
            'value' => $value,
            'type' => $type ?? 'string',
        ]);
    }

    /**
     * Get all settings as key-value array.
     */
    public static function getAllAsArray(): array
    {
        return static::all()->pluck('casted_value', 'key')->toArray();
    }

    /**
     * Get public settings as key-value array.
     */
    public static function getPublicAsArray(): array
    {
        return static::where('is_public', true)->get()->pluck('casted_value', 'key')->toArray();
    }

    /**
     * Get settings by group.
     */
    public static function getByGroup(string $group): array
    {
        return static::where('group', $group)->get()->pluck('casted_value', 'key')->toArray();
    }

    /**
     * Get settings grouped.
     */
    public static function getGrouped(): array
    {
        return static::orderBy('group')->get()->groupBy('group')->toArray();
    }
}
