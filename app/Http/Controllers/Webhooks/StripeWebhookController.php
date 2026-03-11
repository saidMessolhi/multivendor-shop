<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('cashier.webhook.secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        match ($event->type) {
            'payment_intent.succeeded' => $this->handlePaymentSucceeded($event->data->object),
            default => null,
        };

        return response()->json(['status' => 'ok']);
    }

    private function handlePaymentSucceeded($intent): void
    {
        $order = Order::where('stripe_payment_intent_id', $intent->id)->first();
        if ($order && ! $order->isPaid()) {
            app(OrderService::class)->markAsPaid($order, $intent->id, 'stripe');
        }
    }
}
