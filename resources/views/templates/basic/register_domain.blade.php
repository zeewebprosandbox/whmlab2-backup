@extends($activeTemplate . 'layouts.side_bar')

@section('data')
    <div class="col-lg-12 space-y-6 text-slate-900 font-sans">

        <!-- Domain Search Banner Card -->
        <div class="domain-card-box p-6 bg-white border border-slate-200/80 rounded-2xl space-y-4 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 tracking-tight font-display">@lang('Domain Search & Availability')</h1>
                    <p class="text-xs text-slate-500 mt-0.5">@lang('Enter a domain name or brand to search live availability and claim your name.')</p>
                </div>
                <a href="{{ route('shopping.cart') }}" class="btn-add-cart-pill px-4 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold rounded-full transition-all flex items-center gap-2 self-start sm:self-auto shadow-xs">
                    <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                    <span>@lang('View Cart')</span>
                </a>
            </div>

            <!-- Search Form Bar -->
            <form action="{{ route('register.domain') }}" method="GET" class="flex flex-col sm:flex-row items-stretch gap-2">
                <div class="relative flex-1">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="domain" value="{{ $searchDomain }}" placeholder="@lang('Type domain or keyword (e.g. shpayco.com, mybrand.store)')" class="w-full bg-white border border-slate-200 rounded-xl pl-11 pr-4 py-3 text-slate-900 font-mono text-sm placeholder:text-slate-400 focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm hover:shadow transition-all flex items-center justify-center gap-2">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                    <span>@lang('Search Domain')</span>
                </button>
            </form>
        </div>

        @if($primaryResult)
            <!-- Top Primary Search Result Card -->
            <div class="domain-card-box p-6 bg-white border {{ $primaryResult['available'] ? 'border-indigo-200' : 'border-rose-200' }} rounded-2xl shadow-sm transition-all">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <!-- Left: Domain Name + Savings Badge -->
                    <div class="flex items-center gap-3 flex-wrap">
                        <h2 class="text-2xl font-extrabold font-sans text-slate-900 tracking-tight">{{ $primaryResult['domain'] }}</h2>
                        @if($primaryResult['available'])
                            <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 text-xs font-semibold tracking-wide">
                                @lang('Save 60%')
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-[11px] font-medium flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 orb-pulse"></span>
                                @lang('Available')
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-100 text-xs font-semibold">
                                @lang('Already Taken')
                            </span>
                        @endif
                    </div>

                    <!-- Right: Pricing & Add to Cart Pill Button -->
                    @if($primaryResult['available'])
                        <div class="flex items-center gap-4 self-end sm:self-center">
                            <div class="text-right">
                                <div class="text-xs text-slate-400 line-through font-mono">
                                    {{ showAmount($primaryResult['pricing']['renew'] ?? ($primaryResult['pricing']['price'] * 1.5)) }}
                                </div>
                                <div class="text-xl font-extrabold text-slate-900 font-sans">
                                    {{ showAmount($primaryResult['pricing']['price']) }}<span class="text-xs font-normal text-slate-500">/1st yr</span>
                                </div>
                            </div>
                            <form action="{{ route('shopping.cart.add.domain') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="domain" value="{{ $primaryResult['domain'] }}">
                                <input type="hidden" name="domain_setup_id" value="{{ @$primaryResult['pricing']['setup']->id }}">
                                <button type="submit" class="btn-add-cart-pill px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-full transition-all shadow-sm">
                                    @lang('Add to cart')
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- "More options" Section Title & Filter Pills -->
            <div class="space-y-4 pt-2">
                <h3 class="text-xl font-bold text-slate-900 tracking-tight font-display">@lang('More options')</h3>

                <!-- Category Filter Tabs -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none text-xs">
                    <button type="button" class="tld-filter-tab px-4 py-1.5 rounded-full bg-indigo-600 text-white font-semibold shadow-xs transition-all" data-category="all">
                        @lang('Popular')
                    </button>
                    <button type="button" class="tld-filter-tab px-4 py-1.5 rounded-full bg-white hover:bg-slate-50 border border-slate-200 text-slate-600 font-medium transition-all" data-category="tech">
                        @lang('Technology')
                    </button>
                    <button type="button" class="tld-filter-tab px-4 py-1.5 rounded-full bg-white hover:bg-slate-50 border border-slate-200 text-slate-600 font-medium transition-all" data-category="business">
                        @lang('Business')
                    </button>
                    <button type="button" class="tld-filter-tab px-4 py-1.5 rounded-full bg-white hover:bg-slate-50 border border-slate-200 text-slate-600 font-medium transition-all" data-category="international">
                        @lang('International')
                    </button>
                    <button type="button" class="tld-filter-tab px-4 py-1.5 rounded-full bg-white hover:bg-slate-50 border border-slate-200 text-slate-600 font-medium transition-all" data-category="all">
                        @lang('All')
                    </button>
                </div>

                <!-- Suggestions List Container -->
                <div class="domain-card-box bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-6 shadow-sm">
                    @php
                        $allSuggestions = array_merge($tldSuggestions, $variantSuggestions);
                        $discountPercentages = [89, 57, 18, 97, 50, 75, 65, 40, 80, 70];
                    @endphp

                    @foreach($allSuggestions as $idx => $item)
                        @php
                            $discount = $discountPercentages[$idx % count($discountPercentages)];
                            $origPrice = showAmount($item['pricing']['renew'] ?? ($item['pricing']['price'] * 1.8));
                        @endphp
                        <div class="tld-row-item py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 last:border-0">
                            <!-- Left: Domain Name + Discount Badge -->
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="text-base font-bold font-sans text-slate-900 tracking-tight">{{ $item['domain'] }}</span>
                                <span class="px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 text-[11px] font-semibold">
                                    @lang('Save') {{ $discount }}%
                                </span>
                            </div>

                            <!-- Right: Pricing & Add to Cart Button -->
                            <div class="flex items-center gap-4 self-end sm:self-center">
                                <div class="text-right">
                                    <span class="text-xs text-slate-400 line-through font-mono me-1.5">{{ $origPrice }}</span>
                                    <span class="text-base font-bold text-slate-900 font-sans">{{ showAmount($item['pricing']['price']) }}<span class="text-xs font-normal text-slate-500">/1st yr</span></span>
                                </div>
                                <form action="{{ route('shopping.cart.add.domain') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="domain" value="{{ $item['domain'] }}">
                                    <input type="hidden" name="domain_setup_id" value="{{ @$item['pricing']['setup']->id }}">
                                    <button type="submit" class="btn-add-cart-pill px-5 py-2 bg-slate-50 hover:bg-indigo-600 hover:text-white border border-slate-200 text-slate-700 font-semibold text-xs rounded-full transition-all">
                                        @lang('Add to cart')
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Full TLD Pricing Directory -->
        <div class="space-y-4 pt-4">
            <h3 class="text-xl font-bold text-slate-900 tracking-tight font-display">@lang('All Domain Extensions & Rates')</h3>

            <div class="domain-card-box bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-6 shadow-sm">
                @foreach($domainSetups as $setup)
                    @php
                        $pricing = $setup->pricing;
                        $firstPrice = $pricing ? $pricing->firstPrice : null;
                        $regPrice = isset($firstPrice['price']) ? $firstPrice['price'] : 12.99;
                        $renewPrice = $pricing && isset($pricing->one_year_renew) && $pricing->one_year_renew >= 0 ? $pricing->one_year_renew : $regPrice;
                    @endphp
                    <div class="tld-row-item py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 last:border-0">
                        <div class="flex items-center gap-3">
                            <span class="text-base font-bold font-sans text-slate-900 tracking-tight">.{{ ltrim($setup->extension, '.') }}</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-[10px] font-medium">
                                @lang('Free ID Protection')
                            </span>
                        </div>

                        <div class="flex items-center gap-4 self-end sm:self-center">
                            <div class="text-right">
                                <span class="text-xs text-slate-400 me-2">@lang('Renew'): {{ showAmount($renewPrice) }}/yr</span>
                                <span class="text-base font-bold text-emerald-600 font-sans">{{ showAmount($regPrice) }}<span class="text-xs font-normal text-slate-400">/1st yr</span></span>
                            </div>
                            <a href="{{ route('register.domain') }}?domain=mybrand.{{ ltrim($setup->extension, '.') }}" class="btn-add-cart-pill px-4 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 font-semibold text-xs rounded-full transition-all">
                                @lang('Search')
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
@endsection

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.tld-filter-tab').on('click', function () {
                $('.tld-filter-tab').removeClass('bg-indigo-600 text-white font-semibold').addClass('bg-white text-slate-600 font-medium');
                $(this).removeClass('bg-white text-slate-600 font-medium').addClass('bg-indigo-600 text-white font-semibold');
            });
        })(jQuery);
    </script>
@endpush
