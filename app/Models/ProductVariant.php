<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'sku', 'attributes',
        'price', 'stock', 'image',
    ];

    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'price'      => 'decimal:2',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getLabelAttribute(): string
    {
        if (is_array($this->attributes)) {
            return implode(' / ', array_values($this->attributes));
        }
        return '';
    }
}
