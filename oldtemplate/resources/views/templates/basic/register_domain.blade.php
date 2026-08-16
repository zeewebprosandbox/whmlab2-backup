@extends($activeTemplate . 'layouts.side_bar')

@section('data')
    <div class="col-lg-12">

        <!-- Domain Search Header Card (Native oldtemplate theme) -->
        <div class="card custom--card mb-4">
            <div class="card-header bg--base text-white">
                <h5 class="card-title text-white m-0">
                    <i class="las la-search me-1"></i> @lang('Search & Register Domain')
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('register.domain') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="domain" value="{{ $searchDomain }}" class="form-control form--control" placeholder="@lang('Search your domain name or keyword (e.g. mybrand.com)')" required>
                        <button type="submit" class="btn btn--base">
                            <i class="las la-search"></i> @lang('Search Domain')
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($primaryResult)
            <!-- Primary Search Result (Native Theme Styling) -->
            <div class="card custom--card mb-4">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h4 class="mb-1 text--base font-mono">{{ $primaryResult['domain'] }}</h4>
                            @if($primaryResult['available'])
                                <span class="badge badge--success">
                                    <i class="las la-check-circle me-1"></i> @lang('Available Now')
                                </span>
                            @else
                                <span class="badge badge--danger">
                                    <i class="las la-times-circle me-1"></i> @lang('Already Registered')
                                </span>
                            @endif
                        </div>

                        @if($primaryResult['available'])
                            <div class="d-flex align-items-center gap-3">
                                <div class="text-end">
                                    <h4 class="mb-0 text--success font-mono">{{ showAmount($primaryResult['pricing']['price']) }}</h4>
                                    <small class="text-muted">@lang('Renew'): {{ showAmount($primaryResult['pricing']['renew']) }}/yr</small>
                                </div>
                                <form action="{{ route('shopping.cart.add.domain') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="domain" value="{{ $primaryResult['domain'] }}">
                                    <input type="hidden" name="domain_setup_id" value="{{ @$primaryResult['pricing']['setup']->id }}">
                                    <button type="submit" class="btn btn--base btn-sm">
                                        <i class="las la-shopping-cart me-1"></i> @lang('Add to Cart')
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Smart Domain Suggestions (Native Theme List) -->
            @if(count($tldSuggestions) > 0 || count($variantSuggestions) > 0)
                <div class="card custom--card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title m-0">
                            <i class="las la-lightbulb me-1"></i> @lang('Suggested Domain Names')
                        </h5>
                        <span class="badge badge--primary">{{ count($tldSuggestions) + count($variantSuggestions) }} @lang('Options')</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table custom--table mb-0">
                                <thead>
                                    <tr>
                                        <th>@lang('Domain Name')</th>
                                        <th>@lang('Status')</th>
                                        <th>@lang('Registration Price')</th>
                                        <th>@lang('Renewal Price')</th>
                                        <th class="text-end">@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(array_merge($tldSuggestions, $variantSuggestions) as $item)
                                        <tr>
                                            <td class="fw-bold font-mono">{{ $item['domain'] }}</td>
                                            <td>
                                                <span class="badge badge--success">@lang('Available')</span>
                                            </td>
                                            <td class="text--success fw-bold font-mono">{{ showAmount($item['pricing']['price']) }}</td>
                                            <td class="font-mono">{{ showAmount($item['pricing']['renew']) }}</td>
                                            <td class="text-end">
                                                <form action="{{ route('shopping.cart.add.domain') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="domain" value="{{ $item['domain'] }}">
                                                    <input type="hidden" name="domain_setup_id" value="{{ @$item['pricing']['setup']->id }}">
                                                    <button type="submit" class="btn btn-outline--base btn-sm">
                                                        <i class="las la-cart-plus"></i> @lang('Add to Cart')
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- All Supported TLD Pricing Directory (Native Theme Table) -->
        <div class="card custom--card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0">
                    <i class="las la-list me-1"></i> @lang('All Domain Extensions & Pricing Catalog')
                </h5>
                <span class="badge badge--info">{{ count($domainSetups) }} @lang('TLDs')</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table custom--table mb-0">
                        <thead>
                            <tr>
                                <th>@lang('TLD Extension')</th>
                                <th>@lang('Registration Price (1yr)')</th>
                                <th>@lang('Renewal Price (1yr)')</th>
                                <th>@lang('ID Protection')</th>
                                <th class="text-end">@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($domainSetups as $setup)
                                @php
                                    $pricing = $setup->pricing;
                                    $firstPrice = $pricing ? $pricing->firstPrice : null;
                                    $regPrice = isset($firstPrice['price']) ? $firstPrice['price'] : 12.99;
                                    $renewPrice = $pricing && isset($pricing->one_year_renew) && $pricing->one_year_renew >= 0 ? $pricing->one_year_renew : $regPrice;
                                @endphp
                                <tr>
                                    <td class="fw-bold font-mono">.{{ ltrim($setup->extension, '.') }}</td>
                                    <td class="text--success fw-bold font-mono">{{ showAmount($regPrice) }}</td>
                                    <td class="font-mono">{{ showAmount($renewPrice) }}</td>
                                    <td>
                                        <span class="badge badge--success">@lang('Included')</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('register.domain') }}?domain=mybrand.{{ ltrim($setup->extension, '.') }}" class="btn btn-outline--base btn-sm">
                                            <i class="las la-search"></i> @lang('Search')
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection