@extends($activeTemplate . 'layouts.app')

@section('app')
<div class="min-h-screen bg-[#0A0A0B] text-[#F5F5F7] flex flex-col lg:flex-row overflow-hidden relative font-sans">
    
    <!-- Left 55%: Immersive Mesh Gradient & Branding -->
    <div class="lg:w-[55%] relative flex flex-col justify-between p-8 lg:p-16 overflow-hidden border-b lg:border-b-0 lg:border-r border-white/5 bg-[#0D0D10]">
        <!-- Mesh Animation Blobs -->
        <div class="mesh-blob-1 -top-20 -left-20 pointer-events-none"></div>
        <div class="mesh-blob-2 bottom-10 right-10 pointer-events-none"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(99,102,241,0.15),transparent_60%)] pointer-events-none"></div>

        <!-- Top Header / Logo -->
        <div class="relative z-10">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 to-cyan-400 p-[1px] shadow-glow-accent">
                    <div class="w-full h-full bg-[#0A0A0B] rounded-[7px] flex items-center justify-center text-indigo-400 group-hover:text-cyan-400 transition-colors">
                        <i data-lucide="server" class="w-5 h-5"></i>
                    </div>
                </div>
                <div>
                    <span class="block text-lg font-bold tracking-tight text-white leading-none">{{ gs('site_name') }}</span>
                    <span class="block text-[11px] font-semibold tracking-wider text-neutral-400 uppercase mt-1">@lang('Cloud Workspace')</span>
                </div>
            </a>
        </div>

        <!-- Center Hero Copy -->
        <div class="relative z-10 my-12 max-w-xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-semibold uppercase tracking-wider mb-6">
                <span class="w-2 h-2 rounded-full bg-cyan-400 orb-pulse"></span>
                @lang('Orbital Platform v2.0')
            </div>
            <h1 class="text-3xl lg:text-5xl font-extrabold tracking-tight text-white leading-[1.15] mb-4">
                @lang('High performance hosting, simplified.')
            </h1>
            <p class="text-neutral-400 text-base leading-relaxed">
                @lang('Manage NVMe cloud servers, domains, SSL certificates, and automated billing through a high-speed unified console.')
            </p>

            <!-- Feature Pills -->
            <div class="grid grid-cols-2 gap-4 mt-8">
                <div class="flex items-center gap-3 p-3 rounded-lg bg-white/[0.03] border border-white/5">
                    <div class="w-8 h-8 rounded-md bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-white">99.99% Uptime</div>
                        <div class="text-[11px] text-neutral-400">Automated failover</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-lg bg-white/[0.03] border border-white/5">
                    <div class="w-8 h-8 rounded-md bg-cyan-500/10 flex items-center justify-center text-cyan-400">
                        <i data-lucide="zap" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-white">NVMe Speed</div>
                        <div class="text-[11px] text-neutral-400">Sub-10ms latency</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer / Trust Signals -->
        <div class="relative z-10 flex items-center justify-between pt-6 border-t border-white/5 text-xs text-neutral-500">
            <div>&copy; {{ date('Y') }} {{ gs('site_name') }}. All rights reserved.</div>
            <div class="flex items-center gap-2 text-neutral-400">
                <i data-lucide="lock" class="w-3.5 h-3.5 text-cyan-400"></i>
                <span>256-bit SSL Encrypted</span>
            </div>
        </div>
    </div>

    <!-- Right 45%: Clean Form Panel -->
    <div class="lg:w-[45%] flex flex-col justify-center items-center p-6 lg:p-16 bg-[#141416] relative z-10">
        <div class="w-full max-w-md">
            @yield('auth')
        </div>
    </div>

</div>
@endsection
