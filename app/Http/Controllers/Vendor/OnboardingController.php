<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function show()
    {
        if (Auth::user()->vendor) {
            return redirect()->route('vendor.dashboard');
        }
        return view('vendor.onboarding');
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_name'  => 'required|string|max:255|unique:vendors',
            'description' => 'nullable|string|max:2000',
            'phone'       => 'nullable|string|max:20',
        ]);

        $user = Auth::user();

        if (! $user->hasRole('vendor')) {
            $user->assignRole('vendor');
        }

        Vendor::create([
            'user_id'     => $user->id,
            'store_name'  => $request->store_name,
            'description' => $request->description,
            'phone'       => $request->phone,
            'status'      => 'pending',
        ]);

        return redirect()->route('home')
            ->with('success', 'Application submitted! We will review your store and notify you by email.');
    }
}
