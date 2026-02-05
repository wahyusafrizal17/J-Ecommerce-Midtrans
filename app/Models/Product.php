<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price_amount',
        'stock',
        'is_recommended',
        'sold_count',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_recommended' => 'boolean',
            'is_active' => 'boolean',
            'price_amount' => 'integer',
            'stock' => 'integer',
            'sold_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $product) {
            if (blank($product->slug) && filled($product->name)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeRecommended(Builder $query): Builder
    {
        return $query->where('is_recommended', true);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        // Prefer the explicitly marked primary image, but gracefully fall back
        // to the first image (by sort_order) when legacy data has no primary.
        return $this->hasOne(ProductImage::class)
            ->orderByDesc('is_primary')
            ->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->where('status', 'published');
    }

    public function averageRating(): float
    {
        $avg = (float) $this->reviews()->avg('rating');

        return round($avg, 1);
    }

    public function displayPrice(): string
    {
        return 'Rp ' . number_format($this->price_amount, 0, ',', '.');
    }
}

