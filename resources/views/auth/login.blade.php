@extends('layouts.auth')
@section('title', 'Sign In')

@section('form')
<div>
    {{-- Heading --}}
    <div class="slide-up mb-8">
        <h1 class="syne text-3xl font-extrabold text-slate-900 mb-2">Welcome back</h1>
        <p class="text-slate-500 text-[15px]">Sign in to your account to continue</p>
    </div>

    {{-- Login Form --}}
    <form method="POST" action="{{ route('login') }}" x-data="loginForm()" @submit="submitting = true" novalidate>
        @csrf

        {{-- Email --}}
        <div class="slide-up slide-up-delay-1 mb-5">
            <label class="block text-sm font-semibold text-slate-700 mb-2" for="email">
                Email address
            </label>
            <input
                id="email"
                name="email"
                type="email"
                autocomplete="email"
                value="{{ old('email') }}"
                placeholder="you@example.com"
                class="auth-input @error('email') error @enderror"
                required
            >
            @error('email')
                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="slide-up slide-up-delay-2 mb-5">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-semibold text-slate-700" for="password">Password</label>
                <a href="{{ route('password.request') }}" class="text-xs text-orange-500 hover:text-orange-600 font-medium transition-colors">
                    Forgot password?
                </a>
            </div>
            <div class="input-wrapper">
                <input
                    id="password"
                    name="password"
                    :type="showPw ? 'text' : 'password'"
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="auth-input pr-12 @error('password') error @enderror"
                    required
                >
                <button type="button" class="toggle-pw" @click="showPw = !showPw" tabindex="-1">
                    <svg x-show="!showPw" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="!showPw" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Remember me --}}
        <div class="slide-up slide-up-delay-3 flex items-center justify-between mb-7">
            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                <input type="checkbox" name="remember" class="auth-checkbox">
                <span class="text-sm text-slate-600">Remember me for 30 days</span>
            </label>
        </div>

        {{-- Submit --}}
        <div class="slide-up slide-up-delay-4">
            <button type="submit" class="btn-primary" :disabled="submitting">
                <span x-show="!submitting">Sign in to account</span>
                <span x-show="submitting" x-cloak class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Signing in...
                </span>
            </button>
        </div>

        {{-- Register link --}}
        <div class="slide-up slide-up-delay-5 mt-6 text-center">
            <p class="text-sm text-slate-500">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-orange-500 font-semibold hover:text-orange-600 transition-colors">
                    Create one free
                </a>
            </p>
        </div>

        {{-- Divider --}}
        <div class="slide-up mt-8 mb-6">
            <div class="divider">or continue with</div>
        </div>

        {{-- Demo accounts --}}
        <div class="slide-up grid grid-cols-3 gap-3">
            @foreach([['label'=>'Admin','email'=>'admin@shop.com'],['label'=>'Vendor','email'=>'vendor@shop.com'],['label'=>'Customer','email'=>'customer@shop.com']] as $demo)
            <button type="button"
                @click="fillDemo('{{ $demo['email'] }}')"
                class="btn-social text-xs py-2.5">
                <span class="font-semibold">{{ $demo['label'] }}</span>
            </button>
            @endforeach
        </div>
        <p class="text-center text-xs text-slate-400 mt-2">Demo accounts (password: <code class="bg-slate-100 px-1 rounded">password</code>)</p>

    </form>
</div>
@endsection

@section('panel-content')
<div>
    <h2 class="syne text-4xl font-extrabold text-white leading-tight mb-4">
        Your marketplace,<br>
        <span class="text-orange-400">your rules.</span>
    </h2>
    <p class="text-white/60 text-base leading-relaxed">
        Sell across multiple vendors, track every order, and grow your business with powerful analytics — all in one place.
    </p>
</div>

{{-- Feature list --}}
<div class="space-y-4">
    @foreach([
        ['icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'text'=>'Multi-vendor marketplace ready'],
        ['icon'=>'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'text'=>'Stripe, PayPal & COD payments'],
        ['icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'text'=>'Real-time sales dashboard'],
    ] as $feat)
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $feat['icon'] }}"/>
            </svg>
        </div>
        <span class="text-white/75 text-sm">{{ $feat['text'] }}</span>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
function loginForm() {
    return {
        showPw: false,
        submitting: false,
        fillDemo(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
        }
    }
}
</script>
@endpush
