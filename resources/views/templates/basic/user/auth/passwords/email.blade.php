@extends($activeTemplate.'layouts.auth')

@section('auth')
<div class="w-full space-y-8">
    <!-- Header -->
    <div class="text-center sm:text-left space-y-2">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-neutral-900">
            @lang('Account Recovery')
        </h1>
        <p class="text-sm text-neutral-500">
            @lang('Enter your email or username and we will send you instructions to reset your password.')
        </p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('user.password.email') }}" class="space-y-5 verify-gcaptcha">
        @csrf

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-700">
                @lang('Email or Username')
            </label>
            <input type="text" name="value" value="{{ old('value') }}" required autofocus
                class="w-full px-4 py-3.5 bg-neutral-50 hover:bg-neutral-100/70 focus:bg-white border border-neutral-200/90 focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 rounded-xl text-sm text-neutral-900 placeholder:text-neutral-400 transition-all outline-none"
                placeholder="name@example.com">
        </div>

        <x-captcha />

        <button type="submit" 
            class="w-full py-3.5 px-6 bg-neutral-900 hover:bg-black active:scale-[0.99] text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow transition-all duration-200 flex items-center justify-center gap-2">
            <span>@lang('Send Reset Instructions')</span>
            <i class="las la-paper-plane text-base"></i>
        </button>
    </form>

    <!-- Return to Login -->
    <div class="pt-6 border-t border-neutral-100 text-center">
        <p class="text-sm text-neutral-500">
            @lang('Remember your password?')
            <a href="{{ route('user.login') }}" class="text-neutral-900 hover:underline font-semibold ml-1">
                @lang('Sign in')
            </a>
        </p>
    </div>
</div>
@endsection
