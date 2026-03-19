<?php
// ── CategoryController ────────────────────────────────────────────────────

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();
        return response()->json($categories->map(fn($c) => [
            'id'             => $c->id,
            'name'           => $c->name,
            'slug'           => $c->slug,
            'products_count' => $c->products_count,
            'image_url'      => $c->image ? asset('storage/' . $c->image) : null,
        ]));
    }

    public function products(Request $request, Category $category)
    {
        $products = Product::with('vendor')
            ->where('category_id', $category->id)
            ->active()
            ->paginate(15);

        return response()->json([
            'category' => ['id' => $category->id, 'name' => $category->name],
            'data'     => $products->map(fn($p) => [
                'id'            => $p->id,
                'name'          => $p->name,
                'price'         => (float) $p->current_price,
                'sale_price'    => $p->sale_price ? (float) $p->sale_price : null,
                'first_image'   => $p->first_image_url,
                'rating_avg'    => (float) $p->rating_avg,
                'in_stock'      => $p->isInStock(),
                'vendor'        => $p->vendor?->store_name,
            ]),
        ]);
    }
}
