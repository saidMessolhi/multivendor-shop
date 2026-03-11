<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('shop.name'))</title>
    <meta name="description" content="@yield('meta_description', 'Multivendor ecommerce platform')">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        [x-cloak] { display: none !important; }
        .cart-badge { @apply absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center; }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

{{-- Top Bar --}}
<div class="bg-blue-700 text-white text-xs py-1 px-4 text-center">
    Free shipping on orders over ${{ config('shop.free_shipping_threshold') }}
</div>

{{-- Navigation --}}
<nav class="bg-white shadow-sm sticky top-0 z-50" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-store text-white text-sm"></i>
                </div>
                <span class="text-xl font-bold text-blue-700">{{ config('shop.name') }}</span>
            </a>

            {{-- Search --}}
            <form action="{{ route('search') }}" method="GET" class="hidden md:flex flex-1 max-w-lg mx-8">
                <div class="relative w-full">
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="Search products, vendors..."
                        class="w-full pl-4 pr-12 py-2 border border-gray-300 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="absolute right-3 top-2 text-gray-400 hover:text-blue-600">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            {{-- Nav Links --}}
            <div class="hidden md:flex items-center space-x-4">
                <a href="{{ route('shop') }}" class="text-gray-600 hover:text-blue-600 text-sm font-medium">Shop</a>

                {{-- Cart --}}
                <a href="{{ route('cart.index') }}" class="relative text-gray-600 hover:text-blue-600">
                    <i class="fas fa-shopping-cart text-lg"></i>
                    <span id="cart-count" class="cart-badge">0</span>
                </a>

                {{-- User Menu --}}
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 text-sm text-gray-700 hover:text-blue-600">
                            <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full">
                            <span>{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border py-1 z-50">
                            @if(auth()->user()->isVendor())
                                <a href="{{ route('vendor.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-store w-4 mr-2 text-blue-500"></i> Vendor Dashboard
                                </a>
                            @endif
                            @if(auth()->user()->isAdmin())
                                <a href="/admin" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-cog w-4 mr-2 text-purple-500"></i> Admin Panel
                                </a>
                            @endif
                            <a href="{{ route('orders.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-box w-4 mr-2 text-green-500"></i> My Orders
                            </a>
                            <a href="{{ route('wishlist.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-heart w-4 mr-2 text-red-400"></i> Wishlist
                            </a>
                            <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-user w-4 mr-2 text-gray-400"></i> Profile
                            </a>
                            <hr class="my-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt w-4 mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-blue-600">Login</a>
                    <a href="{{ route('register') }}" class="text-sm bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700">Register</a>
                @endauth
            </div>

            {{-- Mobile toggle --}}
            <button @click="mobileOpen = !mobileOpen" class="md:hidden text-gray-600">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="mobileOpen" x-cloak class="md:hidden bg-white border-t px-4 py-3 space-y-2">
        <form action="{{ route('search') }}" method="GET">
            <input type="text" name="q" placeholder="Search..." class="w-full border rounded-full px-4 py-2 text-sm">
        </form>
        <a href="{{ route('shop') }}" class="block py-2 text-sm text-gray-700">Shop</a>
        <a href="{{ route('cart.index') }}" class="block py-2 text-sm text-gray-700">Cart</a>
        @auth
            <a href="{{ route('orders.index') }}" class="block py-2 text-sm text-gray-700">My Orders</a>
            <a href="{{ route('profile.show') }}" class="block py-2 text-sm text-gray-700">Profile</a>
        @else
            <a href="{{ route('login') }}" class="block py-2 text-sm text-gray-700">Login</a>
            <a href="{{ route('register') }}" class="block py-2 text-sm text-gray-700">Register</a>
        @endauth
    </div>
</nav>

{{-- Flash Messages --}}
@foreach(['success','error','info','warning'] as $type)
    @if(session($type))
        <div x-data="{ show: true }" x-show="show" x-cloak
            x-init="setTimeout(() => show = false, 4000)"
            class="fixed top-4 right-4 z-50 max-w-sm px-4 py-3 rounded-lg shadow-lg text-white text-sm
                {{ $type === 'success' ? 'bg-green-500' : ($type === 'error' ? 'bg-red-500' : ($type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500')) }}">
            <div class="flex items-center justify-between space-x-3">
                <span>{{ session($type) }}</span>
                <button @click="show = false" class="opacity-70 hover:opacity-100">✕</button>
            </div>
        </div>
    @endif
@endforeach

{{-- Main Content --}}
<main>
    @yield('content')
</main>

{{-- Footer --}}
<footer class="bg-gray-900 text-gray-300 mt-16">
    <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
            <div class="flex items-center space-x-2 mb-4">
                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-store text-white text-sm"></i>
                </div>
                <span class="text-white font-bold text-lg">{{ config('shop.name') }}</span>
            </div>
            <p class="text-sm leading-relaxed">Your trusted multivendor marketplace. Shop from hundreds of vendors.</p>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-4">Quick Links</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('home') }}" class="hover:text-white">Home</a></li>
                <li><a href="{{ route('shop') }}" class="hover:text-white">Shop</a></li>
                <li><a href="{{ route('vendor.apply') }}" class="hover:text-white">Become a Vendor</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-4">My Account</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('orders.index') }}" class="hover:text-white">Orders</a></li>
                <li><a href="{{ route('wishlist.index') }}" class="hover:text-white">Wishlist</a></li>
                <li><a href="{{ route('profile.show') }}" class="hover:text-white">Profile</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-4">Payments</h4>
            <div class="flex space-x-3 text-2xl">
                <i class="fab fa-cc-stripe text-purple-400" title="Stripe"></i>
                <i class="fab fa-cc-paypal text-blue-400" title="PayPal"></i>
                <i class="fas fa-money-bill-wave text-green-400" title="Cash on Delivery"></i>
            </div>
        </div>
    </div>
    <div class="border-t border-gray-800 text-center py-4 text-xs text-gray-500">
        © {{ date('Y') }} {{ config('shop.name') }}. All rights reserved.
    </div>
</footer>

{{-- Cart count update --}}
<script>
    async function updateCartCount() {
        const res = await fetch('/cart/count');
        const data = await res.json();
        document.getElementById('cart-count').textContent = data.count;
    }
    updateCartCount();
</script>

@stack('scripts')
</body>
</html>
