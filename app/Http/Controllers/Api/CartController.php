<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $items = CartItem::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        $subtotal = $items->sum(fn($i) => $i->product->current_price * $i->quantity);

        return response()->json([
            'items'    => $items->map(fn($i) => $this->cartItemResource($i)),
            'subtotal' => round($subtotal, 2),
            'count'    => $items->sum('quantity'),
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if (!$product->isInStock()) {
            return response()->json(['message' => 'Product is out of stock'], 422);
        }

        $cartItem = CartItem::updateOrCreate(
            ['user_id' => $request->user()->id, 'product_id' => $product->id],
            ['quantity' => \DB::raw("quantity + {$request->quantity}")]
        );

        return response()->json($this->cartItemResource($cartItem->fresh('product')), 201);
    }

    public function update(Request $request, CartItem $cartItem)
    {
        abort_unless($cartItem->user_id === $request->user()->id, 403);
        $request->validate(['quantity' => 'required|integer|min:1']);
        $cartItem->update(['quantity' => $request->quantity]);
        return response()->json($this->cartItemResource($cartItem->load('product')));
    }

    public function remove(Request $request, CartItem $cartItem)
    {
        abort_unless($cartItem->user_id === $request->user()->id, 403);
        $cartItem->delete();
        return response()->json(['message' => 'Removed from cart']);
    }

    public function clear(Request $request)
    {
        CartItem::where('user_id', $request->user()->id)->delete();
        return response()->json(['message' => 'Cart cleared']);
    }

    private function cartItemResource(CartItem $item): array
    {
        return [
            'id'       => $item->id,
            'quantity' => $item->quantity,
            'product'  => [
                'id'            => $item->product->id,
                'name'          => $item->product->name,
                'price'         => (float) $item->product->current_price,
                'image'         => $item->product->first_image_url,
                'in_stock'      => $item->product->isInStock(),
                'vendor'        => $item->product->vendor?->store_name,
            ],
            'subtotal' => round($item->product->current_price * $item->quantity, 2),
        ];
    }
}
