<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'rating',
        'title',
        'body',
        'images',
        'is_approved',
        'vendor_reply',
        'vendor_replied_at',
    ];

    protected function casts(): array
    {
        return [
            'rating'            => 'integer',
            'images'            => 'array',
            'is_approved'       => 'boolean',
            'vendor_replied_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
         static::saved(function (Review $review) {

        if (!$review->product) return; // safety

        $product = $review->product;

        $stats = $product->reviews()
            ->where('is_approved', true)
            ->selectRaw('AVG(rating) as avg, COUNT(*) as count')
            ->first();

        $product->update([
            'rating_avg'   => $stats->avg ?? 0,   // ✅ NEVER NULL
            'rating_count' => $stats->count ?? 0,
        ]);
    });

    /*
        static::saved(function (Review $review) {
            $review->product->update([
                'rating_avg'   => $review->product->reviews()->where('is_approved', true)->avg('rating'),
                'rating_count' => $review->product->reviews()->where('is_approved', true)->count(),
            ]);
        });
        */


       
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function isVerifiedPurchase(): bool
    {
        return $this->order_id !== null;
    }
}
