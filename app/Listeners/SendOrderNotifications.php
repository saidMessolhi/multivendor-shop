<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Mail\OrderConfirmationMail;
use App\Mail\NewOrderVendorMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderNotifications implements ShouldQueue
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order->load('items.vendor.user', 'user');

        // Email customer
        Mail::to($order->user->email)->queue(new OrderConfirmationMail($order));

        // Email each unique vendor
        $order->items->groupBy('vendor_id')->each(function ($items, $vendorId) use ($order) {
            $vendor = $items->first()->vendor;
            Mail::to($vendor->user->email)->queue(new NewOrderVendorMail($order, $items));
        });
    }
}
