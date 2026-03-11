@extends('layouts.auth')
@section('title', 'Reset Password')

@section('form')
<div>
    <div class="slide-up mb-8">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 mb-6 group transition-colors">
            <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to sign in
        </a>
        <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center mb-6">
            <svg class="w-7 h-7 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
        </div>
        <h1 class="syne text-3xl font-extrabold text-slate-900 mb-2">Forgot your password?</h1>
        <p class="text-slate-500 text-[15px] leading-relaxed">
            No worries. Enter your email and we'll send you a reset link within a few minutes.
        </p>
    </div>

    <form method="POST" action="{{ route('password.email') }}" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        <div class="slide-up slide-up-delay-1 mb-6">
            <label class="block text-sm font-semibold text-slate-700 mb-2" for="email">Email address</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                placeholder="you@example.com"
                class="auth-input @error('email') error @enderror"
                autofocus
                required
            >
            @error('email')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="slide-up slide-up-delay-2">
            <button type="submit" class="btn-primary" :disabled="submitting">
                <span x-show="!submitting">Send reset link</span>
                <span x-show="submitting" x-cloak class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Sending...
                </span>
            </button>
        </div>
    </form>
</div>
@endsection

@section('panel-content')
<div>
    <h2 class="syne text-4xl font-extrabold text-white leading-tight mb-4">
        Secure by<br>
        <span class="text-orange-400">design.</span>
    </h2>
    <p class="text-white/60 text-base leading-relaxed">
        Your account security is our top priority. Reset links expire in 60 minutes and can only be used once.
    </p>
</div>
<div class="bg-white/5 rounded-2xl p-6 border border-white/10 space-y-4">
    @foreach(['Check your inbox (and spam folder)','Click the secure reset link','Choose a strong new password','Link expires in 60 minutes'] as $i => $step)
    <div class="flex items-center gap-4">
        <div class="w-8 h-8 rounded-full bg-orange-500/20 text-orange-400 flex items-center justify-center text-xs font-bold syne flex-shrink-0">
            {{ $i + 1 }}
        </div>
        <p class="text-white/70 text-sm">{{ $step }}</p>
    </div>
    @endforeach
</div>
@endsection
