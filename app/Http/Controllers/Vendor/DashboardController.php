<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $vendor = Auth::user()->vendor;

        $stats = [
            'total_products'    => Product::byVendor($vendor->id)->count(),
            'active_products'   => Product::byVendor($vendor->id)->active()->count(),
            'total_orders'      => OrderItem::where('vendor_id', $vendor->id)->distinct('order_id')->count(),
            'pending_orders'    => OrderItem::where('vendor_id', $vendor->id)->where('status', 'pending')->count(),
            'revenue_total'     => OrderItem::where('vendor_id', $vendor->id)
                ->whereHas('order', fn($q) => $q->where('payment_status', 'paid'))
                ->sum('vendor_amount'),
            'revenue_this_month' => OrderItem::where('vendor_id', $vendor->id)
                ->whereHas('order', fn($q) => $q->where('payment_status', 'paid')->whereMonth('paid_at', now()->month))
                ->sum('vendor_amount'),
            'pending_balance'   => $vendor->balance,
            'total_reviews'     => Review::whereHas('product', fn($q) => $q->where('vendor_id', $vendor->id))->count(),
            'avg_rating'        => $vendor->average_rating,
        ];

        $recentOrders = OrderItem::with(['order.user', 'product'])
            ->where('vendor_id', $vendor->id)
            ->latest()
            ->take(5)
            ->get();

        $topProducts = Product::byVendor($vendor->id)
            ->withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(5)
            ->get();

        // Revenue chart — last 6 months
        $revenueChart = collect(range(5, 0))->map(function ($monthsAgo) use ($vendor) {
            $date = now()->subMonths($monthsAgo);
            return [
                'month'   => $date->format('M Y'),
                'revenue' => OrderItem::where('vendor_id', $vendor->id)
                    ->whereHas('order', fn($q) => $q->where('payment_status', 'paid')
                        ->whereYear('paid_at', $date->year)
                        ->whereMonth('paid_at', $date->month))
                    ->sum('vendor_amount'),
            ];
        });

        return view('vendor.dashboard', compact('vendor', 'stats', 'recentOrders', 'topProducts', 'revenueChart'));
    }
}
