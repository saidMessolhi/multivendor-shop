@extends('layouts.auth')
@section('title', 'Set New Password')

@section('form')
<div>
    <div class="slide-up mb-8">
        <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center mb-6">
            <svg class="w-7 h-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <h1 class="syne text-3xl font-extrabold text-slate-900 mb-2">Set new password</h1>
        <p class="text-slate-500 text-[15px]">Choose a strong password for your account.</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}"
        x-data="resetForm()" @submit="submitting = true" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="slide-up slide-up-delay-1 mb-4">
            <label class="block text-sm font-semibold text-slate-700 mb-2" for="email">Email address</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', request('email')) }}"
                placeholder="you@example.com"
                class="auth-input @error('email') error @enderror"
                required
            >
            @error('email')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="slide-up slide-up-delay-2 mb-4">
            <label class="block text-sm font-semibold text-slate-700 mb-2" for="password">New password</label>
            <div class="input-wrapper">
                <input
                    id="password"
                    name="password"
                    :type="showPw ? 'text' : 'password'"
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
            <div x-show="pwLength > 0" x-cloak class="mt-2 space-y-1.5">
                <div class="grid grid-cols-4 gap-1">
                    <template x-for="i in 4">
                        <div class="strength-bar">
                            <div class="strength-fill"
                                :style="{width: strength >= i ? '100%' : '0%', background: strength <= 1 ? '#EF4444' : strength === 2 ? '#F59E0B' : strength === 3 ? '#3B82F6' : '#10B981'}">
                            </div>
                        </div>
                    </template>
                </div>
                <p class="text-xs" :class="{'text-red-500': strength<=1,'text-yellow-600': strength===2,'text-blue-500': strength===3,'text-emerald-600': strength===4}" x-text="strengthLabel"></p>
            </div>
            @error('password')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="slide-up slide-up-delay-3 mb-7">
            <label class="block text-sm font-semibold text-slate-700 mb-2" for="password_confirmation">Confirm new password</label>
            <div class="input-wrapper">
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    :type="showPwConfirm ? 'text' : 'password'"
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

        <div class="slide-up slide-up-delay-4">
            <button type="submit" class="btn-primary" :disabled="submitting">
                <span x-show="!submitting">Reset password</span>
                <span x-show="submitting" x-cloak class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Resetting...
                </span>
            </button>
        </div>
    </form>
</div>
@endsection

@section('panel-content')
<div>
    <h2 class="syne text-4xl font-extrabold text-white leading-tight mb-4">
        Almost<br>
        <span class="text-orange-400">there.</span>
    </h2>
    <p class="text-white/60 text-base leading-relaxed">
        Choose a password that's at least 8 characters with a mix of letters, numbers, and symbols.
    </p>
</div>
<div class="space-y-3">
    @foreach(['At least 8 characters long','Contains uppercase & lowercase letters','Includes numbers and symbols','Different from your previous password'] as $tip)
    <div class="flex items-center gap-3">
        <div class="w-5 h-5 rounded-full bg-orange-500/20 flex items-center justify-center flex-shrink-0">
            <svg class="w-3 h-3 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
        </div>
        <p class="text-white/60 text-sm">{{ $tip }}</p>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
function resetForm() {
    return {
        showPw: false, showPwConfirm: false, submitting: false,
        strength: 0, pwLength: 0, strengthLabel: '',
        checkStrength(pw) {
            this.pwLength = pw.length;
            let s = 0;
            if (pw.length >= 8) s++;
            if (/[A-Z]/.test(pw)) s++;
            if (/[0-9]/.test(pw)) s++;
            if (/[^A-Za-z0-9]/.test(pw)) s++;
            this.strength = s;
            this.strengthLabel = ['','Weak','Fair','Good','Strong ✓'][s];
        }
    }
}
</script>
@endpush
