<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $items = Wishlist::where('user_id', Auth::id())
            ->with('product.vendor')
            ->latest()
            ->get();

        return view('shop.wishlist', compact('items'));
    }

    public function toggle(Product $product)
    {
        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'Removed from wishlist.';
        } else {
            Wishlist::create(['user_id' => Auth::id(), 'product_id' => $product->id]);
            $message = 'Added to wishlist!';
        }

        return back()->with('success', $message);
    }
}
