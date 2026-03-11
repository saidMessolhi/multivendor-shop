<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_method',
        'payment_status',
        'subtotal',
        'shipping_amount',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'shipping_address',
        'billing_address',
        'notes',
        'coupon_code',
        'paid_at',
        'stripe_payment_intent_id',
        'paypal_order_id',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'         => 'decimal:2',
            'shipping_amount'  => 'decimal:2',
            'tax_amount'       => 'decimal:2',
            'discount_amount'  => 'decimal:2',
            'total_amount'     => 'decimal:2',
            'shipping_address' => 'array',
            'billing_address'  => 'array',
            'paid_at'          => 'datetime',
        ];
    }

    // ── Boot ──────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->order_number = static::generateOrderNumber();
        });
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . strtoupper(substr(md5(uniqid()), 0, 8));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    // ── Relationships ──────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // ── Helpers ───────────────────────────────────────────

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'yellow',
            'processing' => 'blue',
            'shipped'    => 'purple',
            'delivered'  => 'green',
            'cancelled'  => 'red',
            'refunded'   => 'gray',
            default      => 'gray',
        };
    }
}
