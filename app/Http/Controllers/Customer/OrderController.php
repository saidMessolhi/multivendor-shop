<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.vendor', 'items.product'])
            ->latest()
            ->paginate(10);

        return view('shop.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        $order->load(['items.vendor', 'items.product', 'transactions']);

        return view('shop.orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        try {
            $this->orderService->cancelOrder($order);
            return back()->with('success', 'Order cancelled successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function invoice(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);
        return view('shop.orders.invoice', compact('order'));
    }
}
