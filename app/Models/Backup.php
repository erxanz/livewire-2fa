<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Backup extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'disk',
        'path',
        'size',
        'type',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    /**
     * Get the user who created the backup.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to filter by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get database backups.
     */
    public function scopeDatabase($query)
    {
        return $query->where('type', 'database');
    }

    /**
     * Scope to get file backups.
     */
    public function scopeFiles($query)
    {
        return $query->where('type', 'files');
    }

    /**
     * Scope to get full backups.
     */
    public function scopeFull($query)
    {
        return $query->where('type', 'full');
    }

    /**
     * Get human-readable file size.
     */
    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get full path to backup file.
     */
    public function getFullPathAttribute(): string
    {
        return Storage::disk($this->disk)->path($this->path);
    }

    /**
     * Check if backup file exists.
     */
    public function exists(): bool
    {
        return Storage::disk($this->disk)->exists($this->path);
    }

    /**
     * Delete backup file and record.
     */
    public function deleteBackup(): bool
    {
        if ($this->exists()) {
            Storage::disk($this->disk)->delete($this->path);
        }

        return $this->delete();
    }

    /**
     * Download backup file.
     */
    public function download()
    {
        if (!$this->exists()) {
            throw new \Exception('Backup file not found.');
        }

        return Storage::disk($this->disk)->path($this->path);
    }

    /**
     * Get type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'database' => 'Database',
            'files' => 'Files',
            'full' => 'Full Backup',
            default => ucfirst($this->type),
        };
    }

    /**
     * Get type color for UI.
     */
    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'database' => 'blue',
            'files' => 'green',
            'full' => 'purple',
            default => 'gray',
        };
    }
}
