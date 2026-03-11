<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayoutController extends Controller
{
    public function index()
    {
        $vendor  = Auth::user()->vendor;
        $payouts = Payout::where('vendor_id', $vendor->id)->latest()->paginate(15);
        return view('vendor.payouts.index', compact('vendor', 'payouts'));
    }

    public function request(Request $request)
    {
        $vendor = Auth::user()->vendor;

        $request->validate([
            'amount' => "required|numeric|min:10|max:{$vendor->balance}",
            'method' => 'required|in:stripe,paypal,bank_transfer',
        ]);

        if ($vendor->balance < $request->amount) {
            return back()->with('error', 'Insufficient balance.');
        }

        Payout::create([
            'vendor_id' => $vendor->id,
            'amount'    => $request->amount,
            'method'    => $request->method,
            'status'    => 'pending',
        ]);

        $vendor->decrement('balance', $request->amount);

        return back()->with('success', 'Payout request submitted.');
    }
}
