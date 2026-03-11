<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue    = Order::where('payment_status', 'paid')->sum('total_amount');
        $todayRevenue    = Order::where('payment_status', 'paid')->whereDate('created_at', today())->sum('total_amount');
        $pendingVendors  = Vendor::where('status', 'pending')->count();
        $pendingOrders   = Order::where('status', 'pending')->count();

        return [
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('$' . number_format($todayRevenue, 2) . ' today')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart(
                    Order::where('payment_status', 'paid')
                        ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->take(7)
                        ->pluck('total')
                        ->toArray()
                ),

            Stat::make('Total Orders', Order::count())
                ->description($pendingOrders . ' pending')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning'),

            Stat::make('Total Customers', User::role('customer')->count())
                ->description(User::role('customer')->whereDate('created_at', today())->count() . ' joined today')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Active Vendors', Vendor::where('status', 'approved')->count())
                ->description($pendingVendors . ' awaiting approval')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color($pendingVendors > 0 ? 'warning' : 'success'),

            Stat::make('Total Products', Product::where('status', 'active')->count())
                ->description(Product::count() . ' total including drafts')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Commission Earned', '$' . number_format(Order::where('payment_status', 'paid')->sum('commission_amount'), 2))
                ->description('Platform earnings')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
