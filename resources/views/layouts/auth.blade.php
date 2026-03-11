<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — {{ config('shop.name', 'MultiVendor') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        * { box-sizing: border-box; }
        [x-cloak] { display: none !important; }

        :root {
            --brand: #0F172A;
            --accent: #F97316;
            --accent2: #3B82F6;
            --surface: #F8FAFC;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--surface);
            min-height: 100vh;
        }

        .syne { font-family: 'Syne', sans-serif; }

        /* Animated background panel */
        .auth-panel {
            background: var(--brand);
            position: relative;
            overflow: hidden;
        }

        .grid-lines {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            animation: drift 8s ease-in-out infinite;
        }
        .orb-1 { width: 320px; height: 320px; background: rgba(249,115,22,0.25); top: -60px; right: -80px; animation-delay: 0s; }
        .orb-2 { width: 240px; height: 240px; background: rgba(59,130,246,0.2); bottom: 80px; left: -60px; animation-delay: 3s; }
        .orb-3 { width: 160px; height: 160px; background: rgba(249,115,22,0.15); bottom: 200px; right: 60px; animation-delay: 5s; }

        @keyframes drift {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(20px, -20px) scale(1.05); }
            66% { transform: translate(-15px, 15px) scale(0.95); }
        }

        /* Floating shapes */
        .shape {
            position: absolute;
            border: 1px solid rgba(255,255,255,0.08);
            animation: spin-slow linear infinite;
        }
        .shape-1 { width: 120px; height: 120px; border-radius: 24px; top: 15%; left: 10%; animation-duration: 20s; transform: rotate(15deg); }
        .shape-2 { width: 80px; height: 80px; border-radius: 50%; top: 60%; right: 15%; animation-duration: 15s; border-color: rgba(249,115,22,0.2); }
        .shape-3 { width: 60px; height: 60px; top: 40%; left: 40%; animation-duration: 25s; transform: rotate(45deg); }

        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Form inputs */
        .auth-input {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #E2E8F0;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            color: #0F172A;
            background: white;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .auth-input:focus {
            border-color: #F97316;
            box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
        }
        .auth-input::placeholder { color: #94A3B8; }

        .auth-input.error {
            border-color: #EF4444;
            box-shadow: 0 0 0 3px rgba(239,68,68,0.08);
        }

        /* Primary button */
        .btn-primary {
            width: 100%;
            padding: 13px 24px;
            background: #F97316;
            color: white;
            font-family: 'Syne', sans-serif;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.03em;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s, background 0.15s;
            position: relative;
            overflow: hidden;
        }
        .btn-primary::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
        }
        .btn-primary:hover {
            background: #EA6F0D;
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(249,115,22,0.35);
        }
        .btn-primary:active { transform: translateY(0); }
        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #94A3B8;
            font-size: 13px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #E2E8F0;
        }

        /* Slide-up animation on load */
        .slide-up {
            animation: slideUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .slide-up-delay-1 { animation-delay: 0.05s; }
        .slide-up-delay-2 { animation-delay: 0.1s; }
        .slide-up-delay-3 { animation-delay: 0.15s; }
        .slide-up-delay-4 { animation-delay: 0.2s; }
        .slide-up-delay-5 { animation-delay: 0.25s; }

        /* Password toggle */
        .input-wrapper { position: relative; }
        .input-wrapper .toggle-pw {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
        }
        .input-wrapper .toggle-pw:hover { color: #64748B; }

        /* Checkbox */
        .auth-checkbox {
            width: 18px;
            height: 18px;
            border: 1.5px solid #CBD5E1;
            border-radius: 5px;
            cursor: pointer;
            accent-color: #F97316;
        }

        /* Social button */
        .btn-social {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 11px 16px;
            background: white;
            border: 1.5px solid #E2E8F0;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: #334155;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .btn-social:hover { border-color: #CBD5E1; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }

        /* Strength meter */
        .strength-bar {
            height: 4px;
            border-radius: 2px;
            background: #E2E8F0;
            overflow: hidden;
            transition: all 0.3s;
        }
        .strength-fill {
            height: 100%;
            border-radius: 2px;
            transition: width 0.4s, background 0.4s;
        }
    </style>
</head>
<body>

<div class="min-h-screen flex">

    {{-- Left: Auth Form --}}
    <div class="flex-1 flex flex-col justify-center px-6 py-12 lg:px-16 xl:px-24 max-w-2xl mx-auto w-full lg:mx-0">

        {{-- Logo --}}
        <div class="slide-up mb-10">
            <a href="{{ route('home') }}" class="inline-flex items-center space-x-3 group">
                <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="syne text-xl font-800 text-slate-900 font-extrabold">{{ config('shop.name', 'MultiVendor') }}</span>
            </a>
        </div>

        {{-- Flash errors --}}
        @if($errors->any())
        <div class="slide-up mb-6 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    @foreach($errors->all() as $error)
                        <p class="text-sm text-red-700">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if(session('status'))
        <div class="slide-up mb-6 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
            <p class="text-sm text-green-700">{{ session('status') }}</p>
        </div>
        @endif

        @yield('form')

    </div>

    {{-- Right: Decorative Panel (hidden on mobile) --}}
    <div class="hidden lg:flex lg:w-[45%] xl:w-[42%] auth-panel flex-col justify-between p-12 flex-shrink-0">
        <div class="grid-lines"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>

        {{-- Panel content --}}
        <div class="relative z-10">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold tracking-widest uppercase"
                style="background:rgba(249,115,22,0.2);color:#FB923C;">
                Multivendor Platform
            </span>
        </div>

        <div class="relative z-10 space-y-8">
            @yield('panel-content')
        </div>

        <div class="relative z-10 flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                <svg class="w-4 h-4 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            </div>
            <div>
                <p class="text-white/80 text-sm font-medium">Trusted by 10,000+ vendors</p>
                <p class="text-white/40 text-xs">Across 50+ countries</p>
            </div>
        </div>
    </div>

</div>

@stack('scripts')
</body>
</html>
