@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">

    <h1 class="text-2xl font-extrabold text-slate-900 mb-8" style="font-family:'Syne',sans-serif;">Checkout</h1>

    <form method="POST" action="{{ route('checkout.store') }}" id="checkout-form">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Left: Shipping + Payment --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Saved Addresses --}}
                @if($addresses->count())
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="font-bold text-slate-800 mb-4">Saved Addresses</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($addresses as $address)
                        <label class="cursor-pointer">
                            <input type="radio" name="saved_address" value="{{ $address->id }}"
                                class="sr-only peer">
                            <div class="peer-checked:border-orange-500 peer-checked:bg-orange-50 border-2 border-gray-100 rounded-xl p-4 transition-all hover:border-orange-200">
                                <p class="font-semibold text-sm text-slate-800">{{ $address->label }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ $address->full_address }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-slate-400 mt-3">Or fill in a new address below</p>
                </div>
                @endif

                {{-- Shipping Address --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="font-bold text-slate-800 mb-5">Shipping Address</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">First Name <span class="text-red-500">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name', Auth::user()->name ? explode(' ', Auth::user()->name)[0] : '') }}" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 @error('first_name') border-red-300 @enderror">
                            @error('first_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 @error('last_name') border-red-300 @enderror">
                            @error('last_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', Auth::user()->phone) }}"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Address <span class="text-red-500">*</span></label>
                            <input type="text" name="address_line_1" value="{{ old('address_line_1') }}" required
                                placeholder="Street address"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 @error('address_line_1') border-red-300 @enderror">
                            @error('address_line_1')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <input type="text" name="address_line_2" value="{{ old('address_line_2') }}"
                                placeholder="Apartment, suite, etc. (optional)"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">City <span class="text-red-500">*</span></label>
                            <input type="text" name="city" value="{{ old('city') }}" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 @error('city') border-red-300 @enderror">
                            @error('city')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Postal Code <span class="text-red-500">*</span></label>
                            <input type="text" name="postal_code" value="{{ old('postal_code') }}" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 @error('postal_code') border-red-300 @enderror">
                            @error('postal_code')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Country <span class="text-red-500">*</span></label>
                            <input type="text" name="country" value="{{ old('country') }}" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 @error('country') border-red-300 @enderror">
                            @error('country')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="font-bold text-slate-800 mb-5">Payment Method</h2>
                    <div class="space-y-3">

                        {{-- Stripe --}}
                        <label class="cursor-pointer block">
                            <input type="radio" name="payment_method" value="stripe" class="sr-only peer" {{ old('payment_method') === 'stripe' ? 'checked' : '' }}>
                            <div class="peer-checked:border-orange-500 peer-checked:bg-orange-50 border-2 border-gray-100 rounded-xl p-4 flex items-center gap-4 transition-all hover:border-orange-200">
                                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800 text-sm">Credit / Debit Card</p>
                                    <p class="text-xs text-slate-400">Visa, Mastercard, Amex via Stripe</p>
                                </div>
                            </div>
                        </label>

                        {{-- PayPal --}}
                        <label class="cursor-pointer block">
                            <input type="radio" name="payment_method" value="paypal" class="sr-only peer" {{ old('payment_method') === 'paypal' ? 'checked' : '' }}>
                            <div class="peer-checked:border-orange-500 peer-checked:bg-orange-50 border-2 border-gray-100 rounded-xl p-4 flex items-center gap-4 transition-all hover:border-orange-200">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <span class="text-blue-700 font-extrabold text-sm">PP</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800 text-sm">PayPal</p>
                                    <p class="text-xs text-slate-400">Pay securely with your PayPal account</p>
                                </div>
                            </div>
                        </label>

                        {{-- Cash on Delivery --}}
                        <label class="cursor-pointer block">
                            <input type="radio" name="payment_method" value="cod" class="sr-only peer" checked>
                            <div class="peer-checked:border-orange-500 peer-checked:bg-orange-50 border-2 border-gray-100 rounded-xl p-4 flex items-center gap-4 transition-all hover:border-orange-200">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800 text-sm">Cash on Delivery</p>
                                    <p class="text-xs text-slate-400">Pay when your order arrives</p>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('payment_method')<p class="mt-2 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Order Notes --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="font-bold text-slate-800 mb-3">Order Notes <span class="text-slate-400 font-normal text-sm">(optional)</span></h2>
                    <textarea name="notes" rows="3" placeholder="Any special instructions for your order..."
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 resize-none">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- Right: Order Summary --}}
            <div class="space-y-4">
                <div class="bg-white rounded-2xl border border-gray-100 p-6 sticky top-20">
                    <h2 class="font-bold text-slate-800 mb-5">Order Summary</h2>

                    {{-- Items --}}
                    <div class="space-y-3 mb-5">
                        @foreach($items as $item)
                        <div class="flex items-center gap-3">
                            <img src="{{ $item->product->first_image_url }}"
                                class="w-12 h-12 rounded-xl object-cover bg-gray-50 flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 line-clamp-1">{{ $item->product->name }}</p>
                                <p class="text-xs text-slate-400">Qty: {{ $item->quantity }}</p>
                            </div>
                            <p class="text-sm font-semibold text-slate-900 flex-shrink-0">
                                ${{ number_format($item->product->current_price * $item->quantity, 2) }}
                            </p>
                        </div>
                        @endforeach
                    </div>

                    {{-- Coupon --}}
                    <div class="flex gap-2 mb-5">
                        <input type="text" name="coupon_code" value="{{ old('coupon_code') }}"
                            placeholder="Coupon code"
                            class="flex-1 px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-slate-700 font-semibold rounded-xl text-sm transition-colors">
                            Apply
                        </button>
                    </div>

                    {{-- Totals --}}
                    <div class="space-y-2 text-sm border-t border-gray-100 pt-4">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal</span>
                            <span>${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Shipping</span>
                            <span>$5.99</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Tax (8%)</span>
                            <span>${{ number_format($subtotal * 0.08, 2) }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-slate-900 text-lg border-t border-gray-100 pt-3 mt-2">
                            <span>Total</span>
                            <span>${{ number_format($subtotal + 5.99 + ($subtotal * 0.08), 2) }}</span>
                        </div>
                    </div>

                    <button type="submit"
                        class="mt-6 w-full py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition-all hover:shadow-lg hover:shadow-orange-500/25 hover:-translate-y-0.5 text-base">
                        Place Order →
                    </button>

                    <p class="text-xs text-center text-slate-400 mt-3">
                        🔒 Secure checkout — your data is protected
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
