<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'vendor_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'product_image',
        'variant_label',
        'quantity',
        'unit_price',
        'commission_rate',
        'commission_amount',
        'vendor_amount',
        'status',
        'tracking_number',
        'shipped_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'unit_price'        => 'decimal:2',
            'commission_rate'   => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'vendor_amount'     => 'decimal:2',
            'shipped_at'        => 'datetime',
            'delivered_at'      => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // ── Helpers ───────────────────────────────────────────

    public function getLineTotalAttribute(): float
    {
        return $this->unit_price * $this->quantity;
    }
}
