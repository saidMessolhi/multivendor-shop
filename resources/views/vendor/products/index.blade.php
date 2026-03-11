@extends('layouts.vendor')
@section('title','Products')
@section('page-title','Products')
@section('content')

{{-- Toolbar --}}
<div class="flex items-center justify-between mb-5">
    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
            class="px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
        <select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none">
            <option value="">All Status</option>
            @foreach(['active','draft','inactive'] as $s)
            <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-slate-700 font-semibold rounded-xl text-sm">Search</button>
    </form>

    <a href="{{ route('vendor.products.create') }}" class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl text-sm transition-colors">
        + Add Product
    </a>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-xs text-gray-500 uppercase tracking-wide border-b bg-gray-50">
                <th class="px-5 py-3">Product</th>
                <th class="px-5 py-3">Price</th>
                <th class="px-5 py-3">Stock</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($products as $product)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ $product->first_image_url }}" class="w-10 h-10 rounded-lg object-cover bg-gray-100">
                        <span class="font-medium text-slate-800 line-clamp-1 max-w-[200px]">{{ $product->name }}</span>
                    </div>
                </td>
                <td class="px-5 py-3 font-semibold">
                    ${{ number_format($product->price, 2) }}
                    @if($product->sale_price)
                        <span class="text-xs text-red-500 line-through ml-1">${{ number_format($product->sale_price, 2) }}</span>
                    @endif
                </td>
                <td class="px-5 py-3">
                    <span class="{{ $product->stock <= 5 ? 'text-red-600 font-semibold' : 'text-slate-600' }}">
                        {{ $product->stock }}
                    </span>
                </td>
                <td class="px-5 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ match($product->status) {
                            'active'   => 'bg-green-100 text-green-700',
                            'draft'    => 'bg-gray-100 text-gray-600',
                            default    => 'bg-red-100 text-red-600'
                        } }}">
                        {{ ucfirst($product->status) }}
                    </span>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('vendor.products.edit', $product) }}"
                            class="text-blue-600 hover:underline text-xs font-medium">Edit</a>

                        <form method="POST" action="{{ route('vendor.products.destroy', $product) }}"
                            onsubmit="return confirm('Delete this product? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-xs font-medium">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                    No products yet.
                    <a href="{{ route('vendor.products.create') }}" class="text-orange-500 hover:underline ml-1">Add your first product →</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-5">{{ $products->links() }}</div>
</div>

@endsection
