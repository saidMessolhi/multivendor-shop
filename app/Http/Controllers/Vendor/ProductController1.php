<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $vendor = Auth::user()->vendor;

        $products = Product::byVendor($vendor->id)
            ->with('category')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return view('vendor.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('vendor.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0|lt:price',
            'stock'             => 'required|integer|min:0',
            'sku'               => 'nullable|string|max:100|unique:products',
            'category_id'       => 'required|exists:categories,id',
            'status'            => 'required|in:draft,active,inactive',
            'is_featured'       => 'boolean',
            'weight'            => 'nullable|numeric|min:0',
            'images.*'          => 'nullable|image|max:2048',
            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:500',
        ]);

        $vendor = Auth::user()->vendor;

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store("products/{$vendor->id}", 'public');
                $images[] = $path;
            }
        }

        $product = Product::create([
            ...$validated,
            'vendor_id' => $vendor->id,
            'images'    => $images,
        ]);

        return redirect()->route('vendor.products.edit', $product)
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $this->authorizeVendorProduct($product);
        $categories = Category::where('is_active', true)->get();
        return view('vendor.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeVendorProduct($product);
      

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0',
            'stock'             => 'required|integer|min:0',
            'sku'               => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'category_id'       => 'required|exists:categories,id',
            'status'            => 'required|in:draft,active,inactive',
            'weight'            => 'nullable|numeric|min:0',
            'images.*'          => 'nullable|image|max:2048',
            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:500',
        ]);

        $images = $product->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store("products/{$product->vendor_id}", 'public');
                $images[] = $path;
            }
        }

        // Remove deleted images
        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $path) {
                Storage::disk('public')->delete($path);
                $images = array_filter($images, fn($i) => $i !== $path);
            }
        }

        $product->update([...$validated, 'images' => array_values($images)]);

        return back()->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->authorizeVendorProduct($product);
        $product->delete();
        return redirect()->route('vendor.products.index')->with('success', 'Product deleted.');
    }

    private function authorizeVendorProduct(Product $product): void
    {
        if ($product->vendor_id !== Auth::user()->vendor->id) {
            abort(403);
        }
    }
}
