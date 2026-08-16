@extends($activeTemplate . 'layouts.auth')

@php
    $policyPages = getContent('policy_pages.element', false, orderById: true);
@endphp

@section('auth')
<div class="space-y-6">
    <div class="space-y-2">
        <h2 class="text-2xl font-bold tracking-tight text-white">@lang('Create your account')</h2>
        <p class="text-sm text-neutral-400">@lang('Deploy servers and manage your hosting environment.')</p>
    </div>

    @include($activeTemplate . 'partials.social_login')

    <form action="{{ route('user.register') }}" method="POST" class="space-y-4 verify-gcaptcha @if (!gs('registration')) opacity-50 pointer-events-none @endif">
        @csrf

        @if (!gs('registration'))
            <div class="p-3 bg-rose-500/10 border border-rose-500/20 rounded-lg text-xs text-rose-400 font-medium text-center">
                @lang('Registration is currently disabled')
            </div>
        @endif

        <!-- Full Name -->
        <div class="space-y-1">
            <label for="full_name" class="block text-xs font-medium text-neutral-300">@lang('Full Name')</label>
            <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required
                class="w-full px-4 py-2.5 bg-[#1C1C1F] border border-white/10 rounded-lg text-sm text-white placeholder-neutral-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                placeholder="John Doe">
        </div>

        <!-- Email -->
        <div class="space-y-1">
            <label for="email" class="block text-xs font-medium text-neutral-300">@lang('Email Address')</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                class="w-full px-4 py-2.5 bg-[#1C1C1F] border border-white/10 rounded-lg text-sm text-white placeholder-neutral-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all checkUser"
                placeholder="john@example.com">
        </div>

        <!-- Phone Number -->
        <div class="space-y-1">
            <label for="mobile" class="block text-xs font-medium text-neutral-300">@lang('Phone Number')</label>
            <input type="tel" id="mobile" name="mobile" value="{{ old('mobile') }}" required
                class="w-full px-4 py-2.5 bg-[#1C1C1F] border border-white/10 rounded-lg text-sm text-white placeholder-neutral-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                placeholder="+1 (555) 000-0000">
        </div>

        <!-- Password -->
        <div class="space-y-1">
            <label for="password" class="block text-xs font-medium text-neutral-300">@lang('Password')</label>
            <input type="password" id="password" name="password" required
                class="w-full px-4 py-2.5 bg-[#1C1C1F] border border-white/10 rounded-lg text-sm text-white placeholder-neutral-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all @if (gs('secure_password')) secure-password @endif"
                placeholder="••••••••">
        </div>

        <x-captcha />

        <!-- Agree to Terms -->
        @if (gs('agree'))
            <div class="pt-2">
                <label class="flex items-start gap-2 cursor-pointer">
                    <input type="checkbox" id="agree" name="agree" @checked(old('agree')) required
                        class="w-4 h-4 mt-0.5 rounded bg-[#1C1C1F] border-white/10 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0">
                    <span class="text-xs text-neutral-400">
                        @lang('I agree to the')
                        @foreach ($policyPages as $policy)
                            <a href="{{ route('policy.pages', $policy->slug) }}" target="_blank" class="text-indigo-400 hover:underline font-medium">
                                {{ __($policy->data_values->title) }}
                            </a>
                            @if (!$loop->last), @endif
                        @endforeach
                    </span>
                </label>
            </div>
        @endif

        <!-- Submit Button -->
        <button type="submit" 
            class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-500 active:scale-[0.98] text-white text-sm font-semibold rounded-lg shadow-glow-accent transition-all duration-200 flex items-center justify-center gap-2">
            <span>@lang('Create Account')</span>
            <i data-lucide="user-plus" class="w-4 h-4"></i>
        </button>
    </form>

    <!-- Sign In Link -->
    <div class="pt-4 border-t border-white/5 text-center">
        <p class="text-xs text-neutral-400">
            @lang('Already have an account?')
            <a href="{{ route('user.login') }}" class="text-indigo-400 hover:text-indigo-300 font-semibold ml-1">
                @lang('Sign in')
            </a>
        </p>
    </div>
</div>
@endsection
