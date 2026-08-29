@extends($activeTemplate . 'layouts.auth')

@section('auth')
<div class="w-full space-y-8">
    <!-- Header -->
    <div class="text-center sm:text-left space-y-2">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-neutral-900">
            @lang('Welcome back')
        </h1>
        <p class="text-sm text-neutral-500">
            @lang('Sign in to manage your servers, domains, and cloud infrastructure.')
        </p>
    </div>

    @include($activeTemplate.'partials.social_login')

    <!-- Form -->
    <form action="{{ route('user.login') }}" method="POST" class="space-y-5 verify-gcaptcha">
        @csrf

        <!-- Username or Email -->
        <div class="space-y-1.5">
            <label for="username" class="block text-xs font-semibold uppercase tracking-wider text-neutral-700">
                @lang('Username or Email')
            </label>
            <div class="relative">
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                    class="w-full px-4 py-3.5 bg-neutral-50 hover:bg-neutral-100/70 focus:bg-white border border-neutral-200/90 focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 rounded-xl text-sm text-neutral-900 placeholder:text-neutral-400 transition-all outline-none"
                    placeholder="name@example.com">
            </div>
        </div>

        <!-- Password -->
        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-neutral-700">
                    @lang('Password')
                </label>
                <a href="{{ route('user.password.request') }}" class="text-xs font-medium text-neutral-500 hover:text-neutral-900 transition-colors">
                    @lang('Forgot password?')
                </a>
            </div>
            <div class="relative">
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-3.5 bg-neutral-50 hover:bg-neutral-100/70 focus:bg-white border border-neutral-200/90 focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 rounded-xl text-sm text-neutral-900 placeholder:text-neutral-400 transition-all outline-none"
                    placeholder="••••••••">
            </div>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-neutral-300 text-neutral-900 focus:ring-neutral-900 focus:ring-offset-0">
                <span class="text-xs text-neutral-600 font-medium">@lang('Keep me signed in')</span>
            </label>
        </div>

        <x-captcha />

        <!-- Submit Button -->
        <button type="submit" 
            class="w-full py-3.5 px-6 bg-neutral-900 hover:bg-black active:scale-[0.99] text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow transition-all duration-200 flex items-center justify-center gap-2">
            <span>@lang('Sign In')</span>
            <i class="las la-arrow-right text-base"></i>
        </button>
    </form>

    <!-- Sign Up Redirect -->
    <div class="pt-6 border-t border-neutral-100 text-center">
        <p class="text-sm text-neutral-500">
            @lang("Don't have an account?")
            <a href="{{ route('user.register') }}" class="text-neutral-900 hover:underline font-semibold ml-1">
                @lang('Create an account')
            </a>
        </p>
    </div>
</div>
@endsection
