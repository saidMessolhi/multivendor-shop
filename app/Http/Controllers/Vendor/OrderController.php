<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $vendor = Auth::user()->vendor;

        $items = OrderItem::where('vendor_id', $vendor->id)
            ->with(['order.user', 'product'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return view('vendor.orders.index', compact('items'));
    }

    public function show(OrderItem $orderItem)
    {
        abort_unless($orderItem->vendor_id === Auth::user()->vendor->id, 403);
        $orderItem->load(['order.user', 'product', 'variant']);
        return view('vendor.orders.show', compact('orderItem'));
    }

    public function markShipped(Request $request, OrderItem $orderItem)
    {
        abort_unless($orderItem->vendor_id === Auth::user()->vendor->id, 403);
        $request->validate(['tracking_number' => 'nullable|string|max:100']);

        $orderItem->update([
            'status'          => 'shipped',
            'tracking_number' => $request->tracking_number,
            'shipped_at'      => now(),
        ]);

        return back()->with('success', 'Order marked as shipped.');
    }

    public function markDelivered(OrderItem $orderItem)
    {
        abort_unless($orderItem->vendor_id === Auth::user()->vendor->id, 403);

        $orderItem->update([
            'status'       => 'delivered',
            'delivered_at' => now(),
        ]);

        return back()->with('success', 'Order marked as delivered.');
    }
}
