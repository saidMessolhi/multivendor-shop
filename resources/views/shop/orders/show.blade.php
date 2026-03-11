@extends('layouts.app')
@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">

    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('orders.index') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900" style="font-family:'Syne',sans-serif;">
                Order {{ $order->order_number }}
            </h1>
            <p class="text-slate-400 text-sm">Placed {{ $order->created_at->format('F d, Y \a\t g:i A') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Items --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Items grouped by vendor --}}
            @foreach($order->items->groupBy('vendor_id') as $vendorId => $items)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17"/>
                        </svg>
                        <span class="text-sm font-semibold text-slate-700">{{ $items->first()->vendor->store_name }}</span>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                        {{ match($items->first()->status) {
                            'delivered' => 'bg-green-100 text-green-700',
                            'shipped'   => 'bg-blue-100 text-blue-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                            default     => 'bg-yellow-100 text-yellow-700',
                        } }}">
                        {{ ucfirst($items->first()->status) }}
                    </span>
                </div>
                @foreach($items as $item)
                <div class="flex items-center gap-4 px-5 py-4 border-b border-gray-50 last:border-0">
                    <img src="{{ $item->product_image ?? asset('images/placeholder.png') }}"
                        class="w-16 h-16 rounded-xl object-cover bg-gray-100 flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-800 text-sm">{{ $item->product_name }}</p>
                        @if($item->variant_label)
                        <p class="text-xs text-slate-400 mt-0.5">{{ $item->variant_label }}</p>
                        @endif
                        <p class="text-xs text-slate-400 mt-0.5">Qty: {{ $item->quantity }}</p>
                        @if($item->tracking_number)
                        <p class="text-xs text-blue-600 mt-1">Tracking: {{ $item->tracking_number }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-slate-900">${{ number_format($item->unit_price * $item->quantity, 2) }}</p>
                        <p class="text-xs text-slate-400">${{ number_format($item->unit_price, 2) }} each</p>
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach

            {{-- Cancel button --}}
            @if($order->isCancellable())
            <form method="POST" action="{{ route('orders.cancel', $order) }}"
                onsubmit="return confirm('Are you sure you want to cancel this order?')">
                @csrf
                <button type="submit"
                    class="w-full py-3 border-2 border-red-200 text-red-600 hover:bg-red-50 font-semibold rounded-xl transition-colors text-sm">
                    Cancel Order
                </button>
            </form>
            @endif
        </div>

        {{-- Right: Summary --}}
        <div class="space-y-4">

            {{-- Order Summary --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="font-bold text-slate-800 mb-4">Order Summary</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span>${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Shipping</span>
                        <span>${{ number_format($order->shipping_amount, 2) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Discount</span>
                        <span>-${{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                    @endif
                    @if($order->tax_amount > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Tax</span>
                        <span>${{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-bold text-slate-900 text-base pt-2 border-t border-gray-100">
                        <span>Total</span>
                        <span>${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Payment Info --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="font-bold text-slate-800 mb-3">Payment</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Method</span>
                        <span class="font-medium capitalize">{{ $order->payment_method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status</span>
                        <span class="font-semibold {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-orange-500' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                    @if($order->paid_at)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Paid at</span>
                        <span class="font-medium">{{ $order->paid_at->format('M d, Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Shipping Address --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="font-bold text-slate-800 mb-3">Shipping Address</h3>
                @php $addr = $order->shipping_address; @endphp
                <address class="text-sm text-slate-600 not-italic leading-relaxed">
                    {{ $addr['first_name'] ?? '' }} {{ $addr['last_name'] ?? '' }}<br>
                    {{ $addr['address_line_1'] ?? '' }}<br>
                    @if(!empty($addr['address_line_2'])){{ $addr['address_line_2'] }}<br>@endif
                    {{ $addr['city'] ?? '' }}, {{ $addr['postal_code'] ?? '' }}<br>
                    {{ $addr['country'] ?? '' }}
                </address>
            </div>
        </div>
    </div>
</div>
@endsection
