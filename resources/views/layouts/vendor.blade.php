<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vendor Dashboard') — {{ config('shop.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>[x-cloak]{display:none!important}</style>
    @stack('styles')
</head>
<body class="bg-gray-100">

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'w-64' : 'w-16'"
        class="bg-gray-900 text-white flex flex-col transition-all duration-300 flex-shrink-0">

        {{-- Logo --}}
        <div class="flex items-center justify-between px-4 py-4 border-b border-gray-700">
            <a href="{{ route('home') }}" x-show="sidebarOpen" class="text-lg font-bold text-blue-400">
                {{ auth()->user()->vendor->store_name ?? 'My Store' }}
            </a>
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 py-4 space-y-1 overflow-y-auto">
            @php
                $navItems = [
                    ['route' => 'vendor.dashboard',       'icon' => 'fas fa-tachometer-alt', 'label' => 'Dashboard'],
                    ['route' => 'vendor.products.index',  'icon' => 'fas fa-box',             'label' => 'Products'],
                    ['route' => 'vendor.orders.index',    'icon' => 'fas fa-shopping-bag',    'label' => 'Orders'],
                    ['route' => 'vendor.reviews.index',   'icon' => 'fas fa-star',            'label' => 'Reviews'],
                    ['route' => 'vendor.payouts.index',   'icon' => 'fas fa-wallet',          'label' => 'Payouts'],
                    ['route' => 'vendor.settings',        'icon' => 'fas fa-cog',             'label' => 'Settings'],
                ];
            @endphp

            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}"
                    class="flex items-center px-4 py-3 text-sm transition-colors
                    {{ request()->routeIs($item['route']) ? 'bg-blue-700 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <i class="{{ $item['icon'] }} w-5 text-center flex-shrink-0"></i>
                    <span x-show="sidebarOpen" class="ml-3">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- Bottom --}}
        <div class="border-t border-gray-700 p-4">
            <a href="{{ route('home') }}" class="flex items-center text-sm text-gray-400 hover:text-white">
                <i class="fas fa-store w-5 text-center"></i>
                <span x-show="sidebarOpen" class="ml-3">View Shop</span>
            </a>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top bar --}}
        <header class="bg-white border-b px-6 py-4 flex items-center justify-between flex-shrink-0">
            <h1 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-500">
                    Balance: <strong class="text-green-600">${{ number_format(auth()->user()->vendor->balance, 2) }}</strong>
                </span>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 text-sm text-gray-700">
                        <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full">
                        <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak
                        class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg border py-1 z-50">
                        <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash --}}
        @foreach(['success','error','info'] as $type)
            @if(session($type))
                <div class="mx-6 mt-4 px-4 py-3 rounded text-sm
                    {{ $type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : ($type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-blue-100 text-blue-800 border border-blue-200') }}">
                    {{ session($type) }}
                </div>
            @endif
        @endforeach

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
