<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private OrderService $orderService
    ) {}

    public function index()
    {
        $items    = $this->cartService->getItems();
        $subtotal = $this->cartService->getSubtotal();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $user      = Auth::user();
        $addresses = $user->addresses()->get();

        return view('shop.checkout', compact('items', 'subtotal', 'addresses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method'    => 'required|in:stripe,paypal,cod',
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'email'             => 'required|email',
            'phone'             => 'nullable|string|max:20',
            'address_line_1'    => 'required|string|max:255',
            'city'              => 'required|string|max:100',
            'postal_code'       => 'required|string|max:20',
            'country'           => 'required|string|max:100',
            'notes'             => 'nullable|string|max:500',
            'coupon_code'       => 'nullable|string|max:50',
        ]);

        $shippingAddress = $request->only([
            'first_name','last_name','email','phone',
            'address_line_1','address_line_2','city','state','postal_code','country',
        ]);

        $order = $this->orderService->createFromCart(
            Auth::id(),
            $shippingAddress,
            $request->payment_method,
            $request->coupon_code,
            null,
            $request->notes
        );

        return match ($request->payment_method) {
            'stripe'  => $this->handleStripePayment($order),
            'paypal'  => $this->handlePaypalPayment($order),
            'cod'     => redirect()->route('checkout.success', $order)->with('success', 'Order placed! Pay on delivery.'),
        };
    }

    private function handleStripePayment(Order $order)
    {
        Stripe::setApiKey(config('cashier.secret'));

        $intent = PaymentIntent::create([
            'amount'               => (int) ($order->total_amount * 100),
            'currency'             => 'usd',
            'automatic_payment_methods' => ['enabled' => true],
            'metadata'             => ['order_id' => $order->id, 'order_number' => $order->order_number],
        ]);

        $order->update(['stripe_payment_intent_id' => $intent->id]);

        Transaction::create([
            'order_id'       => $order->id,
            'gateway'        => 'stripe',
            'transaction_id' => $intent->id,
            'amount'         => $order->total_amount,
            'status'         => 'pending',
        ]);

        return view('shop.payment.stripe', [
            'order'         => $order,
            'clientSecret'  => $intent->client_secret,
            'stripeKey'     => config('cashier.key'),
        ]);
    }

    private function handlePaypalPayment(Order $order)
    {
        $provider = new \Srmklive\PayPal\Services\PayPal;
        $provider->setApiCredentials(config('paypal'));
        $token = $provider->getAccessToken();
        $provider->setAccessToken($token);

        $paypalOrder = $provider->createOrder([
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount'      => ['currency_code' => 'USD', 'value' => number_format($order->total_amount, 2)],
                'description' => 'Order ' . $order->order_number,
            ]],
            'application_context' => [
                'return_url' => route('payment.paypal.return'),
                'cancel_url' => route('payment.paypal.cancel'),
            ],
        ]);

        $order->update(['paypal_order_id' => $paypalOrder['id']]);

        $approvalLink = collect($paypalOrder['links'])->firstWhere('rel', 'approve')['href'];
        return redirect($approvalLink);
    }

    public function stripeReturn(Request $request)
    {
        $order = Order::where('stripe_payment_intent_id', $request->payment_intent)->firstOrFail();

        Stripe::setApiKey(config('cashier.secret'));
        $intent = PaymentIntent::retrieve($request->payment_intent);

        if ($intent->status === 'succeeded') {
            $this->orderService->markAsPaid($order, $intent->id, 'stripe');
            return redirect()->route('checkout.success', $order)->with('success', 'Payment successful!');
        }

        return redirect()->route('checkout.index')->with('error', 'Payment failed. Please try again.');
    }

    public function paypalReturn(Request $request)
    {
        $order = Order::where('paypal_order_id', $request->token)->firstOrFail();

        $provider = new \Srmklive\PayPal\Services\PayPal;
        $provider->setApiCredentials(config('paypal'));
        $token = $provider->getAccessToken();
        $provider->setAccessToken($token);

        $result = $provider->capturePaymentOrder($request->token);

        if (isset($result['status']) && $result['status'] === 'COMPLETED') {
            $this->orderService->markAsPaid($order, $result['id'], 'paypal');
            return redirect()->route('checkout.success', $order)->with('success', 'Payment successful!');
        }

        return redirect()->route('checkout.index')->with('error', 'PayPal payment failed.');
    }

    public function paypalCancel()
    {
        return redirect()->route('cart.index')->with('info', 'PayPal payment was cancelled.');
    }

    public function success(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);
        return view('shop.checkout-success', compact('order'));
    }
}
