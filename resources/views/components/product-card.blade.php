<div class="group bg-white rounded-2xl border border-gray-100 hover:border-orange-200 hover:shadow-lg transition-all overflow-hidden flex flex-col">

    {{-- Image --}}
    <a href="{{ route('product.show', $product) }}" class="block relative overflow-hidden bg-gray-50 aspect-square">
        <img src="{{ $product->first_image_url }}"
            alt="{{ $product->name }}"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

        {{-- Badges --}}
        <div class="absolute top-2 left-2 flex flex-col gap-1">
            @if($product->is_featured)
            <span class="px-2 py-0.5 bg-orange-500 text-white text-xs font-bold rounded-full">Featured</span>
            @endif
            @if($product->isOnSale())
            <span class="px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full">-{{ $product->discount_percentage }}%</span>
            @endif
            @if(!$product->isInStock())
            <span class="px-2 py-0.5 bg-gray-500 text-white text-xs font-bold rounded-full">Out of stock</span>
            @endif
        </div>

        {{-- Wishlist --}}
        @auth
        <form method="POST" action="{{ route('wishlist.toggle', $product) }}" class="absolute top-2 right-2">
            @csrf
            <button type="submit"
                class="w-8 h-8 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow transition-all hover:scale-110">
                <svg class="w-4 h-4 text-gray-400 hover:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>
        </form>
        @endauth
    </a>

    {{-- Info --}}
    <div class="p-4 flex flex-col flex-1">
        <a href="{{ route('vendor.store', $product->vendor) }}"
            class="text-xs text-blue-600 hover:underline font-medium mb-1 truncate">
            {{ $product->vendor->store_name }}
        </a>
        <a href="{{ route('product.show', $product) }}"
            class="text-sm font-semibold text-slate-800 hover:text-orange-600 transition-colors line-clamp-2 mb-2 flex-1">
            {{ $product->name }}
        </a>

        {{-- Rating --}}
        @if($product->rating_count > 0)
        <div class="flex items-center gap-1 mb-2">
            <div class="flex">
                @for($i = 1; $i <= 5; $i++)
                <svg class="w-3 h-3 {{ $i <= round($product->rating_avg) ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                @endfor
            </div>
            <span class="text-xs text-slate-400">({{ $product->rating_count }})</span>
        </div>
        @endif

        {{-- Price + Cart --}}
        <div class="flex items-center justify-between mt-auto pt-2">
            <div>
                <span class="text-lg font-extrabold text-slate-900">
                    ${{ number_format($product->current_price, 2) }}
                </span>
                @if($product->isOnSale())
                <span class="text-xs text-slate-400 line-through ml-1">${{ number_format($product->price, 2) }}</span>
                @endif
            </div>

            @if($product->isInStock())
            <form method="POST" action="{{ route('cart.add') }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit"
                    class="w-9 h-9 bg-orange-500 hover:bg-orange-600 text-white rounded-xl flex items-center justify-center transition-all hover:scale-110 hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
            </form>
            @else
            <span class="text-xs text-gray-400 font-medium">Unavailable</span>
            @endif
        </div>
    </div>
</div>
