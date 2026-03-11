<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $vendor   = Auth::user()->vendor;
        $products = Product::byVendor($vendor->id)
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return view('vendor.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('parent_id')->orderBy('name')->get();
        return view('vendor.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0|lt:price',
            'stock'             => 'required|integer|min:0',
            'status'            => 'required|in:active,draft,inactive',
            'images.*'          => 'nullable|image|max:2048',
        ]);

        $vendor = Auth::user()->vendor;
        $images = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = $image->store("products/{$vendor->id}", 'public');
            }
        }

        Product::create([
            'vendor_id'         => $vendor->id,
            'category_id'       => $request->category_id,
            'name'              => $request->name,
            'slug'              => Str::slug($request->name) . '-' . Str::random(5),
            'short_description' => $request->short_description,
            'description'       => $request->description,
            'price'             => $request->price,
            'sale_price'        => $request->sale_price ?: null,
            'stock'             => $request->stock,
            'sku'               => $request->sku ?: strtoupper(Str::random(8)),
            'status'            => $request->status,
            'is_featured'       => $request->boolean('is_featured'),
            'images'            => $images,
            'meta_title'        => $request->meta_title,
            'meta_description'  => $request->meta_description,
        ]);

        return redirect()->route('vendor.products.index')
            ->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        abort_unless($product->vendor_id === Auth::user()->vendor->id, 403);
        $categories = Category::orderBy('parent_id')->orderBy('name')->get();
        return view('vendor.products.create', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        abort_unless($product->vendor_id === Auth::user()->vendor->id, 403);

        $request->validate([
            'name'      => 'required|string|max:255',
            'price'     => 'required|numeric|min:0',
            'sale_price'=> 'nullable|numeric|min:0',
            'stock'     => 'required|integer|min:0',
            'status'    => 'required|in:active,draft,inactive',
            'images.*'  => 'nullable|image|max:2048',
        ]);

        $images = $product->images ?? [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = $image->store("products/{$product->vendor_id}", 'public');
            }
        }

        $product->update([
            'category_id'       => $request->category_id,
            'name'              => $request->name,
            'short_description' => $request->short_description,
            'description'       => $request->description,
            'price'             => $request->price,
            'sale_price'        => $request->sale_price ?: null,
            'stock'             => $request->stock,
            'sku'               => $request->sku ?: $product->sku,
            'status'            => $request->status,
            'is_featured'       => $request->boolean('is_featured'),
            'images'            => $images,
            'meta_title'        => $request->meta_title,
            'meta_description'  => $request->meta_description,
        ]);

        return redirect()->route('vendor.products.index')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        abort_unless($product->vendor_id === Auth::user()->vendor->id, 403);
        $product->delete();
        return redirect()->route('vendor.products.index')
            ->with('success', 'Product deleted.');
    }

    public function import(Request $request)
    {
        return back()->with('info', 'Import feature coming soon.');
    }
}
