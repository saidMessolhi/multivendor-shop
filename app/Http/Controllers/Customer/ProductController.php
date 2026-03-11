<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['vendor', 'category'])->inStock();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->category) {
            $query->where('category_id', $request->category);
        }
        if ($request->vendor) {
            $query->where('vendor_id', $request->vendor);
        }
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->featured) {
            $query->featured();
        }

        $query = match($request->sort) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'rating'     => $query->orderByDesc('rating_avg'),
            'latest'     => $query->latest(),
            default      => $query->latest(),
        };

        $products   = $query->paginate(config('shop.products_per_page', 12))->withQueryString();
        $categories = Category::where('is_active', true)->get();

        return view('shop.products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        abort_unless($product->status === 'active', 404);

        $product->load(['vendor', 'category', 'variants', 'reviews.user']);

        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('shop.products.show', compact('product', 'related'));
    }

    public function byCategory(\App\Models\Category $category)
    {
        $products = Product::active()
            ->where('category_id', $category->id)
            ->with(['vendor'])
            ->paginate(config('shop.products_per_page', 12));

        return view('shop.products.index', compact('products', 'category'));
    }

    public function search(Request $request)
    {
        $q = $request->get('q');

        $products = Product::active()
            ->where('name', 'like', "%{$q}%")
            ->orWhere('description', 'like', "%{$q}%")
            ->with(['vendor'])
            ->paginate(12)
            ->withQueryString();

        return view('shop.products.index', compact('products', 'q'));
    }
}
