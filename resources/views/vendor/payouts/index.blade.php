@extends('layouts.vendor')
@section('title','Payouts')
@section('page-title','Payouts')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <p class="text-sm text-slate-500 mb-1">Available Balance</p>
        <p class="text-3xl font-extrabold text-slate-900">${{ number_format($vendor->balance, 2) }}</p>
        <form method="POST" action="{{ route('vendor.payouts.request') }}" class="mt-5 space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Amount</label>
                <input type="number" name="amount" min="10" max="{{ $vendor->balance }}" step="0.01"
                    placeholder="Min $10.00"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Method</label>
                <select name="method" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="paypal">PayPal</option>
                    <option value="stripe">Stripe</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>
            <button type="submit" class="w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl text-sm transition-colors"
                {{ $vendor->balance < 10 ? 'disabled' : '' }}>
                Request Payout
            </button>
        </form>
    </div>
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-50"><h3 class="font-semibold text-slate-800">Payout History</h3></div>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 uppercase tracking-wide border-b bg-gray-50">
                <th class="px-5 py-3">Amount</th>
                <th class="px-5 py-3">Method</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3">Date</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($payouts as $payout)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-bold">${{ number_format($payout->amount, 2) }}</td>
                    <td class="px-5 py-3 capitalize">{{ str_replace('_',' ',$payout->method) }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ match($payout->status) {'completed'=>'bg-green-100 text-green-700','failed'=>'bg-red-100 text-red-700',default=>'bg-yellow-100 text-yellow-700'} }}">
                            {{ ucfirst($payout->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-400">{{ $payout->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-5 py-10 text-center text-gray-400">No payouts yet</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-5">{{ $payouts->links() }}</div>
    </div>
</div>
@endsection
