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
    <section class="zod-home-hero">
        <div class="zod-hero-copy">
            <div class="zod-hero-kicker">
                <i data-lucide="sparkles"></i>
                @lang('Cloud hosting control room')
            </div>
            <h1>@lang('Launch hosting, VPS, domains, and support from one sharp workspace.')</h1>
            <p>@lang('ZodHost turns ordering, provisioning, renewals, domains, and helpdesk work into one clean path for customers and admins.')</p>
            <div class="zod-hero-actions">
                @if($firstCategory)
                    <a href="{{ route('service.category', $firstCategory->slug) }}" class="zod-btn zod-btn-primary">
                        <i data-lucide="rocket"></i> @lang('Start a Service')
                    </a>
                @endif
                <a href="{{ route('register.domain') }}" class="zod-btn zod-btn-ghost">
                    <i data-lucide="scan-search"></i> @lang('Search Domain')
                </a>
            </div>
        </div>

        <div class="zod-hero-console" aria-label="@lang('ZodHost operations console preview')">
            <div class="zod-console-top">
                <span></span><span></span><span></span>
                <strong>@lang('ZodHost Ops')</strong>
            </div>
            <div class="zod-console-grid">
                @foreach($opsCards as $card)
                    <article class="zod-console-card zod-tone-{{ $card['tone'] }}">
                        <i data-lucide="{{ $card['icon'] }}"></i>
                        <span>{{ $card['label'] }}</span>
                        <strong>{{ $card['value'] }}</strong>
                    </article>
                @endforeach
            </div>
            <div class="zod-console-flow">
                <div class="zod-flow-line"></div>
                <div class="zod-flow-node active"><i data-lucide="shopping-cart"></i></div>
                <div class="zod-flow-node"><i data-lucide="server-cog"></i></div>
                <div class="zod-flow-node"><i data-lucide="shield-check"></i></div>
                <div class="zod-flow-node"><i data-lucide="smile"></i></div>
            </div>
            <div class="zod-console-list">
                <div><span>@lang('Next order')</span><strong>@lang('VPS package approval')</strong><b>@lang('Ready')</b></div>
                <div><span>@lang('Panel')</span><strong>@lang('WHMPanel / ZodPanel')</strong><b>@lang('Synced')</b></div>
                <div><span>@lang('Client')</span><strong>@lang('Invoices, services, tickets')</strong><b>@lang('Live')</b></div>
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
            <button type="button" data-billing-period="daily">@lang('Daily view')</button>
            <button type="button" class="active" data-billing-period="monthly">@lang('Monthly')</button>
            <button type="button" data-billing-period="annually">@lang('Yearly value')</button>
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
                        @foreach($category->products->take(4) as $product)
                            @php
                                $monthly = (float) $product->price->monthly;
                                $annually = (float) $product->price->annually;
                                $yearlyMonthly = $annually > 0 ? $annually / 12 : $monthly;
                                $features = collect(explode("\n", strip_tags($product->description)))->filter()->take(4);
                            @endphp
                            <article class="zod-plan-tile">
                                <div class="zod-plan-top">
                                    <span>{{ __($category->name) }}</span>
                                    <h4>{{ __($product->name) }}</h4>
                                </div>
                                <div class="zod-plan-price">
                                    <strong
                                        data-daily="{{ getAmount($monthly / 30) }}"
                                        data-monthly="{{ getAmount($monthly) }}"
                                        data-annually="{{ getAmount($yearlyMonthly) }}"
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

                var label = '{{ __('/month') }}';
                if (period === 'daily') {
                    label = '{{ __('/day estimate') }}';
                }
                if (period === 'annually') {
                    label = '{{ __('/month, billed yearly') }}';
                }
                $('[data-period-label]').text(label);
            });
        })(jQuery);
    </script>
@endpush
