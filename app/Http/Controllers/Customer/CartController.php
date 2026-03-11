<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index()
    {
        $items    = $this->cartService->getItems();
        $subtotal = $this->cartService->getSubtotal();
        return view('shop.cart', compact('items', 'subtotal'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'integer|min:1|max:100',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $item = $this->cartService->add(
            $request->product_id,
            $request->quantity ?? 1,
            $request->variant_id
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count'   => $this->cartService->getCount(),
                'message' => 'Item added to cart.',
            ]);
        }

        return back()->with('success', 'Item added to cart.');
    }

    public function update(Request $request, CartItem $item)
    {
        $request->validate(['quantity' => 'required|integer|min:0|max:100']);
        $this->cartService->update($item->id, $request->quantity);

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'subtotal' => $this->cartService->getSubtotal(),
            ]);
        }

        return back();
    }

    public function remove(CartItem $item)
    {
        $this->cartService->remove($item->id);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Item removed from cart.');
    }

    public function count()
    {
        return response()->json(['count' => $this->cartService->getCount()]);
    }
}
