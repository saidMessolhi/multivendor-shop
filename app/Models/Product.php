<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasSlug, Searchable;

    protected $fillable = [
        'vendor_id',
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'sale_price',
        'stock',
        'sku',
        'status',
        'is_featured',
        'weight',
        'images',
        'meta_title',
        'meta_description',
        'rating_avg',
        'rating_count',
    ];

    protected function casts(): array
    {
        return [
            'price'        => 'decimal:2',
            'sale_price'   => 'decimal:2',
            'weight'       => 'decimal:2',
            'rating_avg'   => 'decimal:1',
            'is_featured'  => 'boolean',
            'images'       => 'array',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ── Relationships ──────────────────────────────────────

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    // ── Helpers ───────────────────────────────────────────

    public function getCurrentPriceAttribute(): float
    {
        return $this->sale_price && $this->sale_price < $this->price
            ? (float) $this->sale_price
            : (float) $this->price;
    }

    public function getDiscountPercentageAttribute(): int
    {
        if ($this->sale_price && $this->sale_price < $this->price) {
            return (int) round((($this->price - $this->sale_price) / $this->price) * 100);
        }
        return 0;
    }

    public function isOnSale(): bool
    {
        return $this->sale_price && $this->sale_price < $this->price;
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function getFirstImageUrlAttribute(): string
    {
        $images = $this->images ?? [];
        return count($images) > 0
            ? asset('storage/' . $images[0])
            : asset('images/placeholder.png');
    }

    // ── Scout ─────────────────────────────────────────────

    public function toSearchableArray(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->short_description,
            'category'    => $this->category?->name,
            'vendor'      => $this->vendor?->store_name,
            'price'       => $this->current_price,
            'status'      => $this->status,
        ];
    }
}
