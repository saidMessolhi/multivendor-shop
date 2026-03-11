<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function getItems()
    {
        return CartItem::with(['product.vendor', 'variant'])
            ->where($this->getCartCondition())
            ->get();
    }

    public function add(int $productId, int $quantity = 1, ?int $variantId = null): CartItem
    {
        $product = Product::active()->findOrFail($productId);

        $existing = CartItem::where($this->getCartCondition())
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($existing) {
            $existing->increment('quantity', $quantity);
            return $existing->fresh();
        }

        return CartItem::create([
            ...$this->getCartCondition(),
            'product_id'         => $productId,
            'product_variant_id' => $variantId,
            'quantity'           => $quantity,
        ]);
    }

    public function update(int $cartItemId, int $quantity): void
    {
        $item = CartItem::where($this->getCartCondition())->findOrFail($cartItemId);
        if ($quantity <= 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $quantity]);
        }
    }

    public function remove(int $cartItemId): void
    {
        CartItem::where($this->getCartCondition())->findOrFail($cartItemId)->delete();
    }

    public function clear(): void
    {
        CartItem::where($this->getCartCondition())->delete();
    }

    public function getCount(): int
    {
        return CartItem::where($this->getCartCondition())->sum('quantity');
    }

    public function getSubtotal(): float
    {
        return $this->getItems()->sum(fn($i) => $i->line_total);
    }

    public function mergeGuestCart(string $sessionId, int $userId): void
    {
        $guestItems = CartItem::where('session_id', $sessionId)->get();

        foreach ($guestItems as $guestItem) {
            $existing = CartItem::where('user_id', $userId)
                ->where('product_id', $guestItem->product_id)
                ->where('product_variant_id', $guestItem->product_variant_id)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $guestItem->quantity);
                $guestItem->delete();
            } else {
                $guestItem->update(['user_id' => $userId, 'session_id' => null]);
            }
        }
    }

    private function getCartCondition(): array
    {
        if (Auth::check()) {
            return ['user_id' => Auth::id()];
        }
        return ['session_id' => session()->getId()];
    }
}
