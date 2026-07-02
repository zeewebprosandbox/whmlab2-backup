@extends($activeTemplate . 'layouts.side_bar')

@php
    $products = $serviceCategory->products($filter = true)->paginate(getPaginate());
@endphp

@section('data')
    <div class="col-lg-9">
        <div class="row gy-4 justify-content-center">

            <div class="col-lg-12">
                <div class="whm-store-page-head">
                    <div>
                        <span>@lang('Hosting catalog')</span>
                        <h3>{{ __($serviceCategory->name) }}</h3>
                        <p>{{ $serviceCategory->short_description }}</p>
                    </div>
                    <a href="{{ route('register.domain') }}" class="btn btn-outline-secondary">
                        <i data-lucide="search"></i> @lang('Search Domain')
                    </a>
                </div>
            </div>

            @forelse($products as $product)
                <div class="col-lg-4 col-md-6 col-sm-6">
                    @php
                        $price = $product->price;
                        $setup = pricing($product->payment_type, $price, $type = 'setupFee');
                        $features = collect(explode("\n", strip_tags($product->description)))->filter()->take(4);
                        $isPremium = str_contains(strtolower($product->name), 'pro') || str_contains(strtolower($product->name), 'business') || str_contains(strtolower($product->name), 'enterprise') || str_contains(strtolower($product->name), 'elite') || str_contains(strtolower($product->name), 'dedicated');
                    @endphp
                    <div class="card position-relative h-100 whm-store-plan-card">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <div class="whm-store-plan-top">
                                    <span><i data-lucide="server"></i></span>
                                    @if($isPremium)
                                        <b>@lang('Premium offer')</b>
                                    @else
                                        <b>@lang('Ready to order')</b>
                                    @endif
                                </div>
                                <h5 class="product-name">{{ __($product->name) }}</h5>

                                @if ($product->stock_control)
                                    <span class="fs-13 fw-bold prcing-availble ">{{ $product->stock_quantity }} @lang('Available')</span>
                                @endif

                                <div class="pricing whm-store-price">
                                    <div class="pricing-header">
                                        <h3 class="pricing-header__price">
                                            {{ gs('cur_sym') }}{{ pricing($product->payment_type, $price, $type = 'price') }} <span class="text">/ {{ __(gs('cur_text')) }}</span>
                                        </h3>
                                        <h5 class="pricing-header__time">
                                            {{ pricing($product->payment_type, $price, $type = 'price', $showText = true) }}
                                        </h5>
                                        <p class="pricing-header__setup">
                                            {{ gs('cur_sym') }}{{ $setup }}
                                            {{ pricing($product->payment_type, $price, $type = 'setupFee', $showText = true) }}
                                        </p>
                                    </div>

                                </div>

                                <ul class="whm-store-feature-list">
                                    @foreach($features as $feature)
                                        <li><i data-lucide="check"></i>{{ __($feature) }}</li>
                                    @endforeach
                                </ul>

                            </div>

                            <div class="text-lg-center mt-3">
                                <a href="{{ route('product.configure', ['categorySlug' => $serviceCategory->slug, 'productSlug' => $product->slug, 'id' => $product->id]) }}" class="btn btn--base btn--sm mt-2 w-100">
                                    <i data-lucide="shopping-bag"></i> @lang('Order Now')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-12 text-center">
                    <div class="alert alert-warning p-4 justify-content-center flex-wrap d-flex" role="alert">
                        @lang('No product available in this category')
                    </div>
                </div>
            @endforelse

            {{ paginateLinks($products) }}
        </div>
    </div>
@endsection
