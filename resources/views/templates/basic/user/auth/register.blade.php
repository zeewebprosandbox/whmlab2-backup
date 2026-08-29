@extends($activeTemplate . 'layouts.auth')

@php
    $policyPages = getContent('policy_pages.element', false, orderById: true);
@endphp

@section('auth')
<div class="w-full space-y-8">
    <!-- Header -->
    <div class="text-center sm:text-left space-y-2">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-neutral-900">
            @lang('Create your account')
        </h1>
        <p class="text-sm text-neutral-500">
            @lang('Deploy servers, manage domains, and scale your cloud infrastructure.')
        </p>
    </div>

    @include($activeTemplate . 'partials.social_login')

    <!-- Form -->
    <form action="{{ route('user.register') }}" method="POST" class="space-y-5 verify-gcaptcha @if (!gs('registration')) opacity-50 pointer-events-none @endif">
        @csrf

        @if (!gs('registration'))
            <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 font-medium text-center">
                @lang('Registration is currently disabled')
            </div>
        @endif

        <!-- Full Name -->
        <div class="space-y-1.5">
            <label for="full_name" class="block text-xs font-semibold uppercase tracking-wider text-neutral-700">
                @lang('Full Name')
            </label>
            <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required autofocus
                class="w-full px-4 py-3.5 bg-neutral-50 hover:bg-neutral-100/70 focus:bg-white border border-neutral-200/90 focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 rounded-xl text-sm text-neutral-900 placeholder:text-neutral-400 transition-all outline-none"
                placeholder="John Doe">
        </div>

        <!-- Email -->
        <div class="space-y-1.5">
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-neutral-700">
                @lang('Email Address')
            </label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                class="w-full px-4 py-3.5 bg-neutral-50 hover:bg-neutral-100/70 focus:bg-white border border-neutral-200/90 focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 rounded-xl text-sm text-neutral-900 placeholder:text-neutral-400 transition-all outline-none checkUser"
                placeholder="john@example.com">
        </div>

        <!-- Phone Number -->
        <div class="space-y-1.5">
            <label for="mobile" class="block text-xs font-semibold uppercase tracking-wider text-neutral-700">
                @lang('Phone Number')
            </label>
            <input type="tel" id="mobile" name="mobile" value="{{ old('mobile') }}" required
                class="w-full px-4 py-3.5 bg-neutral-50 hover:bg-neutral-100/70 focus:bg-white border border-neutral-200/90 focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 rounded-xl text-sm text-neutral-900 placeholder:text-neutral-400 transition-all outline-none"
                placeholder="+1 (555) 000-0000">
        </div>

        <!-- Password -->
        <div class="space-y-1.5">
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-neutral-700">
                @lang('Password')
            </label>
            <input type="password" id="password" name="password" required
                class="w-full px-4 py-3.5 bg-neutral-50 hover:bg-neutral-100/70 focus:bg-white border border-neutral-200/90 focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 rounded-xl text-sm text-neutral-900 placeholder:text-neutral-400 transition-all outline-none @if (gs('secure_password')) secure-password @endif"
                placeholder="••••••••">
        </div>

        <x-captcha />

        <!-- Agree to Terms -->
        @if (gs('agree'))
            <div class="pt-1">
                <label class="flex items-start gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" id="agree" name="agree" @checked(old('agree')) required
                        class="w-4 h-4 mt-0.5 rounded border-neutral-300 text-neutral-900 focus:ring-neutral-900 focus:ring-offset-0">
                    <span class="text-xs text-neutral-600">
                        @lang('I agree to the')
                        @foreach ($policyPages as $policy)
                            <a href="{{ route('policy.pages', $policy->slug) }}" target="_blank" class="text-neutral-900 hover:underline font-semibold">
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
            class="w-full py-3.5 px-6 bg-neutral-900 hover:bg-black active:scale-[0.99] text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow transition-all duration-200 flex items-center justify-center gap-2">
            <span>@lang('Create Account')</span>
            <i class="las la-user-plus text-base"></i>
        </button>
    </form>

    <!-- Sign In Link -->
    <div class="pt-6 border-t border-neutral-100 text-center">
        <p class="text-sm text-neutral-500">
            @lang('Already have an account?')
            <a href="{{ route('user.login') }}" class="text-neutral-900 hover:underline font-semibold ml-1">
                @lang('Sign in')
            </a>
        </p>
    </div>
</div>
@endsection
