<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $user   = Auth::user();
        $orders = $user->orders()->latest()->take(5)->get();
        return view('shop.profile', compact('user', 'orders'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'phone'  => 'nullable|string|max:20',
            'avatar' => 'nullable|image|max:1024',
        ]);

        $data = $request->only('name', 'phone');

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        Auth::user()->update($data);

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => 'required|confirmed|min:8',
        ]);

        Auth::user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password updated.');
    }

    public function addresses()
    {
        $addresses = Auth::user()->addresses()->get();
        return view('shop.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'label'         => 'required|string|max:50',
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'address_line_1'=> 'required|string|max:255',
            'city'          => 'required|string|max:100',
            'postal_code'   => 'required|string|max:20',
            'country'       => 'required|string|max:100',
        ]);

        Auth::user()->addresses()->create($request->validated());

        return back()->with('success', 'Address saved.');
    }
}
