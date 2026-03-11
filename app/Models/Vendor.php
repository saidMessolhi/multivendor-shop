<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Vendor extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $fillable = [
        'user_id',
        'store_name',
        'store_slug',
        'description',
        'logo',
        'banner',
        'commission_rate',
        'status',
        'stripe_account_id',
        'paypal_email',
        'balance',
        'phone',
        'address',
        'city',
        'country',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:2',
            'balance'         => 'decimal:2',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('store_name')
            ->saveSlugsTo('store_slug');
    }

    public function getRouteKeyName(): string
    {
        return 'store_slug';
    }

    // ── Relationships ──────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payouts()
    {
        return $this->hasMany(Payout::class);
    }

    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Product::class);
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // ── Helpers ───────────────────────────────────────────

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isBanned(): bool
    {
        return $this->status === 'banned';
    }

    public function getEffectiveCommissionRate(): float
    {
        return $this->commission_rate ?? (float) config('shop.default_commission_rate', 10);
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->store_name) . '&background=2E5BBA&color=fff&size=128';
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->reviews()->where('is_approved', true)->avg('rating') ?? 0, 1);
    }

    public function getTotalSalesAttribute(): int
    {
        return $this->orderItems()->whereHas('order', fn($q) => $q->where('payment_status', 'paid'))->count();
    }
}
