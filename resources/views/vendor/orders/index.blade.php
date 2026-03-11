@extends('layouts.vendor')
@section('title','Orders')
@section('page-title','Orders')
@section('content')
<div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-50 flex items-center justify-between">
        <h3 class="font-semibold text-slate-800">All Orders</h3>
        <form method="GET" class="flex gap-2">
            <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none">
                <option value="">All Statuses</option>
                @foreach(['pending','shipped','delivered','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 uppercase tracking-wide border-b bg-gray-50">
                <th class="px-5 py-3">Order</th>
                <th class="px-5 py-3">Customer</th>
                <th class="px-5 py-3">Product</th>
                <th class="px-5 py-3">Amount</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3">Date</th>
                <th class="px-5 py-3">Action</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($items as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-mono text-xs text-blue-600">{{ $item->order->order_number }}</td>
                    <td class="px-5 py-3">{{ $item->order->user->name }}</td>
                    <td class="px-5 py-3 max-w-[150px] truncate">{{ $item->product_name }}</td>
                    <td class="px-5 py-3 font-semibold">${{ number_format($item->vendor_amount, 2) }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ match($item->status) {'delivered'=>'bg-green-100 text-green-700','shipped'=>'bg-blue-100 text-blue-700','cancelled'=>'bg-red-100 text-red-700',default=>'bg-yellow-100 text-yellow-700'} }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-400">{{ $item->created_at->format('M d') }}</td>
                    <td class="px-5 py-3">
                        <a href="{{ route('vendor.orders.show', $item) }}" class="text-blue-600 hover:underline text-xs">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400">No orders yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-5">{{ $items->links() }}</div>
</div>
@endsection
