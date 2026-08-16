@extends($activeTemplate . 'layouts.side_bar')

@section('data')
    <div class="col-lg-12 space-y-6 text-white font-sans">

        <!-- Domain Search Banner Card -->
        <div class="domain-card-box p-6 bg-[#141416] border border-white/10 rounded-2xl space-y-4 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight" style="color: #ffffff !important; font-size: 20px !important;">@lang('Domain Search & Availability')</h1>
                    <p class="text-xs text-neutral-400 mt-0.5" style="color: #a3a3a3 !important; font-size: 12px !important;">@lang('Enter a domain name or brand to search live availability and claim your name.')</p>
                </div>
                <a href="{{ route('shopping.cart') }}" class="btn-add-cart-pill px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/10 text-white text-xs font-medium rounded-full transition-all flex items-center gap-2 self-start sm:self-auto">
                    <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                    <span>@lang('View Cart')</span>
                </a>
            </div>

            <!-- Search Form Bar -->
            <form action="{{ route('register.domain') }}" method="GET" class="flex flex-col sm:flex-row items-stretch gap-2">
                <div class="relative flex-1">
                    <i data-lucide="search" class="w-4 h-4 text-neutral-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="domain" value="{{ $searchDomain }}" placeholder="@lang('Type domain or keyword (e.g. shpayco.com, mybrand.store)')" class="w-full bg-[#1C1C1F] border border-white/10 rounded-xl pl-11 pr-4 py-3 text-white font-mono text-sm placeholder:text-neutral-500 focus:outline-none focus:border-indigo-500 transition-all" style="background-color: #1c1c1f !important; color: #ffffff !important; border: 1px solid rgba(255,255,255,0.15) !important;">
                </div>
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-xl shadow-glow-accent transition-all flex items-center justify-center gap-2" style="background-color: #4f46e5 !important; color: #ffffff !important; border-radius: 12px !important;">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                    <span>@lang('Search Domain')</span>
                </button>
            </form>
        </div>

        @if($primaryResult)
            <!-- Top Primary Search Result Card (Reference Top Box Design) -->
            <div class="domain-card-box p-6 bg-[#141416] border {{ $primaryResult['available'] ? 'border-white/20' : 'border-rose-500/30' }} rounded-2xl shadow-xl transition-all" style="background-color: #141416 !important; border: 1px solid rgba(255,255,255,0.15) !important; border-radius: 16px !important; padding: 24px !important;">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <!-- Left: Domain Name + Savings Badge -->
                    <div class="flex items-center gap-3 flex-wrap">
                        <h2 class="text-2xl font-bold font-sans text-white tracking-tight" style="color: #ffffff !important; font-size: 24px !important; font-weight: 700 !important;">{{ $primaryResult['domain'] }}</h2>
                        @if($primaryResult['available'])
                            <span class="px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-semibold tracking-wide" style="background-color: rgba(99,102,241,0.2) !important; color: #a5b4fc !important; border: 1px solid rgba(99,102,241,0.3) !important; border-radius: 9999px !important; padding: 4px 12px !important; font-size: 12px !important;">
                                @lang('Save 60%')
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[11px] font-medium flex items-center gap-1" style="color: #34d399 !important;">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 orb-pulse"></span>
                                @lang('Available')
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 text-xs font-semibold" style="color: #fb7185 !important;">
                                @lang('Already Taken')
                            </span>
                        @endif
                    </div>

                    <!-- Right: Pricing & Add to Cart Pill Button -->
                    @if($primaryResult['available'])
                        <div class="flex items-center gap-4 self-end sm:self-center">
                            <div class="text-right">
                                <div class="text-xs text-neutral-400 line-through font-mono" style="color: #a3a3a3 !important; text-decoration: line-through !important;">
                                    {{ showAmount($primaryResult['pricing']['renew'] ?? ($primaryResult['pricing']['price'] * 1.5)) }}
                                </div>
                                <div class="text-xl font-bold text-white font-sans" style="color: #ffffff !important; font-size: 20px !important; font-weight: 700 !important;">
                                    {{ showAmount($primaryResult['pricing']['price']) }}<span class="text-xs font-normal text-neutral-400" style="color: #a3a3a3 !important; font-size: 12px !important;">/1st yr</span>
                                </div>
                            </div>
                            <form action="{{ route('shopping.cart.add.domain') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="domain" value="{{ $primaryResult['domain'] }}">
                                <input type="hidden" name="domain_setup_id" value="{{ @$primaryResult['pricing']['setup']->id }}">
                                <button type="submit" class="btn-add-cart-pill px-6 py-2.5 bg-white/10 hover:bg-white hover:text-black border border-white/20 text-white font-semibold text-xs rounded-full transition-all">
                                    @lang('Add to cart')
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- "More options" Section Title & Filter Pills -->
            <div class="space-y-4 pt-2">
                <h3 class="text-xl font-bold text-white tracking-tight" style="color: #ffffff !important; font-size: 20px !important; font-weight: 700 !important;">@lang('More options')</h3>

                <!-- Category Filter Tabs -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none text-xs">
                    <button type="button" class="tld-filter-tab px-4 py-1.5 rounded-full bg-white text-black font-semibold shadow transition-all" data-category="all" style="background-color: #ffffff !important; color: #000000 !important; border-radius: 9999px !important; padding: 6px 16px !important; font-weight: 600 !important;">
                        @lang('Popular')
                    </button>
                    <button type="button" class="tld-filter-tab px-4 py-1.5 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 text-neutral-300 font-medium transition-all" data-category="tech" style="background-color: rgba(255,255,255,0.05) !important; color: #d4d4d4 !important; border: 1px solid rgba(255,255,255,0.1) !important; border-radius: 9999px !important; padding: 6px 16px !important;">
                        @lang('Technology')
                    </button>
                    <button type="button" class="tld-filter-tab px-4 py-1.5 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 text-neutral-300 font-medium transition-all" data-category="business" style="background-color: rgba(255,255,255,0.05) !important; color: #d4d4d4 !important; border: 1px solid rgba(255,255,255,0.1) !important; border-radius: 9999px !important; padding: 6px 16px !important;">
                        @lang('Business')
                    </button>
                    <button type="button" class="tld-filter-tab px-4 py-1.5 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 text-neutral-300 font-medium transition-all" data-category="international" style="background-color: rgba(255,255,255,0.05) !important; color: #d4d4d4 !important; border: 1px solid rgba(255,255,255,0.1) !important; border-radius: 9999px !important; padding: 6px 16px !important;">
                        @lang('International')
                    </button>
                    <button type="button" class="tld-filter-tab px-4 py-1.5 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 text-neutral-300 font-medium transition-all" data-category="all" style="background-color: rgba(255,255,255,0.05) !important; color: #d4d4d4 !important; border: 1px solid rgba(255,255,255,0.1) !important; border-radius: 9999px !important; padding: 6px 16px !important;">
                        @lang('All')
                    </button>
                </div>

                <!-- Straight Line Options List Container (Reference Image Layout) -->
                <div class="domain-card-box bg-[#141416] border border-white/10 rounded-2xl p-4 sm:p-6" style="background-color: #141416 !important; border: 1px solid rgba(255,255,255,0.1) !important; border-radius: 16px !important; padding: 24px !important;">
                    @php
                        $allSuggestions = array_merge($tldSuggestions, $variantSuggestions);
                        $discountPercentages = [89, 57, 18, 97, 50, 75, 65, 40, 80, 70];
                    @endphp

                    @foreach($allSuggestions as $idx => $item)
                        @php
                            $discount = $discountPercentages[$idx % count($discountPercentages)];
                            $origPrice = showAmount($item['pricing']['renew'] ?? ($item['pricing']['price'] * 1.8));
                        @endphp
                        <div class="tld-row-item py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3" style="padding: 16px 0 !important; border-bottom: 1px solid rgba(255,255,255,0.08) !important; display: flex !important; align-items: center !important; justify-content: space-between !important;">
                            <!-- Left: Domain Name + Discount Badge -->
                            <div class="flex items-center gap-3 flex-wrap" style="display: flex !important; align-items: center !important; gap: 12px !important;">
                                <span class="text-base font-bold font-sans text-white tracking-tight" style="color: #ffffff !important; font-size: 16px !important; font-weight: 700 !important;">{{ $item['domain'] }}</span>
                                <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-[11px] font-semibold" style="background-color: rgba(99,102,241,0.2) !important; color: #a5b4fc !important; border: 1px solid rgba(99,102,241,0.3) !important; border-radius: 9999px !important; padding: 2px 10px !important; font-size: 11px !important;">
                                    @lang('Save') {{ $discount }}%
                                </span>
                            </div>

                            <!-- Right: Pricing & Add to Cart Button -->
                            <div class="flex items-center gap-4 self-end sm:self-center" style="display: flex !important; align-items: center !important; gap: 16px !important;">
                                <div class="text-right" style="text-align: right !important;">
                                    <span class="text-xs text-neutral-400 line-through font-mono me-1.5" style="color: #a3a3a3 !important; text-decoration: line-through !important; font-size: 12px !important; margin-right: 6px !important;">{{ $origPrice }}</span>
                                    <span class="text-base font-bold text-white font-sans" style="color: #ffffff !important; font-size: 16px !important; font-weight: 700 !important;">{{ showAmount($item['pricing']['price']) }}<span class="text-xs font-normal text-neutral-400" style="color: #a3a3a3 !important; font-size: 12px !important;">/1st yr</span></span>
                                </div>
                                <form action="{{ route('shopping.cart.add.domain') }}" method="POST" class="inline" style="display: inline !important;">
                                    @csrf
                                    <input type="hidden" name="domain" value="{{ $item['domain'] }}">
                                    <input type="hidden" name="domain_setup_id" value="{{ @$item['pricing']['setup']->id }}">
                                    <button type="submit" class="btn-add-cart-pill px-5 py-2 bg-white/10 hover:bg-white hover:text-black border border-white/20 text-white font-semibold text-xs rounded-full transition-all">
                                        @lang('Add to cart')
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Full TLD Pricing Directory (Straight Line List Container) -->
        <div class="space-y-4 pt-4">
            <h3 class="text-xl font-bold text-white tracking-tight" style="color: #ffffff !important; font-size: 20px !important; font-weight: 700 !important;">@lang('All Domain Extensions & Rates')</h3>

            <div class="domain-card-box bg-[#141416] border border-white/10 rounded-2xl p-4 sm:p-6" style="background-color: #141416 !important; border: 1px solid rgba(255,255,255,0.1) !important; border-radius: 16px !important; padding: 24px !important;">
                @foreach($domainSetups as $setup)
                    @php
                        $pricing = $setup->pricing;
                        $firstPrice = $pricing ? $pricing->firstPrice : null;
                        $regPrice = isset($firstPrice['price']) ? $firstPrice['price'] : 12.99;
                        $renewPrice = $pricing && isset($pricing->one_year_renew) && $pricing->one_year_renew >= 0 ? $pricing->one_year_renew : $regPrice;
                    @endphp
                    <div class="tld-row-item py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3" style="padding: 14px 0 !important; border-bottom: 1px solid rgba(255,255,255,0.08) !important; display: flex !important; align-items: center !important; justify-content: space-between !important;">
                        <div class="flex items-center gap-3" style="display: flex !important; align-items: center !important; gap: 12px !important;">
                            <span class="text-base font-bold font-sans text-white tracking-tight" style="color: #ffffff !important; font-size: 16px !important; font-weight: 700 !important;">.{{ ltrim($setup->extension, '.') }}</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-medium" style="color: #34d399 !important;">
                                @lang('Free ID Protection')
                            </span>
                        </div>

                        <div class="flex items-center gap-4 self-end sm:self-center" style="display: flex !important; align-items: center !important; gap: 16px !important;">
                            <div class="text-right" style="text-align: right !important;">
                                <span class="text-xs text-neutral-400 me-2" style="color: #a3a3a3 !important; font-size: 12px !important; margin-right: 8px !important;">@lang('Renew'): {{ showAmount($renewPrice) }}/yr</span>
                                <span class="text-base font-bold text-emerald-400 font-sans" style="color: #34d399 !important; font-size: 16px !important; font-weight: 700 !important;">{{ showAmount($regPrice) }}<span class="text-xs font-normal text-neutral-400" style="color: #a3a3a3 !important; font-size: 12px !important;">/1st yr</span></span>
                            </div>
                            <a href="{{ route('register.domain') }}?domain=mybrand.{{ ltrim($setup->extension, '.') }}" class="btn-add-cart-pill px-4 py-1.5 bg-white/5 hover:bg-white/15 border border-white/10 text-neutral-200 hover:text-white font-semibold text-xs rounded-full transition-all">
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
                $('.tld-filter-tab').removeClass('bg-white text-black font-semibold').addClass('bg-white/5 text-neutral-300 font-medium');
                $(this).removeClass('bg-white/5 text-neutral-300 font-medium').addClass('bg-white text-black font-semibold');
            });
        })(jQuery);
    </script>
@endpush
on

