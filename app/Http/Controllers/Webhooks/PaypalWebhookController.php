<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class PaypalWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $eventType = $request->input('event_type');

        if ($eventType === 'PAYMENT.CAPTURE.COMPLETED') {
            $orderId = $request->input('resource.supplementary_data.related_ids.order_id');
            $order   = Order::where('paypal_order_id', $orderId)->first();

            if ($order && ! $order->isPaid()) {
                app(OrderService::class)->markAsPaid($order, $orderId, 'paypal');
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
