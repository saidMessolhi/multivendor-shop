<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = [
        'vendor_id', 'amount', 'method',
        'status', 'reference', 'notes', 'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
