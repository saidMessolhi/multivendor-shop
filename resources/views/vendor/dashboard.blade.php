@extends('layouts.vendor')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $statCards = [
            ['label' => 'Total Revenue',    'value' => '$'.number_format($stats['revenue_total'], 2),    'icon' => 'fas fa-dollar-sign', 'color' => 'green'],
            ['label' => 'This Month',       'value' => '$'.number_format($stats['revenue_this_month'], 2),'icon' => 'fas fa-calendar',    'color' => 'blue'],
            ['label' => 'Total Orders',     'value' => $stats['total_orders'],                            'icon' => 'fas fa-shopping-bag','color' => 'purple'],
            ['label' => 'Pending Orders',   'value' => $stats['pending_orders'],                          'icon' => 'fas fa-clock',       'color' => 'yellow'],
            ['label' => 'Active Products',  'value' => $stats['active_products'],                         'icon' => 'fas fa-box',         'color' => 'indigo'],
            ['label' => 'Pending Balance',  'value' => '$'.number_format($stats['pending_balance'], 2),   'icon' => 'fas fa-wallet',      'color' => 'emerald'],
            ['label' => 'Total Reviews',    'value' => $stats['total_reviews'],                           'icon' => 'fas fa-star',        'color' => 'orange'],
            ['label' => 'Avg Rating',       'value' => number_format($stats['avg_rating'], 1) . ' ★',    'icon' => 'fas fa-award',       'color' => 'pink'],
        ];
        $colorMap = ['green'=>'bg-green-100 text-green-600','blue'=>'bg-blue-100 text-blue-600','purple'=>'bg-purple-100 text-purple-600','yellow'=>'bg-yellow-100 text-yellow-600','indigo'=>'bg-indigo-100 text-indigo-600','emerald'=>'bg-emerald-100 text-emerald-600','orange'=>'bg-orange-100 text-orange-600','pink'=>'bg-pink-100 text-pink-600'];
        @endphp

        @foreach($statCards as $card)
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $card['label'] }}</span>
                <div class="w-9 h-9 rounded-lg flex items-center justify-center {{ $colorMap[$card['color']] }}">
                    <i class="{{ $card['icon'] }} text-sm"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $card['value'] }}</div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Revenue Chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Revenue (Last 6 Months)</h3>
            <canvas id="revenueChart" height="100"></canvas>
        </div>

        {{-- Top Products --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Top Products</h3>
            @forelse($topProducts as $product)
            <div class="flex items-center space-x-3 py-2 border-b border-gray-50 last:border-0">
                <img src="{{ $product->first_image_url }}" class="w-10 h-10 rounded-lg object-cover bg-gray-100">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $product->name }}</p>
                    <p class="text-xs text-gray-500">{{ $product->order_items_count }} sales</p>
                </div>
                <span class="text-sm font-semibold text-blue-600">${{ number_format($product->price, 2) }}</span>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">No products yet</p>
            @endforelse
            <a href="{{ route('vendor.products.index') }}" class="block mt-4 text-center text-sm text-blue-600 hover:underline">View all products →</a>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-800">Recent Orders</h3>
            <a href="{{ route('vendor.orders.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 uppercase tracking-wide border-b">
                        <th class="pb-3">Order</th>
                        <th class="pb-3">Customer</th>
                        <th class="pb-3">Product</th>
                        <th class="pb-3">Amount</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentOrders as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 font-mono text-xs text-blue-600">
                            <a href="{{ route('vendor.orders.show', $item) }}">{{ $item->order->order_number }}</a>
                        </td>
                        <td class="py-3">{{ $item->order->user->name }}</td>
                        <td class="py-3 truncate max-w-[150px]">{{ $item->product_name }}</td>
                        <td class="py-3 font-semibold">${{ number_format($item->vendor_amount, 2) }}</td>
                        <td class="py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $item->status === 'delivered' ? 'bg-green-100 text-green-700' : ($item->status === 'shipped' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="py-3 text-gray-500">{{ $item->created_at->format('M d') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-6 text-center text-gray-400">No orders yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($revenueChart->pluck('month')) !!},
        datasets: [{
            label: 'Revenue ($)',
            data: {!! json_encode($revenueChart->pluck('revenue')) !!},
            backgroundColor: 'rgba(59, 130, 246, 0.7)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { callback: v => '$' + v } } }
    }
});
</script>
@endpush
