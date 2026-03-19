<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $items = Wishlist::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json($items->map(fn($i) => [
            'id'      => $i->id,
            'product' => [
                'id'          => $i->product->id,
                'name'        => $i->product->name,
                'price'       => (float) $i->product->current_price,
                'first_image' => $i->product->first_image_url,
            ],
        ]));
    }

    public function toggle(Request $request, Product $product)
    {
        $existing = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['wishlisted' => false]);
        }

        Wishlist::create(['user_id' => $request->user()->id, 'product_id' => $product->id]);
        return response()->json(['wishlisted' => true]);
    }
}
