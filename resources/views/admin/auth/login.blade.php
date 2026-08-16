@extends('admin.layouts.master')
@section('content')

<style>
    /* ── Login Page Premium Design ── */
    * { box-sizing: border-box; }

    .login-shell {
        min-height: 100vh;
        display: grid;
        grid-template-columns: 1fr 1fr;
        background: #0B1120;
    }

    /* Left — Brand Panel */
    .login-brand-panel {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: clamp(32px, 5vw, 56px);
        overflow: hidden;
        background:
            linear-gradient(135deg, rgba(103, 61, 230, 0.30) 0%, transparent 48%),
            linear-gradient(225deg, rgba(14, 165, 160, 0.18) 0%, transparent 50%),
            #0B1120;
    }

    .login-brand-panel::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 60% 50% at 30% 60%, rgba(103, 61, 230, 0.18), transparent),
            repeating-linear-gradient(
                135deg,
                rgba(255, 255, 255, 0.03) 0 1px,
                transparent 1px 28px
            );
        pointer-events: none;
    }

    .login-brand-panel > * { position: relative; z-index: 1; }

    .login-logo {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .login-logo-mark {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: linear-gradient(135deg, #673DE6, #5530C4);
        display: grid;
        place-items: center;
        box-shadow: 0 14px 32px rgba(103, 61, 230, 0.40);
    }

    .login-logo-mark svg { width: 22px; height: 22px; color: #fff; }

    .login-logo-text strong {
        display: block;
        font-size: 20px;
        font-weight: 800;
        color: #fff;
        letter-spacing: -0.01em;
    }

    .login-logo-text span {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.48);
        text-transform: uppercase;
        letter-spacing: 0.07em;
        margin-top: 2px;
    }

    .login-hero-copy {
        max-width: 460px;
    }

    .login-hero-copy h1 {
        font-size: clamp(2rem, 3.5vw, 3.2rem);
        font-weight: 900;
        line-height: 1.06;
        letter-spacing: -0.025em;
        color: #ffffff;
        margin: 0 0 16px;
    }

    .login-hero-copy h1 span {
        background: linear-gradient(135deg, #a78bfa, #67e8f9);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .login-hero-copy p {
        font-size: 16px;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.60);
        line-height: 1.72;
        margin: 0 0 32px;
    }

    .login-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        max-width: 460px;
    }

    .login-stat {
        border: 1px solid rgba(255, 255, 255, 0.10);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.05);
        padding: 16px;
        backdrop-filter: blur(12px);
    }

    .login-stat strong {
        display: block;
        font-size: 22px;
        font-weight: 900;
        color: #ffffff;
        letter-spacing: -0.02em;
        line-height: 1;
    }

    .login-stat span {
        display: block;
        margin-top: 5px;
        font-size: 11px;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.50);
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .login-footer-copy {
        font-size: 12.5px;
        color: rgba(255, 255, 255, 0.36);
        font-weight: 500;
    }

    /* Right — Form Panel */
    .login-form-panel {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: clamp(32px, 5vw, 56px);
        background: #F4F7FC;
    }

    .login-card {
        width: 100%;
        max-width: 420px;
    }

    .login-card-head {
        margin-bottom: 32px;
    }

    .login-card-head h2 {
        font-size: 26px;
        font-weight: 900;
        letter-spacing: -0.02em;
        color: #0D1B2A;
        margin: 0 0 6px;
    }

    .login-card-head p {
        font-size: 14px;
        color: #5A6A7E;
        font-weight: 500;
        margin: 0;
    }

    .login-form .form-group {
        margin-bottom: 18px;
    }

    .login-form label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #0D1B2A;
        margin-bottom: 8px;
    }

    .login-form .form-control {
        width: 100%;
        height: 48px;
        border: 1.5px solid #E2E8F0;
        border-radius: 8px;
        background: #ffffff;
        color: #0D1B2A;
        font-size: 14px;
        font-weight: 500;
        padding: 0 16px;
        outline: none;
        transition: border-color 180ms ease, box-shadow 180ms ease;
        box-shadow: 0 1px 3px rgba(13, 27, 42, 0.04);
    }

    .login-form .form-control:focus {
        border-color: #673DE6;
        box-shadow: 0 0 0 4px rgba(103, 61, 230, 0.12);
    }

    .login-form .form-control::placeholder {
        color: #94A3B8;
        font-weight: 400;
    }

    .forget-link {
        font-size: 13px;
        font-weight: 700;
        color: #673DE6;
        transition: color 150ms ease;
    }

    .forget-link:hover { color: #5530C4; }

    .field-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .login-submit-btn {
        width: 100%;
        height: 50px;
        border: none;
        border-radius: 8px;
        background: linear-gradient(135deg, #673DE6 0%, #5530C4 100%);
        color: #ffffff;
        font-size: 15px;
        font-weight: 800;
        letter-spacing: 0;
        cursor: pointer;
        margin-top: 8px;
        box-shadow: 0 12px 32px rgba(103, 61, 230, 0.34);
        transition: transform 180ms cubic-bezier(0.16, 1, 0.3, 1), box-shadow 180ms cubic-bezier(0.16, 1, 0.3, 1), background 180ms ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .login-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 42px rgba(103, 61, 230, 0.44);
        background: linear-gradient(135deg, #7C58F0 0%, #673DE6 100%);
    }

    .login-submit-btn:active {
        transform: translateY(0);
        box-shadow: 0 8px 20px rgba(103, 61, 230, 0.28);
    }

    .login-trust {
        margin-top: 28px;
        padding-top: 24px;
        border-top: 1px solid #E2E8F0;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .login-trust-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 700;
        color: #5A6A7E;
    }

    .login-trust-badge svg {
        width: 14px;
        height: 14px;
        color: #059669;
    }

    .login-trust-sep {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: #CBD5E1;
    }

    /* ── Responsive ── */
    @media (max-width: 1024px) {
        .login-shell { grid-template-columns: 1fr; }
        .login-brand-panel { display: none; }
        .login-form-panel { min-height: 100vh; }
    }
</style>

<div class="login-shell">

    {{-- ─── Left Brand Panel ─── --}}
    <div class="login-brand-panel">
        <div class="login-logo">
            <div class="login-logo-mark">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 1 0 0 6h13.5a3 3 0 1 0 0-6m-16.5-3a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3m-19.5 0a4.5 4.5 0 0 1 .9-2.7L5.737 5.1a3.375 3.375 0 0 1 2.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 0 1 .9 2.7m0 0a3 3 0 0 1-3 3m0 3h.008v.008h-.008v-.008Zm0-6h.008v.008h-.008v-.008Zm-3 6h.008v.008h-.008v-.008Zm0-6h.008v.008h-.008v-.008Z" />
                </svg>
            </div>
            <div class="login-logo-text">
                <strong>{{ __(gs('site_name')) }}</strong>
                <span>Admin Portal</span>
            </div>
        </div>

        <div class="login-hero-copy">
            <h1>Manage your <span>hosting empire</span> from one place</h1>
            <p>A powerful control panel for hosting providers — manage clients, services, invoices, and servers with confidence.</p>
            <div class="login-stats">
                <div class="login-stat">
                    <strong>99.9%</strong>
                    <span>Uptime SLA</span>
                </div>
                <div class="login-stat">
                    <strong>24/7</strong>
                    <span>Monitoring</span>
                </div>
                <div class="login-stat">
                    <strong>256-bit</strong>
                    <span>Encrypted</span>
                </div>
            </div>
        </div>

        <div class="login-footer-copy">
            &copy; {{ date('Y') }} {{ __(gs('site_name')) }}. Secure admin access.
        </div>
    </div>

    {{-- ─── Right Form Panel ─── --}}
    <div class="login-form-panel">
        <div class="login-card">
            <div class="login-card-head">
                <h2>@lang('Welcome back') 👋</h2>
                <p>@lang('Sign in to your admin dashboard')</p>
            </div>

            <form action="{{ route('admin.login') }}" method="POST" class="login-form cmn-form verify-gcaptcha">
                @csrf

                <div class="form-group">
                    <label>@lang('Username')</label>
                    <input type="text"
                           class="form-control"
                           value="{{ old('username') }}"
                           name="username"
                           placeholder="@lang('Enter your username')"
                           required>
                </div>

                <div class="form-group">
                    <div class="field-header">
                        <label style="margin:0">@lang('Password')</label>
                        <a href="{{ route('admin.password.reset') }}" class="forget-link">@lang('Forgot Password?')</a>
                    </div>
                    <input type="password"
                           class="form-control"
                           name="password"
                           placeholder="@lang('Enter your password')"
                           required>
                </div>

                <x-captcha />

                <button type="submit" class="login-submit-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width:18px;height:18px">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                    </svg>
                    @lang('Sign In to Dashboard')
                </button>
            </form>

            <div class="login-trust">
                <span class="login-trust-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                    SSL Secured
                </span>
                <span class="login-trust-sep"></span>
                <span class="login-trust-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0 1 21.75 8.25Z" />
                    </svg>
                    2FA Ready
                </span>
                <span class="login-trust-sep"></span>
                <span class="login-trust-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                    </svg>
                    Enterprise Grade
                </span>
            </div>
        </div>
    </div>

</div>

@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            var parseData = JSON.parse(localStorage.getItem('redirect'));
            if (parseData && parseData['expireTime'] >= '{{ Carbon\Carbon::now() }}') {
                document.querySelector('form').innerHTML += `<input type="hidden" value="${parseData['redirect']}" name="redirect" />`;
            }
        })(jQuery);
    </script>
@endpush