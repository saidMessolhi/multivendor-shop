{{-- Cart page --}}
@extends('layouts.app')
@section('title', 'Shopping Cart')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-extrabold text-slate-900 mb-8" style="font-family:'Syne',sans-serif;">Shopping Cart</h1>
    @if($items->isEmpty())
    <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
        <div class="text-5xl mb-4">🛒</div>
        <h3 class="text-lg font-bold text-slate-800 mb-2">Your cart is empty</h3>
        <a href="{{ route('shop') }}" class="inline-block mt-4 px-6 py-3 bg-orange-500 text-white font-semibold rounded-xl">Start Shopping</a>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-3">
            @foreach($items as $item)
            <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4">
                <img src="{{ $item->product->first_image_url }}" class="w-20 h-20 rounded-xl object-cover bg-gray-50">
                <div class="flex-1 min-w-0">
                    <a href="{{ route('product.show', $item->product) }}" class="font-semibold text-slate-800 hover:text-orange-600 line-clamp-1">{{ $item->product->name }}</a>
                    <p class="text-sm text-slate-400">{{ $item->product->vendor->store_name }}</p>
                    <p class="text-sm font-bold text-slate-900 mt-1">${{ number_format($item->product->current_price, 2) }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('cart.update', $item) }}">
                        @csrf @method('PATCH')
                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}"
                            onchange="this.form.submit()"
                            class="w-16 text-center border border-gray-200 rounded-lg py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </form>
                    <form method="POST" action="{{ route('cart.remove', $item) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-6 h-fit">
            <h3 class="font-bold text-slate-800 mb-4">Order Summary</h3>
            <div class="flex justify-between text-sm text-slate-600 mb-2">
                <span>Subtotal</span><span>${{ number_format($subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm text-slate-600 mb-4">
                <span>Shipping</span><span>$5.99</span>
            </div>
            <div class="flex justify-between font-bold text-slate-900 text-lg border-t pt-4 mb-6">
                <span>Total</span><span>${{ number_format($subtotal + 5.99, 2) }}</span>
            </div>
            <a href="{{ route('checkout.index') }}" class="block w-full text-center py-3.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition-colors">
                Proceed to Checkout
            </a>
            <a href="{{ route('shop') }}" class="block w-full text-center py-3 text-slate-500 hover:text-slate-700 text-sm mt-2">Continue Shopping</a>
        </div>
    </div>
    @endif
</div>
@endsection
