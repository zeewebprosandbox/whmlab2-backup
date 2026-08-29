@php
    $text = isset($register) ? 'Sign up' : 'Sign in';
    $hasSocialLogin = @gs('socialite_credentials')->google->status == Status::ENABLE
        || @gs('socialite_credentials')->facebook->status == Status::ENABLE
        || @gs('socialite_credentials')->linkedin->status == Status::ENABLE;
@endphp

@if ($hasSocialLogin)
    <div class="space-y-3">
        <div class="grid grid-cols-1 gap-2.5">
            @if (@gs('socialite_credentials')->google->status == Status::ENABLE)
                <a href="{{ route('user.social.login', 'google') }}" 
                    class="w-full py-3 px-4 bg-white hover:bg-neutral-50 border border-neutral-200/90 rounded-xl text-xs font-semibold text-neutral-800 transition-all shadow-sm flex items-center justify-center gap-2.5">
                    <img src="{{ asset($activeTemplateTrue . 'images/google.svg') }}" alt="Google" class="w-4 h-4 object-contain">
                    <span>@lang("$text with Google")</span>
                </a>
            @endif

            @if (@gs('socialite_credentials')->facebook->status == Status::ENABLE)
                <a href="{{ route('user.social.login', 'facebook') }}" 
                    class="w-full py-3 px-4 bg-white hover:bg-neutral-50 border border-neutral-200/90 rounded-xl text-xs font-semibold text-neutral-800 transition-all shadow-sm flex items-center justify-center gap-2.5">
                    <img src="{{ asset($activeTemplateTrue . 'images/facebook.svg') }}" alt="Facebook" class="w-4 h-4 object-contain">
                    <span>@lang("$text with Facebook")</span>
                </a>
            @endif

            @if (@gs('socialite_credentials')->linkedin->status == Status::ENABLE)
                <a href="{{ route('user.social.login', 'linkedin') }}" 
                    class="w-full py-3 px-4 bg-white hover:bg-neutral-50 border border-neutral-200/90 rounded-xl text-xs font-semibold text-neutral-800 transition-all shadow-sm flex items-center justify-center gap-2.5">
                    <img src="{{ asset($activeTemplateTrue . 'images/linkdin.svg') }}" alt="Linkedin" class="w-4 h-4 object-contain">
                    <span>@lang("$text with LinkedIn")</span>
                </a>
            @endif
        </div>

        <div class="relative flex items-center justify-center py-2">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-neutral-200/80"></div></div>
            <div class="relative bg-white px-3 text-[11px] font-medium uppercase tracking-wider text-neutral-400">@lang('or continue with')</div>
        </div>
    </div>
@endif
