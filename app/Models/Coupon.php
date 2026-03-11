<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'vendor_id', 'code', 'type', 'value',
        'min_order_amount', 'max_uses', 'used_count',
        'is_active', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'value'            => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'is_active'        => 'boolean',
            'expires_at'       => 'datetime',
        ];
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function isValid(): bool
    {
        if (! $this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;
        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percentage') {
            return round($subtotal * ($this->value / 100), 2);
        }
        return min($this->value, $subtotal);
    }
}
