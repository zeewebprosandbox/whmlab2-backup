@extends($activeTemplate . 'layouts.auth')

@section('auth')
<form action="{{ route('user.login') }}" class="account-form verify-gcaptcha" method="POST">
    @csrf

    <div class="whm-auth-form-head">
        <span>@lang('Secure client access')</span>
        <h2>@lang('Welcome back')</h2>
        <p>@lang('Sign in to manage services, domains, invoices, support tickets, and WHMPanel access.')</p>
    </div>

    @include($activeTemplate.'partials.social_login')

    <div class="row gy-3">
        <div class="col-12">
            <div class="form-group whm-field">
                <input type="text" name="username" value="{{ old('username') }}" required
                    class="form-control form--control h-45" placeholder="@lang('Username or email')">
            </div>
        </div>
        <div class="col-12">
            <div class="form-group whm-field">
                <input type="password" name="password" class="form-control form--control h-45" required placeholder="@lang('Password')">
            </div>
        </div>
        <div class="col-12">
            <div class="whm-auth-options">
                <div class="form-group custom--checkbox">
                    <input type="checkbox" id="remember" name="remember" class="form-check-input"
                        {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">@lang('Remember Me')</label>
                </div>
                <a href="{{ route('user.password.request') }}" class="text--base fw-bold">@lang('Forgot Password?')</a>
            </div>
        </div>

        <x-captcha />

        <div class="col-12">
            <button type="submit" class="btn btn--base w-100 whm-auth-submit">@lang('Login')</button>
        </div>
        <div class="col-12">
            <p class="text-center whm-auth-switch">
                @lang('Don\'t have any account?')
                <a href="{{ route('user.register') }}" class="fw-bold text--base">@lang('Create one')</a>
            </p>
        </div>
    </div>
</form>
@endsection
