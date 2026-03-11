@extends('layouts.app')
@section('title', 'My Orders')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900" style="font-family:'Syne',sans-serif;">My Orders</h1>
            <p class="text-slate-500 text-sm mt-1">Track and manage your purchases</p>
        </div>
        <a href="{{ route('shop') }}" class="text-sm text-orange-500 hover:underline font-medium">Continue shopping →</a>
    </div>

    @if($orders->isEmpty())
    <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-slate-800 mb-2">No orders yet</h3>
        <p class="text-slate-400 text-sm mb-6">When you place an order, it will appear here.</p>
        <a href="{{ route('shop') }}" class="inline-block px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl transition-colors">
            Start Shopping
        </a>
    </div>
    @else
    <div class="space-y-4">
        @foreach($orders as $order)
        <div class="bg-white rounded-2xl border border-gray-100 hover:border-orange-200 hover:shadow-sm transition-all overflow-hidden">

            {{-- Order Header --}}
            <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-gray-50">
                <div class="flex items-center gap-6">
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">Order</p>
                        <p class="font-mono text-sm font-bold text-slate-800">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">Date</p>
                        <p class="text-sm font-medium text-slate-700">{{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">Total</p>
                        <p class="text-sm font-bold text-slate-900">${{ number_format($order->total_amount, 2) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Status badge --}}
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        {{ match($order->status) {
                            'delivered'  => 'bg-green-100 text-green-700',
                            'shipped'    => 'bg-blue-100 text-blue-700',
                            'processing' => 'bg-purple-100 text-purple-700',
                            'cancelled'  => 'bg-red-100 text-red-700',
                            'refunded'   => 'bg-gray-100 text-gray-700',
                            default      => 'bg-yellow-100 text-yellow-700',
                        } }}">
                        {{ ucfirst($order->status) }}
                    </span>
                    {{-- Payment badge --}}
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        {{ $order->payment_status === 'paid' ? 'bg-green-50 text-green-600' : 'bg-orange-50 text-orange-600' }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                    <a href="{{ route('orders.show', $order) }}"
                        class="px-4 py-1.5 bg-slate-900 hover:bg-slate-700 text-white text-xs font-semibold rounded-lg transition-colors">
                        View Details
                    </a>
                </div>
            </div>

            {{-- Order Items --}}
            <div class="px-6 py-4 flex flex-wrap gap-4">
                @foreach($order->items->take(3) as $item)
                <div class="flex items-center gap-3">
                    <img src="{{ $item->product_image ?? asset('images/placeholder.png') }}"
                        class="w-12 h-12 rounded-xl object-cover bg-gray-100 border border-gray-100">
                    <div>
                        <p class="text-sm font-medium text-slate-800 line-clamp-1 max-w-[160px]">{{ $item->product_name }}</p>
                        <p class="text-xs text-slate-400">Qty: {{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}</p>
                    </div>
                </div>
                @endforeach
                @if($order->items->count() > 3)
                <div class="flex items-center">
                    <span class="text-xs text-slate-400 bg-gray-50 px-3 py-2 rounded-xl">
                        +{{ $order->items->count() - 3 }} more item(s)
                    </span>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
