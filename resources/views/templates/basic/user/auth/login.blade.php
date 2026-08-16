@extends($activeTemplate . 'layouts.auth')

@section('auth')
<div class="space-y-6">
    <div class="space-y-2">
        <h2 class="text-2xl font-bold tracking-tight text-white">@lang('Welcome back')</h2>
        <p class="text-sm text-neutral-400">@lang('Sign in to access your servers and billing dashboard.')</p>
    </div>

    @include($activeTemplate.'partials.social_login')

    <form action="{{ route('user.login') }}" method="POST" class="space-y-4 verify-gcaptcha">
        @csrf

        <!-- Username or Email Input -->
        <div class="space-y-1">
            <label for="username" class="block text-xs font-medium text-neutral-300">@lang('Username or Email')</label>
            <div class="relative">
                <input type="text" id="username" name="username" value="{{ old('username') }}" required
                    class="w-full px-4 py-2.5 bg-[#1C1C1F] border border-white/10 rounded-lg text-sm text-white placeholder-neutral-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                    placeholder="name@example.com">
            </div>
        </div>

        <!-- Password Input -->
        <div class="space-y-1">
            <div class="flex items-center justify-between">
                <label for="password" class="block text-xs font-medium text-neutral-300">@lang('Password')</label>
                <a href="{{ route('user.password.request') }}" class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors font-medium">
                    @lang('Forgot password?')
                </a>
            </div>
            <div class="relative">
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2.5 bg-[#1C1C1F] border border-white/10 rounded-lg text-sm text-white placeholder-neutral-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                    placeholder="••••••••">
            </div>
        </div>

        <!-- Remember Me & Magic Link -->
        <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                    class="w-4 h-4 rounded bg-[#1C1C1F] border-white/10 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0">
                <span class="text-xs text-neutral-400">@lang('Remember this device')</span>
            </label>

            <button type="button" onclick="alert('Magic link email sent to your inbox!')" class="text-xs text-cyan-400 hover:underline inline-flex items-center gap-1 font-medium">
                <i data-lucide="sparkles" class="w-3 h-3"></i>
                @lang('Magic Link')
            </button>
        </div>

        <x-captcha />

        <!-- Submit Button -->
        <button type="submit" 
            class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-500 active:scale-[0.98] text-white text-sm font-semibold rounded-lg shadow-glow-accent transition-all duration-200 flex items-center justify-center gap-2">
            <span>@lang('Sign In to Dashboard')</span>
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </button>
    </form>

    <!-- Sign Up Redirect -->
    <div class="pt-4 border-t border-white/5 text-center">
        <p class="text-xs text-neutral-400">
            @lang("Don't have an account yet?")
            <a href="{{ route('user.register') }}" class="text-indigo-400 hover:text-indigo-300 font-semibold ml-1">
                @lang('Create one now')
            </a>
        </p>
    </div>
</div>
@endsection
