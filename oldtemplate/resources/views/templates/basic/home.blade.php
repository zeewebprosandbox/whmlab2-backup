@extends($activeTemplate . 'layouts.frontend')

@php
    $categories = App\Models\ServiceCategory::active()
        ->with(['products' => function ($product) {
            $product->active()->whereHas('price', function ($price) {
                $price->priceFilter();
            })->with('price')->orderBy('id');
        }])
        ->get()
        ->filter(function ($category) {
            return $category->products->count();
        })
        ->values();

    $tlds = App\Models\DomainSetup::active()->with('pricing')->orderBy('id')->take(6)->get();

    $firstCategory = $categories->first();

    $heroMetrics = [
        ['value' => $categories->count() ?: '6+', 'label' => __('service lanes')],
        ['value' => $tlds->count() ?: '24+', 'label' => __('domain zones')],
        ['value' => __('24/7'), 'label' => __('support flow')],
    ];

    $heroPills = $categories->take(4)->map(function ($category) {
        return [
            'name' => __($category->name),
            'icon' => str_contains($category->slug, 'domain') ? 'globe-2' : (str_contains($category->slug, 'vps') || str_contains($category->slug, 'dedicated') ? 'server-cog' : 'box'),
        ];
    })->values();

    $opsCards = [
        ['icon' => 'cpu', 'label' => __('Compute'), 'value' => __('VPS nodes'), 'tone' => 'blue'],
        ['icon' => 'globe-2', 'label' => __('Domains'), 'value' => __('DNS ready'), 'tone' => 'teal'],
        ['icon' => 'receipt-text', 'label' => __('Billing'), 'value' => __('Clear renewals'), 'tone' => 'gold'],
        ['icon' => 'message-circle', 'label' => __('Support'), 'value' => __('Account aware'), 'tone' => 'green'],
    ];

    $launchSteps = [
        ['icon' => 'search', 'title' => __('Choose'), 'text' => __('Find hosting, VPS, domains, RDP, or mail from one catalog.')],
        ['icon' => 'sliders-horizontal', 'title' => __('Configure'), 'text' => __('Pick billing, options, and the domain attached to the service.')],
        ['icon' => 'zap', 'title' => __('Provision'), 'text' => __('Approved services are prepared for panel access and support visibility.')],
        ['icon' => 'layout-dashboard', 'title' => __('Operate'), 'text' => __('Renewals, nameservers, tickets, invoices, and services stay together.')],
    ];
@endphp

