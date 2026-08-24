@php
    $count = App\Models\ShoppingCart::where('user_id', @auth()->user()->id ?? session()->get('randomId'))->count();
@endphp
<a class="add-cart whm-cart-link" href="{{ route('shopping.cart') }}">
    <i data-lucide="shopping-cart"></i>
    <span>{{ $count }}</span>
</a>
