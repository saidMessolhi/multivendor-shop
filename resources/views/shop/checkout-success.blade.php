@extends('layouts.app')
@section('title', 'Order Confirmed!')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-16 text-center">

    {{-- Success Icon --}}
    <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-12 h-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h1 class="text-3xl font-extrabold text-slate-900 mb-3" style="font-family:'Syne',sans-serif;">
        Order Confirmed! 🎉
    </h1>
    <p class="text-slate-500 mb-2">Thank you for your purchase. Your order has been placed successfully.</p>
    <p class="text-sm text-slate-400 mb-8">
        A confirmation email will be sent to <strong>{{ Auth::user()->email }}</strong>
    </p>

    {{-- Order Info --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6 text-left">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-0.5">Order Number</p>
                <p class="font-mono font-bold text-slate-800 text-lg">{{ $order->order_number }}</p>
            </div>
            <span class="px-3 py-1.5 bg-yellow-100 text-yellow-700 text-sm font-semibold rounded-full">
                {{ ucfirst($order->status) }}
            </span>
        </div>

        {{-- Items --}}
        <div class="divide-y divide-gray-50">
            @foreach($order->items as $item)
            <div class="flex items-center gap-3 py-3">
                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0 text-xs font-bold text-gray-400">
                    {{ $item->quantity }}×
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-800 truncate">{{ $item->product_name }}</p>
                    <p class="text-xs text-slate-400">{{ $item->vendor->store_name }}</p>
                </div>
                <p class="text-sm font-semibold text-slate-900">${{ number_format($item->unit_price * $item->quantity, 2) }}</p>
            </div>
            @endforeach
        </div>

        {{-- Totals --}}
        <div class="border-t border-gray-100 pt-4 mt-2 space-y-1.5 text-sm">
            <div class="flex justify-between text-slate-500">
                <span>Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-slate-500">
                <span>Shipping</span><span>${{ number_format($order->shipping_amount, 2) }}</span>
            </div>
            @if($order->tax_amount > 0)
            <div class="flex justify-between text-slate-500">
                <span>Tax</span><span>${{ number_format($order->tax_amount, 2) }}</span>
            </div>
            @endif
            <div class="flex justify-between font-bold text-slate-900 text-base pt-2 border-t border-gray-100">
                <span>Total</span><span>${{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        {{-- Payment --}}
        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between text-sm">
            <span class="text-slate-500">Payment</span>
            <span class="font-semibold capitalize px-2.5 py-1 rounded-full text-xs
                {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                {{ ucfirst($order->payment_method) }} — {{ ucfirst($order->payment_status) }}
            </span>
        </div>
    </div>

    {{-- Shipping Address --}}
    @php $addr = $order->shipping_address; @endphp
    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-8 text-left">
        <h3 class="font-bold text-slate-800 mb-3">Shipping To</h3>
        <address class="text-sm text-slate-600 not-italic leading-relaxed">
            {{ $addr['first_name'] ?? '' }} {{ $addr['last_name'] ?? '' }}<br>
            {{ $addr['address_line_1'] ?? '' }}<br>
            @if(!empty($addr['address_line_2'])){{ $addr['address_line_2'] }}<br>@endif
            {{ $addr['city'] ?? '' }}, {{ $addr['postal_code'] ?? '' }}<br>
            {{ $addr['country'] ?? '' }}
        </address>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('orders.show', $order) }}"
            class="px-6 py-3 bg-slate-900 hover:bg-slate-700 text-white font-semibold rounded-xl transition-colors">
            Track Order
        </a>
        <a href="{{ route('shop') }}"
            class="px-6 py-3 bg-white border border-gray-200 hover:border-orange-300 text-slate-700 font-semibold rounded-xl transition-colors">
            Continue Shopping
        </a>
    </div>
</div>
@endsection
