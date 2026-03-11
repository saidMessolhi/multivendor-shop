<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $vendor  = Auth::user()->vendor;
        $reviews = Review::whereHas('product', fn($q) => $q->where('vendor_id', $vendor->id))
            ->with(['product', 'user'])
            ->latest()
            ->paginate(15);

        return view('vendor.reviews.index', compact('reviews'));
    }

    public function reply(Request $request, Review $review)
    {
        $vendor = Auth::user()->vendor;
        abort_unless($review->product->vendor_id === $vendor->id, 403);

        $request->validate(['reply' => 'required|string|max:1000']);

        $review->update([
            'vendor_reply'      => $request->reply,
            'vendor_replied_at' => now(),
        ]);

        return back()->with('success', 'Reply posted.');
    }
}
