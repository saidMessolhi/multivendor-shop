<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Events\OrderPlaced;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private CommissionService $commissionService
    ) {}

    /**
     * Create an order from the current cart.
     */
    public function createFromCart(
        int $userId,
        array $shippingAddress,
        string $paymentMethod,
        ?string $couponCode = null,
        ?array $billingAddress = null,
        ?string $notes = null
    ): Order {
        $cartItems = CartItem::with(['product.vendor', 'variant'])
            ->where('user_id', $userId)
            ->get();

        if ($cartItems->isEmpty()) {
            throw new \Exception('Cart is empty.');
        }

        // Validate stock
        foreach ($cartItems as $item) {
            $available = $item->variant ? $item->variant->stock : $item->product->stock;
            if ($item->quantity > $available) {
                throw new \Exception("Insufficient stock for: {$item->product->name}");
            }
        }

        $subtotal = $cartItems->sum(fn($i) => $i->line_total);

        // Apply coupon
        $discountAmount = 0;
        if ($couponCode) {
            $coupon = Coupon::where('code', strtoupper($couponCode))->first();
            if ($coupon && $coupon->isValid() && $subtotal >= ($coupon->min_order_amount ?? 0)) {
                $discountAmount = $coupon->calculateDiscount($subtotal);
            }
        }

        $shippingAmount = $this->calculateShipping($cartItems, $shippingAddress);
        $taxAmount      = $this->calculateTax($subtotal - $discountAmount);
        $totalAmount    = $subtotal - $discountAmount + $shippingAmount + $taxAmount;

        return DB::transaction(function () use (
            $userId, $shippingAddress, $billingAddress,
            $paymentMethod, $subtotal, $discountAmount,
            $shippingAmount, $taxAmount, $totalAmount,
            $cartItems, $couponCode, $notes
        ) {
            $order = Order::create([
                'user_id'          => $userId,
                'payment_method'   => $paymentMethod,
                'payment_status'   => $paymentMethod === 'cod' ? 'pending' : 'pending',
                'status'           => 'pending',
                'subtotal'         => $subtotal,
                'shipping_amount'  => $shippingAmount,
                'tax_amount'       => $taxAmount,
                'discount_amount'  => $discountAmount,
                'total_amount'     => $totalAmount,
                'shipping_address' => $shippingAddress,
                'billing_address'  => $billingAddress ?? $shippingAddress,
                'coupon_code'      => $couponCode,
                'notes'            => $notes,
            ]);

            foreach ($cartItems as $cartItem) {
                $vendor  = $cartItem->product->vendor;
                $price   = $cartItem->variant?->price ?? $cartItem->product->current_price;
                $rate    = $vendor->getEffectiveCommissionRate();
                $lineTotal = $price * $cartItem->quantity;

                [$commissionAmount, $vendorAmount] = $this->commissionService->calculate($lineTotal, $rate);

                OrderItem::create([
                    'order_id'           => $order->id,
                    'vendor_id'          => $vendor->id,
                    'product_id'         => $cartItem->product_id,
                    'product_variant_id' => $cartItem->product_variant_id,
                    'product_name'       => $cartItem->product->name,
                    'product_image'      => $cartItem->product->first_image_url,
                    'variant_label'      => $cartItem->variant?->label,
                    'quantity'           => $cartItem->quantity,
                    'unit_price'         => $price,
                    'commission_rate'    => $rate,
                    'commission_amount'  => $commissionAmount,
                    'vendor_amount'      => $vendorAmount,
                ]);

                // Decrement stock
                if ($cartItem->variant) {
                    $cartItem->variant->decrement('stock', $cartItem->quantity);
                } else {
                    $cartItem->product->decrement('stock', $cartItem->quantity);
                }
            }

            // Increment coupon usage
            if ($couponCode) {
                Coupon::where('code', strtoupper($couponCode))->increment('used_count');
            }

            // Clear cart
            CartItem::where('user_id', $userId)->delete();

            event(new OrderPlaced($order));

            return $order;
        });
    }

    public function markAsPaid(Order $order, string $transactionId, string $gateway): void
    {
        DB::transaction(function () use ($order, $transactionId, $gateway) {
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'processing',
                'paid_at'        => now(),
            ]);

            // Credit vendor balances
            foreach ($order->items as $item) {
                $item->vendor->increment('balance', $item->vendor_amount);
            }
        });
    }

    public function cancelOrder(Order $order): void
    {
        if (! $order->isCancellable()) {
            throw new \Exception('This order cannot be cancelled.');
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'cancelled']);

            // Restore stock
            foreach ($order->items as $item) {
                if ($item->product_variant_id) {
                    $item->variant?->increment('stock', $item->quantity);
                } else {
                    $item->product?->increment('stock', $item->quantity);
                }
            }
        });
    }

    private function calculateShipping($cartItems, array $address): float
    {
        // Basic flat rate — extend with zone-based logic
        return 5.99;
    }

    private function calculateTax(float $subtotal): float
    {
        // Basic tax rate — extend with locale-based logic
        $rate = (float) config('shop.tax_rate', 0);
        return round($subtotal * ($rate / 100), 2);
    }
}
