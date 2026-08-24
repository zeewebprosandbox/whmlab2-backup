@extends($activeTemplate . 'layouts.side_bar')

@php
    $products = $serviceCategory->products($filter = true)->paginate(getPaginate());
    $allCategories = App\Models\ServiceCategory::active()->get();
@endphp

@section('data')
    <div class="col-12 space-y-6">

        <!-- Category Nav Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none border-b border-slate-200">
            @foreach($allCategories as $cat)
                <a href="{{ route('service.category', $cat->slug) }}" class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all {{ $cat->id == $serviceCategory->id ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white hover:bg-slate-50 border border-slate-200 text-slate-700' }}">
                    {{ __($cat->name) }}
                </a>
            @endforeach
        </div>

        <!-- Category Header Banner -->
        <div class="p-6 bg-white border border-slate-200/80 rounded-2xl shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-700">@lang('Hosting Catalog')</span>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display mt-0.5">{{ __($serviceCategory->name) }}</h2>
                <p class="text-xs text-slate-500 mt-1 max-w-xl">{{ $serviceCategory->short_description }}</p>
            </div>
            <a href="{{ route('register.domain') }}" class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-2 shadow-xs self-start sm:self-auto">
                <i data-lucide="search" class="w-4 h-4 text-indigo-600"></i>
                <span>@lang('Search Domain')</span>
            </a>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($products as $product)
                @php
                    $price = $product->price;
                    $setup = pricing($product->payment_type, $price, $type = 'setupFee');
                    $features = collect(explode("\n", strip_tags($product->description)))->filter()->take(5);
                    $isPremium = str_contains(strtolower($product->name), 'pro') || str_contains(strtolower($product->name), 'business') || str_contains(strtolower($product->name), 'enterprise') || str_contains(strtolower($product->name), 'elite') || str_contains(strtolower($product->name), 'dedicated');
                @endphp
                <div class="p-6 bg-white border border-slate-200/80 hover:border-indigo-300 rounded-2xl shadow-sm hover:shadow-md transition-all flex flex-col justify-between space-y-6 relative group">
                    @if($isPremium)
                        <span class="absolute top-4 right-4 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                            @lang('Premium Plan')
                        </span>
                    @endif

                    <div class="space-y-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 group-hover:scale-105 transition-transform">
                            <i data-lucide="server" class="w-5 h-5"></i>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-slate-900 font-display tracking-tight">{{ __($product->name) }}</h3>
                            <div class="mt-2 flex items-baseline gap-1">
                                <span class="text-2xl font-extrabold font-mono text-slate-900">{{ gs('cur_sym') }}{{ pricing($product->payment_type, $price, $type = 'price') }}</span>
                                <span class="text-xs text-slate-500 font-medium">/ {{ pricing($product->payment_type, $price, $type = 'price', $showText = true) }}</span>
                            </div>
                            @if ($setup > 0)
                                <div class="text-[11px] text-slate-400 mt-0.5">+ {{ gs('cur_sym') }}{{ $setup }} @lang('setup fee')</div>
                            @else
                                <div class="text-[11px] text-emerald-600 font-semibold mt-0.5">@lang('Free instant setup')</div>
                            @endif
                        </div>

                        <ul class="space-y-2 pt-2 border-t border-slate-100 text-xs text-slate-600">
                            @foreach($features as $feature)
                                <li class="flex items-center gap-2">
                                    <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0"></i>
                                    <span>{{ __($feature) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <a href="{{ route('product.configure', ['categorySlug' => $serviceCategory->slug, 'productSlug' => $product->slug, 'id' => $product->id]) }}" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-xs transition-all flex items-center justify-center gap-2">
                        <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                        <span>@lang('Configure & Order →')</span>
                    </a>
                </div>
            @empty
                <div class="col-span-full p-12 bg-white border border-dashed border-slate-200 rounded-2xl text-center space-y-3">
                    <i data-lucide="server-off" class="w-8 h-8 mx-auto text-slate-400"></i>
                    <p class="text-sm font-semibold text-slate-900">@lang('No products available in this category currently.')</p>
                </div>
            @endforelse
        </div>

        @if($products->hasPages())
            <div class="pt-4">
                {{ paginateLinks($products) }}
            </div>
        @endif
    </div>
@endsection
