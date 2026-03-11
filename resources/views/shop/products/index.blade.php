@extends('layouts.app')
@section('title', isset($category) ? $category->name : (isset($q) ? 'Search: '.$q : 'Shop'))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    <div class="flex flex-col md:flex-row gap-8">

        {{-- Sidebar Filters --}}
        <aside class="w-full md:w-56 flex-shrink-0">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 sticky top-20">
                <h3 class="font-bold text-slate-800 mb-4">Filters</h3>
                <form method="GET" action="{{ route('shop') }}">

                    {{-- Search --}}
                    <div class="mb-4">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2 block">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Product name..."
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>

                    {{-- Category --}}
                    @if(isset($categories) && $categories->count())
                    <div class="mb-4">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2 block">Category</label>
                        <select name="category" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Price Range --}}
                    <div class="mb-4">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2 block">Price Range</label>
                        <div class="flex gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}"
                                placeholder="Min" min="0"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <input type="number" name="max_price" value="{{ request('max_price') }}"
                                placeholder="Max" min="0"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>

                    {{-- Sort --}}
                    <div class="mb-5">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2 block">Sort By</label>
                        <select name="sort" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="latest"     {{ request('sort') === 'latest'     ? 'selected' : '' }}>Latest</option>
                            <option value="price_asc"  {{ request('sort') === 'price_asc'  ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="rating"     {{ request('sort') === 'rating'     ? 'selected' : '' }}>Top Rated</option>
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl text-sm transition-colors">
                        Apply Filters
                    </button>
                    <a href="{{ route('shop') }}"
                        class="block w-full text-center mt-2 py-2.5 text-slate-500 hover:text-slate-700 text-sm transition-colors">
                        Clear All
                    </a>
                </form>
            </div>
        </aside>

        {{-- Products Grid --}}
        <div class="flex-1">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900" style="font-family:'Syne',sans-serif;">
                        @isset($category) {{ $category->name }}
                        @elseif(isset($q)) Search: "{{ $q }}"
                        @else All Products
                        @endisset
                    </h1>
                    <p class="text-sm text-slate-400 mt-0.5">{{ $products->total() }} products found</p>
                </div>
            </div>

            @if($products->isEmpty())
            <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
                <div class="text-4xl mb-4">🔍</div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">No products found</h3>
                <p class="text-slate-400 text-sm">Try adjusting your filters or search term.</p>
            </div>
            @else
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($products as $product)
                    @include('components.product-card', ['product' => $product])
                @endforeach
            </div>
            <div class="mt-8">
                {{ $products->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
