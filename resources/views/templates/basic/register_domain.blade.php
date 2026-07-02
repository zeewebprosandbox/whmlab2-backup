@extends($activeTemplate . 'layouts.side_bar')

@php 
    $getResponse = collect(@$result['data']);
@endphp

@section('data')
    <div class="col-lg-9">
        <div class="row gy-4">
            <div class="col-lg-12">
                <div class="whm-store-page-head whm-domain-page-head">
                    <div>
                        <span>@lang('Domain search')</span>
                        <h3>@lang('Find the domain that fits your next service.')</h3>
                        <p>@lang('Search a name, compare supported extensions, and add available domains directly to your cart.')</p>
                    </div>
                    <a href="{{ route('shopping.cart') }}" class="btn btn-outline-secondary">
                        <i data-lucide="shopping-cart"></i> @lang('View Cart')
                    </a>
                </div>
            </div>
            <div class="col-lg-12 text-center whm-domain-search-panel">
                @include($activeTemplate . 'partials.domain_search_form')
            </div>

            <div class="col-lg-12 text-center">
                @if($getResponse->where('domain', @$result['domain'])->where('available', true)->count())
                    <h3>
                        @lang('Congratulations')! <span class="text--base">{{ @$result['domain'] }} @lang('is')</span> @lang('available')!
                    </h3>
                @elseif($getResponse->where('domain', @$result['domain'])->where('available', false)->count())
                    <h3>
                        <span class="text--danger">{{ @$result['domain'] }} @lang('is')</span> @lang('unavailable')!
                    </h3>
                @endif
            </div>

            <div class="col-lg-12 text-center">
                @if(!@$result['isSupported'] && @$result['domain'])
                    <h3>@lang('We are not supporting ') <span class="text--warning">({{ @$result['tld'] }})</span> @lang('right now')</h3>
                @endif 
            </div>

            <div class="col-12">
                @foreach($getResponse->sortByDesc('match') as $data)  
                    <div class="domain-row whm-domain-result-row">
                        <span>{{ @$data['domain'] }}</span>
                        <div class="text-end">
                            @if(@$data['available'])
                                <small class="whm-domain-status whm-domain-status--available">@lang('Available')</small>
                                <strong class="fw-bold text-end">
                                    {{ showAmount(@$data['setup']->pricing->firstPrice['price'] ?? 0) }}
                                </strong>
                                <form action="{{ route('shopping.cart.add.domain') }}" method="post" class="d-inline ms-2">
                                    @csrf
                                    <input type="hidden" name="domain" required value="{{ @$data['domain'] }} ">

                                    <input type="hidden" name="domain_setup_id" required value="{{ @$data['setup']->id }}">
                                    <button class="btn btn--sm btn--base{{ @$data['domain'] != $result['domain'] ? '-outline' : null }}">
                                        <i class="la la-cart-plus"></i> @lang('Add')
                                    </button>
                                </form>
                            @else
                                <small class="whm-domain-status whm-domain-status--unavailable">@lang('Unavailable')</small>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
