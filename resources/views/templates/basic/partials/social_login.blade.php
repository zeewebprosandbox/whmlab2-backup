@php
    $text = isset($register) ? 'Register' : 'Login';
    $hasSocialLogin = @gs('socialite_credentials')->google->status == Status::ENABLE
        || @gs('socialite_credentials')->facebook->status == Status::ENABLE
        || @gs('socialite_credentials')->linkedin->status == Status::ENABLE;
@endphp

@if ($hasSocialLogin)
    <div class="whm-social-login">
        <span>@lang("$text with")</span>
        <div class="whm-social-icons">
            @if (@gs('socialite_credentials')->google->status == Status::ENABLE)
                <a href="{{ route('user.social.login', 'google') }}" class="whm-social-icon" aria-label="@lang("$text with Google")">
                    <img src="{{ asset($activeTemplateTrue . 'images/google.svg') }}" alt="Google">
                </a>
            @endif

            @if (@gs('socialite_credentials')->facebook->status == Status::ENABLE)
                <a href="{{ route('user.social.login', 'facebook') }}" class="whm-social-icon" aria-label="@lang("$text with Facebook")">
                    <img src="{{ asset($activeTemplateTrue . 'images/facebook.svg') }}" alt="Facebook">
                </a>
            @endif

            @if (@gs('socialite_credentials')->linkedin->status == Status::ENABLE)
                <a href="{{ route('user.social.login', 'linkedin') }}" class="whm-social-icon" aria-label="@lang("$text with Linkedin")">
                    <img src="{{ asset($activeTemplateTrue . 'images/linkdin.svg') }}" alt="Linkedin">
                </a>
            @endif
        </div>
    </div>
    <div class="text-center login-or whm-login-or">
        <span>@lang('or continue with email')</span>
    </div>
@endif
