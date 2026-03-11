<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureVendorIsApproved
{
    public function handle(Request $request, Closure $next)
    {
        $vendor = $request->user()?->vendor;

        if (! $vendor) {
            return redirect()->route('vendor.apply')->with('info', 'Please complete your vendor registration.');
        }

        if ($vendor->isPending()) {
            return redirect()->route('home')->with('info', 'Your vendor application is under review.');
        }

        if ($vendor->isBanned()) {
            return redirect()->route('home')->with('error', 'Your vendor account has been suspended.');
        }

        return $next($request);
    }
}
