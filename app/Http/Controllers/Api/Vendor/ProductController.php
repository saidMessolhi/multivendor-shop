<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $vendor   = $request->user()->vendor;
        $products = Product::byVendor($vendor->id)->latest()->paginate(15);

        return response()->json([
            'data' => $products->map(fn($p) => [
                'id'          => $p->id,
                'name'        => $p->name,
                'price'       => (float) $p->price,
                'sale_price'  => $p->sale_price ? (float) $p->sale_price : null,
                'stock'       => $p->stock,
                'status'      => $p->status,
                'first_image' => $p->first_image_url,
                'rating_avg'  => (float) $p->rating_avg,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'price'  => 'required|numeric|min:0',
            'stock'  => 'required|integer|min:0',
            'status' => 'required|in:active,draft,inactive',
        ]);

        $vendor  = $request->user()->vendor;
        $product = Product::create([
            'vendor_id'         => $vendor->id,
            'name'              => $request->name,
            'slug'              => Str::slug($request->name) . '-' . Str::random(5),
            'description'       => $request->description,
            'short_description' => $request->short_description,
            'price'             => $request->price,
            'sale_price'        => $request->sale_price ?: null,
            'stock'             => $request->stock,
            'category_id'       => $request->category_id,
            'status'            => $request->status,
            'sku'               => $request->sku ?: strtoupper(Str::random(8)),
        ]);

        return response()->json($product, 201);
    }

    public function show(Request $request, Product $product)
    {
        abort_unless($product->vendor_id === $request->user()->vendor->id, 403);
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        abort_unless($product->vendor_id === $request->user()->vendor->id, 403);
        $product->updateQuietly($request->only([
            'name', 'description', 'short_description',
            'price', 'sale_price', 'stock', 'status', 'category_id',
        ]));
        return response()->json($product->fresh());
    }

    public function destroy(Request $request, Product $product)
    {
        abort_unless($product->vendor_id === $request->user()->vendor->id, 403);
        $product->delete();
        return response()->json(['message' => 'Product deleted']);
    }

    public function toggleStatus(Request $request, Product $product)
    {
        abort_unless($product->vendor_id === $request->user()->vendor->id, 403);
        $product->updateQuietly(['status' => $product->status === 'active' ? 'inactive' : 'active']);
        return response()->json(['status' => $product->status]);
    }
}
