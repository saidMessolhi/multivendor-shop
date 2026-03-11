<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function edit()
    {
        $vendor = Auth::user()->vendor;
        return view('vendor.settings', compact('vendor'));
    }

    public function update(Request $request)
    {
        $vendor = Auth::user()->vendor;

        $request->validate([
            'store_name'  => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string|max:255',
            'city'        => 'nullable|string|max:100',
            'country'     => 'nullable|string|max:100',
            'logo'        => 'nullable|image|max:1024',
            'banner'      => 'nullable|image|max:2048',
        ]);

        $data = $request->only('store_name', 'description', 'phone', 'address', 'city', 'country');

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store("vendors/{$vendor->id}", 'public');
        }
        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner')->store("vendors/{$vendor->id}", 'public');
        }

        $vendor->update($data);

        return back()->with('success', 'Store settings updated.');
    }

    public function payments()
    {
        $vendor = Auth::user()->vendor;
        return view('vendor.settings-payments', compact('vendor'));
    }

    public function updatePayments(Request $request)
    {
        $vendor = Auth::user()->vendor;

        $request->validate([
            'paypal_email' => 'nullable|email',
        ]);

        $vendor->update($request->only('paypal_email'));

        return back()->with('success', 'Payment settings updated.');
    }
}
