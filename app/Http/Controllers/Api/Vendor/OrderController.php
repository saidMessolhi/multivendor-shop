<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $vendor = $request->user()->vendor;
        $items  = OrderItem::with('order.user')
            ->where('vendor_id', $vendor->id)
            ->latest()
            ->paginate(15);

        return response()->json([
            'data' => $items->map(fn($i) => $this->itemResource($i)),
        ]);
    }

    public function show(Request $request, OrderItem $orderItem)
    {
        abort_unless($orderItem->vendor_id === $request->user()->vendor->id, 403);
        return response()->json($this->itemResource($orderItem->load('order.user')));
    }

    public function markShipped(Request $request, OrderItem $orderItem)
    {
        abort_unless($orderItem->vendor_id === $request->user()->vendor->id, 403);
        $orderItem->update(['status' => 'shipped']);
        return response()->json(['status' => 'shipped']);
    }

    public function markDelivered(Request $request, OrderItem $orderItem)
    {
        abort_unless($orderItem->vendor_id === $request->user()->vendor->id, 403);
        $orderItem->update(['status' => 'delivered']);
        return response()->json(['status' => 'delivered']);
    }

    private function itemResource(OrderItem $item): array
    {
        return [
            'id'            => $item->id,
            'order_number'  => $item->order->order_number,
            'product_name'  => $item->product_name,
            'quantity'      => $item->quantity,
            'unit_price'    => (float) $item->unit_price,
            'vendor_amount' => (float) $item->vendor_amount,
            'status'        => $item->status,
            'customer'      => $item->order->user->name,
            'created_at'    => $item->created_at->toISOString(),
        ];
    }
}
