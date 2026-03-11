@extends('layouts.vendor')
@section('title','Order Detail')
@section('page-title','Order Detail')
@section('content')
<div class="max-w-2xl bg-white rounded-xl border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-xs text-slate-400 mb-1">Order Number</p>
            <p class="font-mono font-bold text-slate-800">{{ $orderItem->order->order_number }}</p>
        </div>
        <span class="px-3 py-1 rounded-full text-sm font-semibold
            {{ match($orderItem->status) {'delivered'=>'bg-green-100 text-green-700','shipped'=>'bg-blue-100 text-blue-700','cancelled'=>'bg-red-100 text-red-700',default=>'bg-yellow-100 text-yellow-700'} }}">
            {{ ucfirst($orderItem->status) }}
        </span>
    </div>
    <div class="flex items-center gap-4 mb-6 p-4 bg-gray-50 rounded-xl">
        <img src="{{ $orderItem->product_image ?? asset('images/placeholder.png') }}" class="w-16 h-16 rounded-xl object-cover">
        <div>
            <p class="font-semibold text-slate-800">{{ $orderItem->product_name }}</p>
            <p class="text-sm text-slate-400">Qty: {{ $orderItem->quantity }} × ${{ number_format($orderItem->unit_price, 2) }}</p>
            <p class="text-sm font-bold text-green-600 mt-1">Your earnings: ${{ number_format($orderItem->vendor_amount, 2) }}</p>
        </div>
    </div>
    <div class="mb-6 p-4 bg-gray-50 rounded-xl">
        <p class="text-xs font-semibold text-slate-500 uppercase mb-2">Customer</p>
        <p class="font-semibold text-slate-800">{{ $orderItem->order->user->name }}</p>
        <p class="text-sm text-slate-500">{{ $orderItem->order->user->email }}</p>
    </div>
    @if($orderItem->status === 'pending')
    <form method="POST" action="{{ route('vendor.orders.ship', $orderItem) }}" class="mb-3">
        @csrf
        <div class="flex gap-3">
            <input type="text" name="tracking_number" placeholder="Tracking number (optional)"
                class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition-colors">
                Mark Shipped
            </button>
        </div>
    </form>
    @endif
    @if($orderItem->status === 'shipped')
    <form method="POST" action="{{ route('vendor.orders.deliver', $orderItem) }}">
        @csrf
        <button type="submit" class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl text-sm transition-colors">
            Mark Delivered
        </button>
    </form>
    @endif
</div>
@endsection