@section('content')
    <section class="zod-home-hero zod-hero-focused">
        <div class="zod-hero-bg-grid"></div>
        <div class="zod-hero-bg-glow"></div>

        <div class="zod-hero-content">
            <div class="zod-hero-kicker">
                <span class="zod-kicker-dot"></span>
                <i data-lucide="shield-check"></i>
                <span>@lang('High-Performance Cloud & Web Infrastructure')</span>
            </div>

            <h1 class="zod-hero-title">@lang('Hosting made clear, fast, and ready to scale.')</h1>

            <p class="zod-hero-desc">@lang('Deploy NVMe web hosting, high-performance cloud VPS, business email clusters, and instant DNS domains from one unified workspace built for speed and reliability.')</p>

            <div class="zod-hero-actions">
                @if($firstCategory)
                    <a href="{{ route('service.category', $firstCategory->slug) }}" class="zod-btn zod-btn-primary">
                        <i data-lucide="rocket"></i>
                        <span>@lang('Browse Services')</span>
                    </a>
                @endif
                <a href="{{ route('register.domain') }}" class="zod-btn zod-btn-ghost">
                    <i data-lucide="scan-search"></i>
                    <span>@lang('Register Domain')</span>
                </a>
            </div>

            <!-- Features Trust Strip -->
            <div class="zod-hero-trust-strip">
                <div class="zod-trust-item">
                    <i data-lucide="zap" class="text-amber-500"></i>
                    <span>@lang('NVMe Fast Storage')</span>
                </div>
                <div class="zod-trust-item">
                    <i data-lucide="shield" class="text-indigo-600"></i>
                    <span>@lang('Free SSL Certificates')</span>
                </div>
                <div class="zod-trust-item">
                    <i data-lucide="globe" class="text-cyan-500"></i>
                    <span>@lang('Instant DNS Provisioning')</span>
                </div>
                <div class="zod-trust-item">
                    <i data-lucide="clock" class="text-emerald-500"></i>
                    <span>@lang('99.9% Uptime SLA')</span>
                </div>
            </div>

            <!-- 4 Modern Quick Service Cards -->
            <div class="zod-quick-services-grid" aria-label="@lang('Core hosting infrastructure services')">
                <a href="{{ route('service.category') }}" class="zod-quick-card">
                    <div class="zod-quick-card-icon zod-icon-indigo">
                        <i data-lucide="server"></i>
                    </div>
                    <div class="zod-quick-card-body">
                        <div class="zod-quick-card-head">
                            <h4>@lang('Web Hosting')</h4>
                            <span class="zod-quick-tag">@lang('NVMe Fast')</span>
                        </div>
                        <p>@lang('Ultra-fast PCIe Gen4 NVMe storage with automated 1-click app installs & free SSL.')</p>
                        <div class="zod-quick-card-foot">
                            <span class="zod-quick-explore">@lang('Explore Plans') <i data-lucide="arrow-up-right"></i></span>
                        </div>
                    </div>
                </a>

                <a href="{{ route('service.category') }}" class="zod-quick-card">
                    <div class="zod-quick-card-icon zod-icon-cyan">
                        <i data-lucide="cpu"></i>
                    </div>
                    <div class="zod-quick-card-body">
                        <div class="zod-quick-card-head">
                            <h4>@lang('Cloud VPS')</h4>
                            <span class="zod-quick-tag">@lang('Root Access')</span>
                        </div>
                        <p>@lang('Dedicated KVM compute instances with instant provisioning & 10Gbps network.')</p>
                        <div class="zod-quick-card-foot">
                            <span class="zod-quick-explore">@lang('Configure VPS') <i data-lucide="arrow-up-right"></i></span>
                        </div>
                    </div>
                </a>

                <a href="{{ route('register.domain') }}" class="zod-quick-card">
                    <div class="zod-quick-card-icon zod-icon-teal">
                        <i data-lucide="globe"></i>
                    </div>
                    <div class="zod-quick-card-body">
                        <div class="zod-quick-card-head">
                            <h4>@lang('Domains & DNS')</h4>
                            <span class="zod-quick-tag">@lang('Anycast')</span>
                        </div>
                        <p>@lang('Register global TLDs with instant propagation, WHOIS privacy & DNSSEC.')</p>
                        <div class="zod-quick-card-foot">
                            <span class="zod-quick-explore">@lang('Search Domain') <i data-lucide="arrow-up-right"></i></span>
                        </div>
                    </div>
                </a>

                <a href="{{ route('service.category') }}" class="zod-quick-card">
                    <div class="zod-quick-card-icon zod-icon-emerald">
                        <i data-lucide="shield-check"></i>
                    </div>
                    <div class="zod-quick-card-body">
                        <div class="zod-quick-card-head">
                            <h4>@lang('Security & Mail')</h4>
                            <span class="zod-quick-tag">@lang('100% Inbox')</span>
                        </div>
                        <p>@lang('Business email clusters with DMARC/DKIM authentication & DDoS protection.')</p>
                        <div class="zod-quick-card-foot">
                            <span class="zod-quick-explore">@lang('View Services') <i data-lucide="arrow-up-right"></i></span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <section class="zod-home-section zod-lanes-section" id="plans">
        <div class="zod-section-head">
            <span>@lang('Product lanes')</span>
            <h2>@lang('Pick the lane. Configure the plan. Keep the panel clean.')</h2>
            <p>@lang('Every category becomes a focused product lane with pricing, features, and a direct configure action.')</p>
        </div>

        <div class="zod-lane-tabs" role="tablist">
            @foreach($categories as $category)
                <button type="button" class="{{ $loop->first ? 'active' : '' }}" data-home-category="{{ $category->slug }}">
                    <i data-lucide="{{ str_contains($category->slug, 'domain') ? 'globe' : (str_contains($category->slug, 'vps') || str_contains($category->slug, 'dedicated') ? 'server-cog' : 'box') }}"></i>
                    {{ __($category->name) }}
                </button>
            @endforeach
        </div>

        <div class="zod-billing-strip" role="group" aria-label="@lang('Billing period')">
            <button type="button" class="active" data-billing-period="monthly">@lang('Monthly')</button>
            <button type="button" data-billing-period="weekly">@lang('Weekly')</button>
        </div>

        <div class="zod-lane-panels">
            @foreach($categories as $category)
                <div class="zod-lane-panel {{ $loop->first ? 'active' : '' }}" data-category-panel="{{ $category->slug }}">
                    <div class="zod-lane-intro">
                        <div>
                            <small>@lang('Current lane')</small>
                            <h3>{{ __($category->name) }}</h3>
                            <p>{{ __($category->short_description) }}</p>
                        </div>
                        <a href="{{ route('service.category', $category->slug) }}">@lang('View catalog') <i data-lucide="arrow-right"></i></a>
                    </div>

                    <div class="zod-plan-strip">
                        @foreach($category->products->take(3) as $product)
                            @php
                                $monthly = (float) $product->price->monthly;
                                $weekly = $monthly > 0 ? $monthly / 4.33 : 0;
                                $features = collect(explode("\n", strip_tags($product->description)))->filter()->take(4);
                            @endphp
                            <article class="zod-plan-tile">
                                <div class="zod-plan-top">
                                    <span>{{ __($category->name) }}</span>
                                    <h4>{{ __($product->name) }}</h4>
                                </div>
                                <div class="zod-plan-price">
                                    <strong
                                        data-monthly="{{ getAmount($monthly) }}"
                                        data-weekly="{{ getAmount($weekly) }}"
                                    >{{ gs('cur_sym') }}{{ getAmount($monthly) }}</strong>
                                    <small data-period-label>@lang('/month')</small>
                                </div>
                                <ul>
                                    @forelse($features as $feature)
                                        <li><i data-lucide="check"></i>{{ __($feature) }}</li>
                                    @empty
                                        <li><i data-lucide="check"></i>@lang('Panel-ready service')</li>
                                        <li><i data-lucide="check"></i>@lang('Client billing access')</li>
                                    @endforelse
                                </ul>
                                <a href="{{ route('product.configure', ['categorySlug' => $category->slug, 'productSlug' => $product->slug, 'id' => $product->id]) }}">
                                    @lang('Configure') <i data-lucide="arrow-up-right"></i>
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="zod-home-section zod-launch-section">
        <div class="zod-section-head">
            <span>@lang('Launch sequence')</span>
            <h2>@lang('A buying flow that feels like a guided deployment.')</h2>
        </div>
        <div class="zod-launch-grid">
            @foreach($launchSteps as $step)
                <article>
                    <div><i data-lucide="{{ $step['icon'] }}"></i><b>0{{ $loop->iteration }}</b></div>
                    <h3>{{ $step['title'] }}</h3>
                    <p>{{ $step['text'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="zod-home-section zod-domain-console">
        <div class="zod-domain-main">
            <span>@lang('Domain command')</span>
            <h2>@lang('Search the name, attach the hosting, and keep DNS visible.')</h2>
            @include($activeTemplate . 'partials.domain_search_form')
        </div>
        <div class="zod-tld-board">
            @foreach($tlds as $tld)
                <div>
                    <strong>{{ $tld->extension }}</strong>
                    <span>{{ showAmount(@$tld->pricing->firstPrice['price'] ?? 0) }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="zod-home-section zod-proof-section">
        <div class="zod-proof-card">
            <i data-lucide="panel-top"></i>
            <h3>@lang('Panel-aware services')</h3>
            <p>@lang('Services can point clients to WHMPanel, ZodPanel, legacy panels, nameservers, webmail, and service details from one area.')</p>
        </div>
        <div class="zod-proof-card">
            <i data-lucide="life-buoy"></i>
            <h3>@lang('Support without repeated context')</h3>
            <p>@lang('Support PINs and service records help staff verify accounts and understand the customer’s active products quickly.')</p>
        </div>
        <div class="zod-proof-card">
            <i data-lucide="file-check-2"></i>
            <h3>@lang('Billing that stays readable')</h3>
            <p>@lang('Invoices, renewal dates, domain status, and recurring amounts remain clear after the first checkout.')</p>
        </div>
    </section>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            $('[data-home-category]').on('click', function() {
                var slug = $(this).data('home-category');
                $('[data-home-category]').removeClass('active');
                $(this).addClass('active');
                $('[data-category-panel]').removeClass('active');
                $('[data-category-panel="' + slug + '"]').addClass('active');

                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });

            $('[data-billing-period]').on('click', function() {
                var period = $(this).data('billing-period');
                $('[data-billing-period]').removeClass('active');
                $(this).addClass('active');

                $('.zod-plan-price strong').each(function() {
                    var value = $(this).data(period);
                    $(this).text('{{ gs('cur_sym') }}' + value);
                });

                var label = period === 'weekly' ? '{{ __('/week') }}' : '{{ __('/month') }}';
                $('[data-period-label]').text(label);
            });
        })(jQuery);
    </script>
@endpush
