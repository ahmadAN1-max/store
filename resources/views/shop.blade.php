{{-- resources/views/products.blade.php --}}

@extends('layouts.app')
@section('content')
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-WEN8v4oT+Vsm6+bt0xK8M32K5U6zOiFkk4B7C0gi0xKQ6AQWw9n5Y7kFv3vTOmxH" crossorigin="anonymous">

    <!-- JS Bundle (مع Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-6A6c0SlMnUyr2B4Hf8n2q+oXx6+n7sDRCmyF6y9VZyfIflpDRZMFJZ4EMHfDdC2Q" crossorigin="anonymous">
    </script>

    <style>
        .product-wrapper {
            position: relative;
            display: inline-block;
        }

        .out-of-stock-label {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(255, 0, 0, 0.8);
            color: #fff;
            padding: 5px 12px;
            font-weight: bold;
            border-radius: 5px;
            font-size: 14px;
            z-index: 10;
            text-transform: uppercase;
        }

        .pc__img {
            display: block;
            width: 100%;
            height: auto;
        }
    </style>
    <div class="container my-4">

        {{-- Filter Buttons --}}
        <div class="sizes-filter mb-4">
            <button class="btn btn-sm btn-outline-primary active" data-size="all">All</button>
            @php
                $allSizes = [];
                foreach ($products as $product) {
                    foreach ($product->children as $child) {
                        if (!in_array($child->sizes, $allSizes)) {
                            if ($child->quantity > 0) {
                                $allSizes[] = $child->sizes;
                            }
                        }
                    }
                }
            @endphp
            @foreach ($allSizes as $size)
                <button class="btn btn-sm btn-outline-primary" data-size="{{ $size }}">{{ $size }}</button>
            @endforeach
        </div>

        {{-- Products Grid --}}
        <div class="row g-3 product-card-wrapper">
            @foreach ($products as $product)
                
                    @php
                        $sizesString = $product->children->pluck('sizes')->implode(',');
                    @endphp
                    <div class="col-md-3 product-card" data-sizes="{{ $sizesString }}">
                        <div class=" h-100">

                            {{-- Product Images --}}
                            <div class="pc__img-wrapper">
                                <div class="swiper-container background-img js-swiper-slider"
                                    data-settings='{"resizeObserver": true}'>
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <a
                                                href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}">
                                                <img loading="lazy" src="{{ asset('uploads/products/' . $product->image) }}"
                                                    width="330" height="400" alt="{{ $product->name }}"
                                                    class="pc__img">
                                            </a>
                                            @if ($product->quantity == 0)
                                                <span class="out-of-stock-label">Out of Stock</span>
                                            @endif
                                        </div>
                                        <div class="swiper-slide">
                                            @foreach (explode(',', $product->images) as $gimg)
                                                <a
                                                    href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}">
                                                    <img loading="lazy"
                                                        src="{{ asset('uploads/products/thumbnails/' . trim($gimg)) }}"
                                                        width="330" height="400" alt="{{ $product->name }}"
                                                        class="pc__img">
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    <span class="pc__img-prev"><svg width="7" height="11" viewBox="0 0 7 11"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <use href="#icon_prev_sm" />
                                        </svg></span>
                                    <span class="pc__img-next"><svg width="7" height="11" viewBox="0 0 7 11"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <use href="#icon_next_sm" />
                                        </svg></span>
                                </div>

                                {{-- Add to Cart Button --}}
                                @if (Cart::instance('cart')->content()->where('id', $product->id)->count() > 0)
                                    <a href="{{ route('cart.index') }}"
                                        class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium js-add-cart btn-warning">Go
                                        to Cart</a>
                                @else
                                    <a href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}"
                                        class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium js-add-cart btn-warning">Add
                                        to Cart</a>
                                @endif
                            </div>

                            {{-- Product Info --}}
                            <div class="pc__info position-relative">
                                <p class="pc__category">
                                    {{ $product->categories->isNotEmpty() ? $product->categories->first()->name : 'No Category' }}
                                </p>
                                <h6 class="pc__title"><a
                                        href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}">{{ $product->name }}</a>
                                </h6>

                                {{-- Sizes --}}
                                @if ($product->children->count() > 0)
                                    <div class="mt-2">
                                        <label class="fw-bold text-muted small mb-1 d-block">Available Sizes:</label>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($product->children as $child)
                                                @php $available = $child->quantity > 0; @endphp
                                                <span class="badge rounded-pill fw-semibold text-center"
                                                    style="
                                                      font-size: 0.65rem;
                                                      padding: 3px 10px;
                                                      min-width: 42px;
                                                      line-height: 1.2;
                                                      background-color: {{ $available ? '#5c84ffff' : '#6c757d' }};
                                                      color: white;
                                                      border: 1px solid {{ $available ? '#5c84ffff' : '#5a6268' }};
                                                      {{ !$available ? 'text-decoration: line-through; opacity: 0.45;' : '' }}"
                                                    title="{{ $available ? 'In stock: ' . $child->quantity : 'Out of stock' }}">
                                                    {{ $child->sizes ?? 'Size ?' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Price --}}
                                <div class="product-card__price d-flex">
                                    <span class="money price">
                                        @if ($product->sale_price)
                                            <s>${{ $product->regular_price }}</s> ${{ $product->sale_price }}
                                            {{ round((($product->regular_price - $product->sale_price) * 100) / $product->regular_price) }}%
                                            OFF
                                        @else
                                            ${{ $product->regular_price }}
                                        @endif
                                    </span>
                                </div>

                                {{-- Wishlist --}}
                                @if (Cart::instance('wishlist')->content()->where('id', $product->id)->count() > 0)
                                    <form method="POST"
                                        action="{{ route('wishlist.remove', ['rowId' => Cart::instance('wishlist')->content()->where('id', $product->id)->first()->rowId]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="pc__btn-wl position-absolute top-0 end-0 bg-transparent border-0 filled-heart"
                                            title="Remove from Wishlist">
                                            <svg width="16" height="16" viewBox="0 0 20 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <use href="#icon_heart" />
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('wishlist.add') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $product->id }}">
                                        <input type="hidden" name="name" value="{{ $product->name }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <input type="hidden" name="price"
                                            value="{{ $product->sale_price == '' ? $product->regular_price : $product->sale_price }}">
                                        <button type="submit"
                                            class="pc__btn-wl position-absolute top-0 end-0 bg-transparent border-0"
                                            title="Add To Wishlist">
                                            <svg width="16" height="16" viewBox="0 0 20 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <use href="#icon_heart" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif

                            </div>
                        </div>
                    </div>
                
            @endforeach
        </div>

    </div>

    {{-- Filter Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.sizes-filter button');
            const productCards = document.querySelectorAll('.product-card');

            buttons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const selectedSize = this.dataset.size;

                    // Update active button
                    buttons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    // Filter products
                    productCards.forEach(card => {
                        const sizes = card.dataset.sizes.split(',');
                        card.style.display = (selectedSize === 'all' || sizes.includes(
                            selectedSize)) ? '' : 'none';
                    });
                });
            });
        });
    </script>

@endsection
