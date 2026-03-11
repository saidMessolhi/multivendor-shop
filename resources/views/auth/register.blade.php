@extends('layouts.auth')
@section('title', 'Create Account')

@section('form')
<div>
    {{-- Heading --}}
    <div class="slide-up mb-7">
        <h1 class="syne text-3xl font-extrabold text-slate-900 mb-2">Create your account</h1>
        <p class="text-slate-500 text-[15px]">Join thousands of vendors and shoppers</p>
    </div>

   <form method="POST" action="{{ route('register') }}">
         @csrf

        {{-- Role Selector --}}
        <div class="slide-up mb-6">
            <label class="block text-sm font-semibold text-slate-700 mb-3">I want to</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="relative cursor-pointer">
                    <input type="radio" name="role" value="customer" class="sr-only peer" checked>
                    <div class="flex items-center gap-3 px-4 py-3.5 border-2 rounded-xl transition-all
                        border-slate-200 peer-checked:border-orange-500 peer-checked:bg-orange-50">
                        <div class="w-9 h-9 rounded-lg bg-slate-100 peer-checked:bg-orange-100 flex items-center justify-center flex-shrink-0 transition-colors">
                            <svg class="w-5 h-5 text-slate-500 peer-checked:text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Shop</p>
                            <p class="text-xs text-slate-400">Buy products</p>
                        </div>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input type="radio" name="role" value="vendor" class="sr-only peer">
                    <div class="flex items-center gap-3 px-4 py-3.5 border-2 rounded-xl transition-all
                        border-slate-200 peer-checked:border-orange-500 peer-checked:bg-orange-50">
                        <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0 transition-colors">
                            <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Sell</p>
                            <p class="text-xs text-slate-400">Open a store</p>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        {{-- Name --}}
        <div class="slide-up slide-up-delay-1 mb-4">
            <label class="block text-sm font-semibold text-slate-700 mb-2" for="name">Full name</label>
            <input
                id="name"
                name="name"
                type="text"
                autocomplete="name"
                value="{{ old('name') }}"
                placeholder="Jane Smith"
                class="auth-input @error('name') error @enderror"
                required
            >
            @error('name')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="slide-up slide-up-delay-2 mb-4">
            <label class="block text-sm font-semibold text-slate-700 mb-2" for="email">Email address</label>
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
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="slide-up slide-up-delay-3 mb-4">
            <label class="block text-sm font-semibold text-slate-700 mb-2" for="password">Password</label>
            <div class="input-wrapper">
                <input
                    id="password"
                    name="password"
                    :type="showPw ? 'text' : 'password'"
                    autocomplete="new-password"
                    placeholder="Min. 8 characters"
                    @input="checkStrength($event.target.value)"
                    class="auth-input pr-12 @error('password') error @enderror"
                    required
                >
                <button type="button" class="toggle-pw" @click="showPw = !showPw" tabindex="-1">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>

            {{-- Strength meter --}}
            <div x-show="pwLength > 0" x-cloak class="mt-2 space-y-1.5">
                <div class="grid grid-cols-4 gap-1">
                    <template x-for="i in 4">
                        <div class="strength-bar">
                            <div class="strength-fill"
                                :style="{
                                    width: strength >= i ? '100%' : '0%',
                                    background: strength <= 1 ? '#EF4444' : strength === 2 ? '#F59E0B' : strength === 3 ? '#3B82F6' : '#10B981'
                                }">
                            </div>
                        </div>
                    </template>
                </div>
                <p class="text-xs" :class="{
                    'text-red-500': strength <= 1,
                    'text-yellow-600': strength === 2,
                    'text-blue-500': strength === 3,
                    'text-emerald-600': strength === 4
                }" x-text="strengthLabel"></p>
            </div>

            @error('password')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="slide-up slide-up-delay-4 mb-6">
            <label class="block text-sm font-semibold text-slate-700 mb-2" for="password_confirmation">Confirm password</label>
            <div class="input-wrapper">
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    :type="showPwConfirm ? 'text' : 'password'"
                    autocomplete="new-password"
                    placeholder="Repeat password"
                    class="auth-input pr-12"
                    required
                >
                <button type="button" class="toggle-pw" @click="showPwConfirm = !showPwConfirm" tabindex="-1">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Terms --}}
        <div class="slide-up mb-6">
            <label class="flex items-start gap-2.5 cursor-pointer">
                <input type="checkbox" name="terms" class="auth-checkbox mt-0.5" required>
                <span class="text-sm text-slate-600 leading-relaxed">
                    I agree to the
                    <a href="#" class="text-orange-500 hover:underline font-medium">Terms of Service</a>
                    and
                    <a href="#" class="text-orange-500 hover:underline font-medium">Privacy Policy</a>
                </span>
            </label>
        </div>

        {{-- Submit --}}
        <div class="slide-up">
            <button type="submit" class="btn-primary" :disabled="submitting" @click="submitting = true">
                <span x-show="!submitting">Create free account</span>
                <span x-show="submitting" x-cloak class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Creating account...
                </span>
            </button>
        </div>

        {{-- Login link --}}
        <div class="mt-6 text-center">
            <p class="text-sm text-slate-500">
                Already have an account?
                <a href="{{ route('login') }}" class="text-orange-500 font-semibold hover:text-orange-600 transition-colors">Sign in</a>
            </p>
        </div>

    </form>
</div>
@endsection

@section('panel-content')
<div>
    <h2 class="syne text-4xl font-extrabold text-white leading-tight mb-4">
        Start selling<br>
        <span class="text-orange-400">in minutes.</span>
    </h2>
    <p class="text-white/60 text-base leading-relaxed">
        Set up your store, list your products, and start receiving orders today. No upfront fees.
    </p>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 gap-4">
    @foreach([['num'=>'10K+','label'=>'Active vendors'],['num'=>'500K+','label'=>'Products listed'],['num'=>'2M+','label'=>'Orders processed'],['num'=>'99.9%','label'=>'Uptime SLA']] as $stat)
    <div class="bg-white/5 rounded-xl p-4 border border-white/10">
        <p class="syne text-2xl font-extrabold text-white mb-0.5">{{ $stat['num'] }}</p>
        <p class="text-white/50 text-xs">{{ $stat['label'] }}</p>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
function registerForm() {
    return {
        showPw: false,
        showPwConfirm: false,
        submitting: false,
        strength: 0,
        pwLength: 0,
        strengthLabel: '',
        checkStrength(pw) {
            this.pwLength = pw.length;
            let score = 0;
            if (pw.length >= 8)  score++;
            if (/[A-Z]/.test(pw)) score++;
            if (/[0-9]/.test(pw)) score++;
            if (/[^A-Za-z0-9]/.test(pw)) score++;
            this.strength = score;
            this.strengthLabel = ['','Weak — try adding numbers','Fair — add uppercase letters','Good — almost there!','Strong password ✓'][score];
        }
    }
}
</script>
@endpush
