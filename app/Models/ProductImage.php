<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'path',
        'sort_order',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_primary' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function url(): string
    {
        $relativePath = ltrim((string) $this->path, '/');

        // Preferred: files stored directly under /public (e.g. products/xyz.jpg → /products/xyz.jpg)
        // This avoids relying on `file_exists()` which can be restricted in some shared host environments.
        if ($relativePath !== '' && Str::startsWith($relativePath, ['products/', 'products\\'])) {
            return asset(str_replace('\\', '/', $relativePath));
        }

        // Backward compatibility: older records stored under storage/app/public (served via /storage symlink)
        return asset('storage/' . $relativePath);
    }
}

