<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return $this->stats($request);
    }

    public function stats(Request $request)
    {
        $vendor = $request->user()->vendor;

        return response()->json([
            'total_products'     => Product::byVendor($vendor->id)->count(),
            'active_products'    => Product::byVendor($vendor->id)->active()->count(),
            'total_orders'       => OrderItem::where('vendor_id', $vendor->id)->distinct('order_id')->count(),
            'pending_orders'     => OrderItem::where('vendor_id', $vendor->id)->where('status', 'pending')->count(),
            'revenue_total'      => (float) OrderItem::where('vendor_id', $vendor->id)
                ->whereHas('order', fn($q) => $q->where('payment_status', 'paid'))
                ->sum('vendor_amount'),
            'revenue_this_month' => (float) OrderItem::where('vendor_id', $vendor->id)
                ->whereHas('order', fn($q) => $q->where('payment_status', 'paid')->whereMonth('paid_at', now()->month))
                ->sum('vendor_amount'),
            'pending_balance'    => (float) $vendor->balance,
            'avg_rating'         => (float) $vendor->average_rating,
        ]);
    }
}
