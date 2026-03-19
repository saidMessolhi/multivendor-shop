<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['vendor', 'category'])
            ->active()
            ->when($request->search,   fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->category, fn($q) => $q->where('category_id', $request->category))
            ->when($request->vendor,   fn($q) => $q->where('vendor_id', $request->vendor))
            ->when($request->min_price, fn($q) => $q->where('price', '>=', $request->min_price))
            ->when($request->max_price, fn($q) => $q->where('price', '<=', $request->max_price))
            ->when($request->featured,  fn($q) => $q->featured())
            ->when($request->sort === 'price_asc',  fn($q) => $q->orderBy('price'))
            ->when($request->sort === 'price_desc', fn($q) => $q->orderByDesc('price'))
            ->when($request->sort === 'newest',     fn($q) => $q->latest())
            ->when($request->sort === 'rating',     fn($q) => $q->orderByDesc('rating_avg'))
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'data'       => $products->map(fn($p) => $this->productResource($p)),
            'pagination' => [
                'total'        => $products->total(),
                'per_page'     => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
            ],
        ]);
    }

    public function show(Product $product)
    {
        $product->load(['vendor', 'category', 'reviews.user']);
        return response()->json($this->productResource($product, true));
    }

    public function productResource(Product $product, bool $full = false): array
    {
        $data = [
            'id'                => $product->id,
            'name'              => $product->name,
            'slug'              => $product->slug,
            'short_description' => $product->short_description,
            'price'             => (float) $product->price,
            'sale_price'        => $product->sale_price ? (float) $product->sale_price : null,
            'current_price'     => (float) $product->current_price,
            'discount_percent'  => $product->discount_percentage,
            'is_on_sale'        => $product->isOnSale(),
            'in_stock'          => $product->isInStock(),
            'stock'             => $product->stock,
            'rating_avg'        => (float) $product->rating_avg,
            'rating_count'      => $product->rating_count,
            'images'            => collect($product->images ?? [])->map(fn($img) => asset('storage/' . $img)),
            'first_image_url'   => $product->first_image_url,
            'category'          => $product->category ? ['id' => $product->category->id, 'name' => $product->category->name] : null,
            'vendor'            => $product->vendor ? ['id' => $product->vendor->id, 'store_name' => $product->vendor->store_name, 'logo_url' => $product->vendor->logo_url] : null,
        ];

        if ($full) {
            $data['description'] = $product->description;
            $data['reviews']     = $product->reviews->map(fn($r) => [
                'id'         => $r->id,
                'rating'     => $r->rating,
                'comment'    => $r->comment,
                'user'       => ['name' => $r->user->name, 'avatar' => $r->user->avatar_url],
                'created_at' => $r->created_at->diffForHumans(),
            ]);
        }

        return $data;
    }
}
