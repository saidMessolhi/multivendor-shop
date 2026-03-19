<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index(Request $request)
    {
        $orders = Order::with('items')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return response()->json([
            'data' => $orders->map(fn($o) => $this->orderResource($o)),
            'pagination' => [
                'total'        => $orders->total(),
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:stripe,paypal,cod',
            'first_name'     => 'required|string',
            'last_name'      => 'required|string',
            'email'          => 'required|email',
            'address_line_1' => 'required|string',
            'city'           => 'required|string',
            'postal_code'    => 'required|string',
            'country'        => 'required|string',
        ]);

        $shippingAddress = [
            'first_name'     => $request->first_name,
            'last_name'      => $request->last_name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'city'           => $request->city,
            'postal_code'    => $request->postal_code,
            'country'        => $request->country,
        ];

        try {
            $order = $this->orderService->createFromCart(
                userId:          $request->user()->id,
                shippingAddress: $shippingAddress,
                paymentMethod:   $request->payment_method,
                couponCode:      $request->coupon_code,
                notes:           $request->notes,
            );

            return response()->json($this->orderResource($order->load('items')), 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        return response()->json($this->orderResource($order->load('items.product')));
    }

    public function cancel(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        try {
            $this->orderService->cancelOrder($order);
            return response()->json(['message' => 'Order cancelled']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    private function orderResource(Order $order): array
    {
        return [
            'id'               => $order->id,
            'order_number'     => $order->order_number,
            'status'           => $order->status,
            'payment_method'   => $order->payment_method,
            'payment_status'   => $order->payment_status,
            'subtotal'         => (float) $order->subtotal,
            'shipping_amount'  => (float) $order->shipping_amount,
            'tax_amount'       => (float) $order->tax_amount,
            'total_amount'     => (float) $order->total_amount,
            'shipping_address' => $order->shipping_address,
            'created_at'       => $order->created_at->toISOString(),
            'items'            => $order->items->map(fn($i) => [
                'id'           => $i->id,
                'product_name' => $i->product_name,
                'quantity'     => $i->quantity,
                'unit_price'   => (float) $i->unit_price,
                'subtotal'     => (float) ($i->unit_price * $i->quantity),
                'status'       => $i->status,
                'image'        => $i->product?->first_image_url,
            ]),
        ];
    }
}
