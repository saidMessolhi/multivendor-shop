<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::active()->featured()->with('vendor')->inStock()->take(8)->get();
        $categories       = Category::where('is_active', true)->whereNull('parent_id')->take(8)->get();
        $topVendors       = Vendor::approved()->withCount('products')->orderByDesc('products_count')->take(6)->get();
        $newArrivals      = Product::active()->with('vendor')->latest()->take(8)->get();

        return view('shop.home', compact('featuredProducts', 'categories', 'topVendors', 'newArrivals'));
    }

    public function vendorStore(Vendor $vendor)
    {
        abort_unless($vendor->isApproved(), 404);

        $products = Product::where('vendor_id', $vendor->id)
            ->active()
            ->with('category')
            ->paginate(12);

        return view('shop.vendor-store', compact('vendor', 'products'));
    }
}
