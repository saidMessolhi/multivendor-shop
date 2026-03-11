@extends('layouts.vendor')
@section('title', isset($product) ? 'Edit Product' : 'Add Product')
@section('page-title', isset($product) ? 'Edit Product' : 'Add Product')

@section('content')
<div class="max-w-4xl">

    <form method="POST"
        action="{{ isset($product) ? route('vendor.products.update', $product) : route('vendor.products.store') }}"
        enctype="multipart/form-data">
        @csrf
        @if(isset($product)) @method('PUT') @endif

        <div class="space-y-6">

            {{-- Basic Info --}}
            <div class="bg-white rounded-xl border border-gray-100 p-6">
                <h2 class="font-bold text-slate-800 mb-5">Product Information</h2>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 @error('name') border-red-300 @enderror">
                        @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Short Description</label>
                        <input type="text" name="short_description"
                            value="{{ old('short_description', $product->short_description ?? '') }}"
                            placeholder="Brief summary shown in product cards"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Full Description</label>
                        <textarea name="description" rows="6"
                            placeholder="Detailed product description..."
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 resize-y">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Category</label>
                        <select name="category_id"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">— Select Category —</option>
                            @foreach($categories as $category)
                                @if(!$category->parent_id)
                                <optgroup label="{{ $category->name }}">
                                    @foreach($categories->where('parent_id', $category->id) as $child)
                                    <option value="{{ $child->id }}"
                                        {{ old('category_id', $product->category_id ?? '') == $child->id ? 'selected' : '' }}>
                                        {{ $child->name }}
                                    </option>
                                    @endforeach
                                </optgroup>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Pricing & Stock --}}
            <div class="bg-white rounded-xl border border-gray-100 p-6">
                <h2 class="font-bold text-slate-800 mb-5">Pricing & Stock</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Price <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">$</span>
                            <input type="number" name="price" step="0.01" min="0"
                                value="{{ old('price', $product->price ?? '') }}" required
                                class="w-full pl-7 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 @error('price') border-red-300 @enderror">
                        </div>
                        @error('price')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Sale Price</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">$</span>
                            <input type="number" name="sale_price" step="0.01" min="0"
                                value="{{ old('sale_price', $product->sale_price ?? '') }}"
                                placeholder="Optional"
                                class="w-full pl-7 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Stock <span class="text-red-500">*</span></label>
                        <input type="number" name="stock" min="0"
                            value="{{ old('stock', $product->stock ?? 0) }}" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 @error('stock') border-red-300 @enderror">
                        @error('stock')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">SKU</label>
                        <input type="text" name="sku"
                            value="{{ old('sku', $product->sku ?? '') }}"
                            placeholder="Auto-generated"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>
            </div>

            {{-- Images --}}
            <div class="bg-white rounded-xl border border-gray-100 p-6">
                <h2 class="font-bold text-slate-800 mb-5">Product Images</h2>
                @if(isset($product) && $product->images && count($product->images))
                <div class="flex flex-wrap gap-3 mb-4">
                    @foreach($product->images as $image)
                    <div class="relative w-24 h-24 rounded-xl overflow-hidden border border-gray-200">
                        <img src="{{ asset('storage/' . $image) }}" class="w-full h-full object-cover">
                    </div>
                    @endforeach
                </div>
                @endif
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-orange-300 transition-colors">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                    <p class="text-sm text-slate-500 mb-2">Upload product images</p>
                    <p class="text-xs text-slate-400 mb-4">PNG, JPG up to 2MB each. First image will be the main image.</p>
                    <input type="file" name="images[]" multiple accept="image/*"
                        class="text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-orange-50 file:text-orange-600 file:font-semibold hover:file:bg-orange-100 cursor-pointer">
                </div>
            </div>

            {{-- Status & Visibility --}}
            <div class="bg-white rounded-xl border border-gray-100 p-6">
                <h2 class="font-bold text-slate-800 mb-5">Status & Visibility</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Status</label>
                        <select name="status"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                            @foreach(['draft' => 'Draft', 'active' => 'Active', 'inactive' => 'Inactive'] as $val => $label)
                            <option value="{{ $val }}"
                                {{ old('status', $product->status ?? 'draft') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-3 pt-6">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" value="1" id="is_featured"
                            {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}
                            class="w-4 h-4 text-orange-500 rounded border-gray-300 focus:ring-orange-500">
                        <label for="is_featured" class="text-sm font-semibold text-slate-700">Mark as Featured Product</label>
                    </div>
                </div>
            </div>

            {{-- SEO --}}
            <div class="bg-white rounded-xl border border-gray-100 p-6">
                <h2 class="font-bold text-slate-800 mb-1">SEO <span class="text-slate-400 font-normal text-sm">(optional)</span></h2>
                <p class="text-xs text-slate-400 mb-4">Leave blank to auto-generate from product name</p>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Meta Title</label>
                        <input type="text" name="meta_title"
                            value="{{ old('meta_title', $product->meta_title ?? '') }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Meta Description</label>
                        <textarea name="meta_description" rows="2"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 resize-none">{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Submit buttons — NO delete button inside this form --}}
            <div class="flex items-center gap-3 pb-2">
                <button type="submit"
                    class="px-8 py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition-colors">
                    {{ isset($product) ? 'Update Product' : 'Create Product' }}
                </button>
                <a href="{{ route('vendor.products.index') }}"
                    class="px-6 py-3 bg-white border border-gray-200 hover:border-gray-300 text-slate-600 font-semibold rounded-xl transition-colors">
                    Cancel
                </a>
            </div>

        </div>
    </form>

    {{-- Delete form is OUTSIDE the main form to avoid nested form bug --}}
    @if(isset($product))
    <div class="pb-8 mt-2">
        <form method="POST" action="{{ route('vendor.products.destroy', $product) }}"
            onsubmit="return confirm('Delete this product? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="px-6 py-3 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-xl transition-colors">
                Delete Product
            </button>
        </form>
    </div>
    @endif

</div>
@endsection
