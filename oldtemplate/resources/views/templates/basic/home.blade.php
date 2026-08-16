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

    $storySlides = [
        [
            'year' => '01',
            'title' => __('Built for dependable hosting'),
            'text' => __('ZodHost packages shared hosting, VPS, dedicated servers, RDP, and streaming services into clear plans clients can compare quickly.'),
            'metric' => __('Clean plan catalog'),
        ],
        [
            'year' => '02',
            'title' => __('Provision with less confusion'),
            'text' => __('Orders, invoices, DNS records, nameservers, and support stay connected, so every service has the right next step.'),
            'metric' => __('One client workspace'),
        ],
        [
            'year' => '03',
            'title' => __('Support stays account-aware'),
            'text' => __('Support PINs, service records, and live chat help staff find the right account without asking clients to repeat every detail.'),
            'metric' => __('Faster support handoff'),
        ],
    ];
@endphp

@section('content')
    <section class="whm-home-hero">
        <div class="whm-home-hero__content">
            <div class="whm-eyebrow">
                <i data-lucide="server"></i>
                @lang('ZodHost cloud hosting')
            </div>
            <h1>@lang('Hosting, VPS, domains, and support without the clutter.')</h1>
            <p>
                @lang('Choose a plan, attach a domain, see exact renewal costs, and manage every service from a focused client panel.')
            </p>
            <div class="whm-hero-actions">
                @if($categories->first())
                    <a href="{{ route('service.category', $categories->first()->slug) }}" class="whm-primary-action">
                        <i data-lucide="shopping-bag"></i> @lang('Browse Plans')
                    </a>
                @endif
                <a href="{{ route('register.domain') }}" class="whm-secondary-action">
                    <i data-lucide="globe"></i> @lang('Find a Domain')
                </a>
            </div>
        </div>
    </section>

    <section class="whm-story-section" aria-label="@lang('Success story')">
        <div class="whm-story-copy">
            <p>@lang('How ZodHost works')</p>
            <h2>@lang('A hosting flow designed around real client tasks.')</h2>
            <span>@lang('Short, useful sections keep decisions clear: pick a plan, configure DNS, pay invoices, and get support.')</span>
        </div>

        <div class="whm-story-stage" data-story-stage>
            @foreach($storySlides as $story)
                <article class="whm-story-slide {{ $loop->first ? 'active' : '' }}" data-story-slide>
                    <div>
                        <small>{{ $story['year'] }}</small>
                        <h3>{{ $story['title'] }}</h3>
                        <p>{{ $story['text'] }}</p>
                    </div>
                    <strong>{{ $story['metric'] }}</strong>
                </article>
            @endforeach

            <div class="whm-story-controls">
                @foreach($storySlides as $story)
                    <button type="button" class="{{ $loop->first ? 'active' : '' }}" data-story-dot="{{ $loop->index }}" aria-label="@lang('Show story step') {{ $loop->iteration }}"></button>
                @endforeach
            </div>
        </div>
    </section>

    <section class="whm-image-story-section" aria-label="@lang('Infrastructure overview')">
        <div class="whm-image-story-media">
            <img src="https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1200&q=82" alt="@lang('Data center infrastructure')">
        </div>
        <div class="whm-image-story-copy">
            <p>@lang('Infrastructure workspace')</p>
            <h2>@lang('Plans, panel access, and support stay connected from day one.')</h2>
            <span>@lang('ZodHost keeps the storefront simple while the client area tracks renewals, support PINs, invoices, domains, and WHMPanel access in one place.')</span>
            <div class="whm-image-story-list">
                <div><i data-lucide="shield-check"></i><strong>@lang('Account-aware support')</strong><small>@lang('Expiring support PINs help staff locate the right services.')</small></div>
                <div><i data-lucide="panel-top"></i><strong>@lang('Panel-ready services')</strong><small>@lang('Hosting records are prepared for WHMPanel or legacy panels.')</small></div>
                <div><i data-lucide="receipt-text"></i><strong>@lang('Clear billing flow')</strong><small>@lang('Clients can sign in or create an account during checkout.')</small></div>
            </div>
        </div>
    </section>

    <section class="whm-home-section" id="plans">
        <div class="whm-section-heading">
            <div>
                <p>@lang('Hosting plans')</p>
                <h2>@lang('Compact plans with clear billing and no hidden steps.')</h2>
            </div>
            <div class="whm-billing-switch" role="group" aria-label="@lang('Billing period')">
                <button type="button" data-billing-period="daily">@lang('Daily est.')</button>
                <button type="button" class="active" data-billing-period="monthly">@lang('Monthly')</button>
                <button type="button" data-billing-period="annually">@lang('Yearly')</button>
            </div>
        </div>

        <div class="whm-category-tabs" role="tablist">
            @foreach($categories as $category)
                <button type="button" class="{{ $loop->first ? 'active' : '' }}" data-home-category="{{ $category->slug }}">
                    <i data-lucide="{{ str_contains($category->slug, 'domain') ? 'globe' : (str_contains($category->slug, 'vps') || str_contains($category->slug, 'dedicated') ? 'database' : 'server') }}"></i>
                    {{ __($category->name) }}
                </button>
            @endforeach
        </div>

        <div class="whm-category-panels">
            @foreach($categories as $category)
                <div class="whm-category-panel {{ $loop->first ? 'active' : '' }}" data-category-panel="{{ $category->slug }}">
                    <div class="whm-category-panel__intro">
                        <div>
                            <h3>{{ __($category->name) }}</h3>
                            <p>{{ __($category->short_description) }}</p>
                        </div>
                        <a href="{{ route('service.category', $category->slug) }}">
                            @lang('View all') <i data-lucide="arrow-right"></i>
                        </a>
                    </div>

                    <div class="whm-plan-grid">
                        @foreach($category->products->take(3) as $product)
                            @php
                                $monthly = (float) $product->price->monthly;
                                $annually = (float) $product->price->annually;
                                $yearlyMonthly = $annually > 0 ? $annually / 12 : $monthly;
                                $features = collect(explode("\n", strip_tags($product->description)))->filter()->take(5);
                            @endphp
                            <article class="whm-plan-card">
                                <div class="whm-plan-card__top">
                                    <span>{{ __($category->name) }}</span>
                                    <h4>{{ __($product->name) }}</h4>
                                </div>
                                <div class="whm-plan-price">
                                    <strong
                                        data-daily="{{ getAmount($monthly / 30) }}"
                                        data-monthly="{{ getAmount($monthly) }}"
                                        data-annually="{{ getAmount($yearlyMonthly) }}"
                                    >{{ gs('cur_sym') }}{{ getAmount($monthly) }}</strong>
                                    <span data-period-label>@lang('/month')</span>
                                </div>
                                <ul>
                                    @foreach($features as $feature)
                                        <li><i data-lucide="check"></i>{{ __($feature) }}</li>
                                    @endforeach
                                </ul>
                                <a href="{{ route('product.configure', ['categorySlug' => $category->slug, 'productSlug' => $product->slug, 'id' => $product->id]) }}" class="whm-plan-action">
                                    @lang('Configure') <i data-lucide="arrow-up-right"></i>
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="whm-home-section whm-split-section">
        <div class="whm-domain-card">
            <p>@lang('Domain search')</p>
            <h2>@lang('Search a domain and connect it to the right hosting plan.')</h2>
            @include($activeTemplate . 'partials.domain_search_form')
            <div class="whm-tld-row">
                @foreach($tlds as $tld)
                    <span>{{ $tld->extension }} <b>{{ showAmount(@$tld->pricing->firstPrice['price'] ?? 0) }}</b></span>
                @endforeach
            </div>
        </div>

        <div class="whm-workflow-card">
            <div class="whm-workflow-step">
                <i data-lucide="mouse-pointer-click"></i>
                <div><strong>@lang('Pick a service')</strong><span>@lang('Shared, VPS, dedicated, RDP, or streaming.')</span></div>
            </div>
            <div class="whm-workflow-step">
                <i data-lucide="sliders-horizontal"></i>
                <div><strong>@lang('Compare billing')</strong><span>@lang('Daily estimates, monthly pricing, and yearly value.')</span></div>
            </div>
            <div class="whm-workflow-step">
                <i data-lucide="shopping-cart"></i>
                <div><strong>@lang('Checkout cleanly')</strong><span>@lang('Sign in or create an account directly in the cart.')</span></div>
            </div>
        </div>
    </section>

    <section class="whm-home-section">
        <div class="whm-section-heading">
            <div>
                <p>@lang('Built for repeat use')</p>
                <h2>@lang('Everything clients need after purchase stays visible.')</h2>
            </div>
        </div>
        <div class="whm-feature-grid">
            <div><i data-lucide="scan-search"></i><strong>@lang('Fast scanning')</strong><span>@lang('Compact cards show the plan, price, and strongest features first.')</span></div>
            <div><i data-lucide="server-cog"></i><strong>@lang('DNS visibility')</strong><span>@lang('Purchased hosting services show assigned provider nameservers clearly.')</span></div>
            <div><i data-lucide="message-circle"></i><strong>@lang('Support ready')</strong><span>@lang('Live chat and expiring support PINs keep help connected to the right account.')</span></div>
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

                $('.whm-plan-price strong').each(function() {
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

            var storyIndex = 0;
            var storySlides = $('[data-story-slide]');
            var storyDots = $('[data-story-dot]');

            function showStorySlide(index) {
                if (!storySlides.length) {
                    return;
                }

                storyIndex = index % storySlides.length;
                storySlides.removeClass('active').eq(storyIndex).addClass('active');
                storyDots.removeClass('active').eq(storyIndex).addClass('active');
            }

            var storyTimer = setInterval(function() {
                showStorySlide(storyIndex + 1);
            }, 3600);

            storyDots.on('click', function() {
                clearInterval(storyTimer);
                showStorySlide(parseInt($(this).data('story-dot')));
                storyTimer = setInterval(function() {
                    showStorySlide(storyIndex + 1);
                }, 3600);
            });
        })(jQuery);
    </script>
@endpush
