@extends('layouts.app')
@section('title', config('shop.name') . ' — Multivendor Marketplace')

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 text-white py-20 px-4 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10"
        style="background-image: linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px), linear-gradient(90deg,rgba(255,255,255,0.05) 1px,transparent 1px); background-size: 48px 48px;">
    </div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-orange-500/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-72 h-72 bg-blue-500/20 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>

    <div class="max-w-4xl mx-auto text-center relative z-10">
        <span class="inline-block px-4 py-1.5 bg-orange-500/20 text-orange-300 text-xs font-semibold rounded-full uppercase tracking-widest mb-6">
            Multivendor Marketplace
        </span>
        <h1 class="text-5xl md:text-6xl font-extrabold mb-6 leading-tight" style="font-family:'Syne',sans-serif;">
            Shop from hundreds<br>
            <span class="text-orange-400">of vendors.</span>
        </h1>
        <p class="text-white/60 text-lg mb-10 max-w-xl mx-auto leading-relaxed">
            Discover unique products from independent sellers. Secure payments, fast delivery, buyer protection.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('shop') }}"
                class="px-8 py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition-all hover:shadow-lg hover:shadow-orange-500/30 hover:-translate-y-0.5">
                Browse Products
            </a>
            <a href="{{ route('vendor.apply') }}"
                class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl transition-all border border-white/20 backdrop-blur-sm">
                Start Selling Free
            </a>
        </div>

        {{-- Trust badges --}}
        <div class="flex flex-wrap justify-center gap-6 mt-12 text-sm text-white/40">
            @foreach(['🔒 Secure Checkout', '🚚 Fast Delivery', '↩️ Easy Returns', '⭐ Verified Vendors'] as $badge)
            <span>{{ $badge }}</span>
            @endforeach
        </div>
    </div>
</section>

{{-- Search bar --}}
<section class="bg-white shadow-sm py-5 px-4 -mt-0 border-b">
    <div class="max-w-2xl mx-auto">
        <form action="{{ route('search') }}" method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}"
                placeholder="Search for products, brands, vendors..."
                class="flex-1 px-5 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
            <button type="submit"
                class="px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl transition-colors text-sm">
                Search
            </button>
        </form>
    </div>
</section>

{{-- Categories --}}
@if($categories->count())
<section class="max-w-7xl mx-auto px-4 py-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-extrabold text-slate-900" style="font-family:'Syne',sans-serif;">Shop by Category</h2>
        <a href="{{ route('shop') }}" class="text-sm text-orange-500 hover:underline font-medium">View all →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
        @foreach($categories as $category)
        <a href="{{ route('category.show', $category) }}"
            class="group flex flex-col items-center gap-2 p-4 bg-white rounded-2xl border border-gray-100 hover:border-orange-200 hover:shadow-md transition-all text-center">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-50 to-orange-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                {{ ['Electronics'=>'💻','Clothing'=>'👕','Home & Garden'=>'🏡','Books'=>'📚','Sports'=>'⚽'][$category->name] ?? '🛍️' }}
            </div>
            <span class="text-xs font-semibold text-slate-700 leading-tight">{{ $category->name }}</span>
        </a>
        @endforeach
    </div>
</section>
@endif

{{-- Featured Products --}}
@if($featuredProducts->count())
<section class="bg-gray-50 py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900" style="font-family:'Syne',sans-serif;">Featured Products</h2>
                <p class="text-sm text-slate-500 mt-1">Handpicked by our team</p>
            </div>
            <a href="{{ route('shop') }}?featured=1" class="text-sm text-orange-500 hover:underline font-medium">See all →</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($featuredProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Top Vendors --}}
@if($topVendors->count())
<section class="max-w-7xl mx-auto px-4 py-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-extrabold text-slate-900" style="font-family:'Syne',sans-serif;">Top Vendors</h2>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($topVendors as $vendor)
        <a href="{{ route('vendor.store', $vendor) }}"
            class="group flex flex-col items-center gap-3 p-5 bg-white rounded-2xl border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all text-center">
            <img src="{{ $vendor->logo_url }}" alt="{{ $vendor->store_name }}"
                class="w-14 h-14 rounded-full object-cover border-2 border-gray-100 group-hover:border-blue-300 transition-colors">
            <div>
                <p class="text-sm font-bold text-slate-800 truncate max-w-[100px]">{{ $vendor->store_name }}</p>
                <p class="text-xs text-slate-400">{{ $vendor->products_count }} products</p>
            </div>
        </a>
        @endforeach
    </div>
</section>
@endif

{{-- New Arrivals --}}
@if($newArrivals->count())
<section class="bg-gray-50 py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900" style="font-family:'Syne',sans-serif;">New Arrivals</h2>
                <p class="text-sm text-slate-500 mt-1">Just added to the marketplace</p>
            </div>
            <a href="{{ route('shop') }}?sort=latest" class="text-sm text-orange-500 hover:underline font-medium">See all →</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($newArrivals as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA Banner --}}
<section class="max-w-7xl mx-auto px-4 py-12">
    <div class="bg-gradient-to-r from-blue-700 to-blue-900 rounded-3xl p-10 md:p-14 flex flex-col md:flex-row items-center justify-between gap-8 relative overflow-hidden">
        <div class="absolute right-0 top-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute left-1/2 bottom-0 w-48 h-48 bg-orange-500/10 rounded-full translate-y-1/2"></div>
        <div class="relative z-10">
            <h3 class="text-3xl font-extrabold text-white mb-3" style="font-family:'Syne',sans-serif;">
                Ready to start selling?
            </h3>
            <p class="text-blue-200 text-base max-w-md">
                Join thousands of vendors. Set up your store in minutes, no upfront fees, and start earning today.
            </p>
        </div>
        <div class="relative z-10 flex-shrink-0">
            <a href="{{ route('vendor.apply') }}"
                class="inline-block px-8 py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition-all hover:shadow-lg hover:-translate-y-0.5 whitespace-nowrap">
                Open Your Store →
            </a>
        </div>
    </div>
</section>

@endsection

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&display=swap" rel="stylesheet">
@endpush
