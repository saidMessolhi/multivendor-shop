<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title'  => 'nullable|string|max:255',
            'body'   => 'nullable|string|max:2000',
        ]);

        $existing = Review::where('product_id', $product->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        Review::create([
            'product_id'  => $product->id,
            'user_id'     => Auth::id(),
            'rating'      => $request->rating,
            'title'       => $request->title,
            'body'        => $request->body,
            'is_approved' => ! config('shop.reviews_require_approval', true),
        ]);

        return back()->with('success', 'Review submitted successfully.');
    }

    public function destroy(Review $review)
    {
        abort_unless($review->user_id === Auth::id(), 403);
        $review->delete();
        return back()->with('success', 'Review deleted.');
    }
}
