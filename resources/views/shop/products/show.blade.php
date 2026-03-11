@extends('layouts.app')
@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-slate-400 mb-8">
        <a href="{{ route('home') }}" class="hover:text-slate-600">Home</a>
        <span>/</span>
        <a href="{{ route('shop') }}" class="hover:text-slate-600">Shop</a>
        @if($product->category)
        <span>/</span>
        <a href="{{ route('category.show', $product->category) }}" class="hover:text-slate-600">{{ $product->category->name }}</a>
        @endif
        <span>/</span>
        <span class="text-slate-700 font-medium truncate max-w-[200px]">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">

        {{-- Images --}}
        <div x-data="{ active: 0 }">
            <div class="aspect-square rounded-2xl overflow-hidden bg-gray-50 border border-gray-100 mb-3">
                @if($product->images && count($product->images))
                    @foreach($product->images as $i => $img)
                    <img src="{{ asset('storage/'.$img) }}" alt="{{ $product->name }}"
                        x-show="active === {{ $i }}"
                        class="w-full h-full object-cover">
                    @endforeach
                @else
                <div class="w-full h-full flex items-center justify-center text-gray-300">
                    <svg class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                @endif
            </div>
            @if($product->images && count($product->images) > 1)
            <div class="flex gap-2">
                @foreach($product->images as $i => $img)
                <button @click="active = {{ $i }}"
                    :class="active === {{ $i }} ? 'ring-2 ring-orange-500' : 'ring-1 ring-gray-200'"
                    class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 transition-all">
                    <img src="{{ asset('storage/'.$img) }}" class="w-full h-full object-cover">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Info --}}
        <div>
            <a href="{{ route('vendor.store', $product->vendor) }}" class="text-sm text-blue-600 hover:underline font-medium">
                {{ $product->vendor->store_name }}
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900 mt-2 mb-3 leading-tight" style="font-family:'Syne',sans-serif;">
                {{ $product->name }}
            </h1>

            {{-- Rating --}}
            @if($product->rating_count > 0)
            <div class="flex items-center gap-2 mb-4">
                <div class="flex">
                    @for($i = 1; $i <= 5; $i++)
                    <svg class="w-4 h-4 {{ $i <= round($product->rating_avg) ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endfor
                </div>
                <span class="text-sm font-semibold text-slate-700">{{ number_format($product->rating_avg, 1) }}</span>
                <span class="text-sm text-slate-400">({{ $product->rating_count }} reviews)</span>
            </div>
            @endif

            {{-- Price --}}
            <div class="flex items-baseline gap-3 mb-6">
                <span class="text-4xl font-extrabold text-slate-900">${{ number_format($product->current_price, 2) }}</span>
                @if($product->isOnSale())
                <span class="text-xl text-slate-400 line-through">${{ number_format($product->price, 2) }}</span>
                <span class="px-2 py-0.5 bg-red-100 text-red-600 text-sm font-bold rounded-full">
                    -{{ $product->discount_percentage }}% OFF
                </span>
                @endif
            </div>

            {{-- Short description --}}
            @if($product->short_description)
            <p class="text-slate-600 leading-relaxed mb-6">{{ $product->short_description }}</p>
            @endif

            {{-- Stock --}}
            <div class="flex items-center gap-2 mb-6">
                @if($product->isInStock())
                <span class="flex items-center gap-1.5 text-sm text-green-600 font-medium">
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    In stock ({{ $product->stock }} available)
                </span>
                @else
                <span class="flex items-center gap-1.5 text-sm text-red-500 font-medium">
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                    Out of stock
                </span>
                @endif
            </div>

            {{-- Add to Cart --}}
            @if($product->isInStock())
            <form method="POST" action="{{ route('cart.add') }}" x-data="{ qty: 1 }">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                        <button type="button" @click="qty = Math.max(1, qty - 1)"
                            class="px-4 py-3 text-slate-600 hover:bg-gray-50 transition-colors font-bold">−</button>
                        <input type="number" name="quantity" x-model="qty" min="1" max="{{ $product->stock }}"
                            class="w-14 text-center py-3 border-x border-gray-200 text-sm font-semibold focus:outline-none">
                        <button type="button" @click="qty = Math.min({{ $product->stock }}, qty + 1)"
                            class="px-4 py-3 text-slate-600 hover:bg-gray-50 transition-colors font-bold">+</button>
                    </div>
                    <button type="submit"
                        class="flex-1 py-3.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition-all hover:shadow-lg hover:shadow-orange-500/25 hover:-translate-y-0.5">
                        Add to Cart
                    </button>
                </div>
            </form>
            @endif

            {{-- Meta --}}
            <div class="border-t border-gray-100 pt-5 space-y-2 text-sm text-slate-500">
                @if($product->sku)
                <div class="flex gap-2"><span class="font-medium text-slate-700 w-20">SKU:</span>{{ $product->sku }}</div>
                @endif
                @if($product->category)
                <div class="flex gap-2"><span class="font-medium text-slate-700 w-20">Category:</span>
                    <a href="{{ route('category.show', $product->category) }}" class="text-blue-600 hover:underline">{{ $product->category->name }}</a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Description --}}
    @if($product->description)
    <div class="bg-white rounded-2xl border border-gray-100 p-8 mb-8">
        <h2 class="text-xl font-bold text-slate-900 mb-4" style="font-family:'Syne',sans-serif;">Description</h2>
        <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed">
            {!! nl2br(e($product->description)) !!}
        </div>
    </div>
    @endif

    {{-- Reviews --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-8 mb-8">
        <h2 class="text-xl font-bold text-slate-900 mb-6" style="font-family:'Syne',sans-serif;">
            Reviews ({{ $product->rating_count }})
        </h2>

        @auth
        <form method="POST" action="{{ route('reviews.store', $product) }}" class="mb-8 p-5 bg-gray-50 rounded-xl" x-data="{ rating: 0 }">
            @csrf
            <h3 class="font-semibold text-slate-800 mb-4">Write a Review</h3>
            <div class="flex items-center gap-1 mb-4">
                @for($i = 1; $i <= 5; $i++)
                <button type="button" @click="rating = {{ $i }}"
                    :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'"
                    class="text-2xl transition-colors hover:text-yellow-400">★</button>
                @endfor
                <input type="hidden" name="rating" :value="rating">
            </div>
            <input type="text" name="title" placeholder="Review title" value="{{ old('title') }}"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-orange-500">
            <textarea name="body" rows="3" placeholder="Share your experience..." value="{{ old('body') }}"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-orange-500 resize-none"></textarea>
            <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl text-sm transition-colors">
                Submit Review
            </button>
        </form>
        @endauth

        <div class="space-y-5">
            @forelse($product->reviews->where('is_approved', true) as $review)
            <div class="border-b border-gray-50 pb-5 last:border-0">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <img src="{{ $review->user->avatar_url }}" class="w-9 h-9 rounded-full">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $review->user->name }}</p>
                            <p class="text-xs text-slate-400">{{ $review->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div class="flex">
                        @for($i = 1; $i <= 5; $i++)
                        <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        @endfor
                    </div>
                </div>
                @if($review->title)<p class="font-semibold text-slate-800 text-sm mb-1">{{ $review->title }}</p>@endif
                @if($review->body)<p class="text-slate-600 text-sm leading-relaxed">{{ $review->body }}</p>@endif
                @if($review->vendor_reply)
                <div class="mt-3 ml-4 pl-4 border-l-2 border-orange-200 bg-orange-50 rounded-r-lg p-3">
                    <p class="text-xs font-semibold text-orange-600 mb-1">Vendor reply</p>
                    <p class="text-sm text-slate-600">{{ $review->vendor_reply }}</p>
                </div>
                @endif
            </div>
            @empty
            <p class="text-slate-400 text-sm text-center py-6">No reviews yet. Be the first to review this product!</p>
            @endforelse
        </div>
    </div>

    {{-- Related Products --}}
    @if($related->count())
    <div>
        <h2 class="text-xl font-bold text-slate-900 mb-5" style="font-family:'Syne',sans-serif;">Related Products</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($related as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
