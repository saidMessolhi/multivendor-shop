@extends('layouts.auth')
@section('title', 'Verify Email')

@section('form')
<div>
    <div class="slide-up mb-8 text-center">
        {{-- Animated envelope --}}
        <div class="relative inline-block mb-6">
            <div class="w-20 h-20 bg-blue-50 rounded-3xl flex items-center justify-center mx-auto">
                <svg class="w-10 h-10 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="absolute -top-1 -right-1 w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center animate-bounce">
                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                </svg>
            </div>
        </div>

        <h1 class="syne text-3xl font-extrabold text-slate-900 mb-3">Check your inbox</h1>
        <p class="text-slate-500 text-[15px] leading-relaxed max-w-sm mx-auto">
            We sent a verification link to <br>
            <strong class="text-slate-700">{{ auth()->user()->email }}</strong>
        </p>
    </div>

    {{-- Resend form --}}
    @if (session('status') == 'verification-link-sent')
    <div class="slide-up mb-6 bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-center">
        <p class="text-sm text-green-700 font-medium">✓ A new verification link has been sent!</p>
    </div>
    @endif

    <div class="slide-up slide-up-delay-1 space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primary">
                Resend verification email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-social w-full">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Sign out
            </button>
        </form>
    </div>

    <div class="slide-up slide-up-delay-2 mt-8 p-4 bg-slate-50 rounded-xl border border-slate-200">
        <p class="text-xs text-slate-500 text-center leading-relaxed">
            Didn't receive the email? Check your spam folder, or make sure
            <strong class="text-slate-700">{{ auth()->user()->email }}</strong> is correct.
        </p>
    </div>
</div>
@endsection

@section('panel-content')
<div>
    <h2 class="syne text-4xl font-extrabold text-white leading-tight mb-4">
        One last<br>
        <span class="text-orange-400">step.</span>
    </h2>
    <p class="text-white/60 text-base leading-relaxed">
        Verifying your email keeps your account secure and ensures you receive important notifications.
    </p>
</div>
<div class="bg-white/5 rounded-2xl p-5 border border-white/10">
    <p class="text-white/50 text-xs mb-3 uppercase tracking-widest font-semibold">Why verify?</p>
    <div class="space-y-3">
        @foreach(['Receive order confirmations','Reset your password securely','Get vendor payout notifications','Stay updated on new features'] as $reason)
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-1.5 rounded-full bg-orange-400 flex-shrink-0"></div>
            <p class="text-white/60 text-sm">{{ $reason }}</p>
        </div>
        @endforeach
    </div>
</div>
@endsection
