@php
    use Illuminate\Support\Str;
@endphp
@extends('layouts.app')

@section('content')
    <main>

        <section class="swiper-container js-swiper-slider swiper-number-pagination slideshow"
            data-settings='{
                             "autoplay": {
                             "delay": 5000
                                },
                            "slidesPerView": 1,
                            "effect": "fade",
                            "loop": true
                                }'>
            <div class="swiper-wrapper" style="margin-top:-5%">
                <div class="swiper-slide">
                    <div class="overflow-hidden position-relative h-100">
                        <div class="slideshow-character position-absolute bottom-0 pos_right-center">
                            <img loading="lazy" src="{{ asset('images/home/slider1.png') }}" alt="Woman Fashion 1"
                                class="slideshow-character__img animate animate_fade animate_btt animate_delay-9 " />
                            <div class="character_markup type2">
                                <p
                                    class="text-uppercase font-sofia mark-grey-color animate animate_fade animate_btt animate_delay-10 mb-0">
                                    Sarah's Palace</p>
                            </div>
                        </div>
                        <div class="slideshow-text container position-absolute start-50 top-50 translate-middle">
                            <h6
                                class="text_dash text-uppercase fs-base fw-medium animate animate_fade animate_btt animate_delay-3">
                                New Collection</h6>
                            <h2 class="h1 fw-normal mb-0 animate animate_fade animate_btt animate_delay-5">Fall-Winter
                            </h2>
                            <h2 class="h1 fw-bold animate animate_fade animate_btt animate_delay-5">2025/2026</h2>
                            <a href="{{ route('shop.byCategorySlug', ['slug' => Str::slug('new-collection')]) }}"
                                class="btn-link btn-link_lg default-underline fw-medium animate animate_fade animate_btt animate_delay-7">Shop
                                Now</a>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="overflow-hidden position-relative h-100">
                        <div class="slideshow-character position-absolute bottom-0 pos_right-center">
                            <img loading="lazy" src="{{ asset('images/home/slider2.png') }}" width="400" height="733"
                                alt=""
                                class="slideshow-character__img animate animate_fade animate_btt animate_delay-9 w-auto h-auto" />

                        </div>
                        <div class="slideshow-text container position-absolute start-50 top-50 translate-middle">
                            <h6
                                class="text_dash text-uppercase fs-base fw-medium animate animate_fade animate_btt animate_delay-3">
                                Fall-Winter 2025/2026</h6>
                            <h2 class="h1 fw-normal mb-0 animate animate_fade animate_btt animate_delay-5">First Moments
                                Collection </h2>
                            <h2 class="h1 fw-bold animate animate_fade animate_btt animate_delay-5">Newborn Essentials </h2>
                            <a href="{{ route('shop.byCategorySlug', ['slug' => Str::slug('NewBorn Boy (0-0 -->6-9)')]) }}"
                                style="color:blue;"
                                class="btn-link btn-link_lg default-underline fw-medium animate animate_fade animate_btt animate_delay-7">Shop
                                Now - Little Prince</a>
                            <p> </p>
                            <a href="{{ route('shop.byCategorySlug', ['slug' => Str::slug('NewBorn Girl (0-0 -->6-9)')]) }}"
                                style="color:#ff69b4"
                                class="btn-link btn-link_lg default-underline fw-medium animate animate_fade animate_btt animate_delay-7">Shop
                                Now - Little Princess</a>

                        </div>
                    </div>
                </div>

                <div class="swiper-slide">
                    <div class="overflow-hidden position-relative h-100">
                        <div class="slideshow-character position-absolute bottom-0 pos_right-center">
                            <img loading="lazy" src="{{ asset('images/home/slider3.png') }}" width="800" height="990"
                                alt="Woman Fashion 2"
                                class="slideshow-character__img animate animate_fade animate_rtl animate_delay-10 w-auto h-auto" />
                        </div>
                        <div class="slideshow-text container position-absolute start-50 top-50 translate-middle">
                            <h6
                                class="text_dash text-uppercase fs-base fw-medium animate animate_fade animate_btt animate_delay-3">
                                Exclusive on Website</h6>
                            <h2 class="h1 fw-normal mb-0 animate animate_fade animate_btt animate_delay-5">Limited to Our
                            </h2>
                            <h2 class="h1 fw-bold animate animate_fade animate_btt animate_delay-5">Website</h2>
                            <a href="{{ route('shop.byCategorySlug', ['slug' => Str::slug('Exclusive Website')]) }}"
                                class="btn-link btn-link_lg default-underline fw-medium animate animate_fade animate_btt animate_delay-7">Shop
                                Now</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <div
                    class="slideshow-pagination slideshow-number-pagination d-flex align-items-center position-absolute bottom-0 mb-5">
                </div>
            </div>
        </section>

        <section>

        </section>

        <div class="mb-3 mb-xl-5 pt-1 pb-4"></div>
        <div class="container mw-1620 bg-white border-radius-10">
            <section class="category-carousel container">
                <h2 class="section-title text-center mb-3 pb-xl-2 mb-xl-4">Explore by Category</h2>

                <div class="position-relative">
                    <div class="swiper-container js-swiper-slider"
                        data-settings='{
                                    "autoplay": {
                                        "delay": 5000
                                    },
                                    "slidesPerView": 8,
                                    "slidesPerGroup": 1,
                                    "effect": "none",
                                    "loop": true,
                                    "navigation": {
                                        "nextEl": ".products-carousel__next-1",
                                        "prevEl": ".products-carousel__prev-1"
                                    },
                                    "breakpoints": {
                                        "320": {
                                        "slidesPerView": 2,
                                        "slidesPerGroup": 2,
                                        "spaceBetween": 15
                                        },
                                        "768": {
                                        "slidesPerView": 4,
                                        "slidesPerGroup": 4,
                                        "spaceBetween": 30
                                        },
                                        "992": {
                                        "slidesPerView": 6,
                                        "slidesPerGroup": 1,
                                        "spaceBetween": 45,
                                        "pagination": false
                                        },
                                        "1200": {
                                        "slidesPerView": 8,
                                        "slidesPerGroup": 1,
                                        "spaceBetween": 60,
                                        "pagination": false
                                        }
                                    }
                                }'>
                        <div class="swiper-wrapper">
    @foreach ($categories as $category)
        @php
            // استخدم Regex لفصل الاسم عن الأرقام بين الأقواس
            preg_match('/^(.*?)\s*(\(.+\))?$/', $category->name, $matches);
            $namePart = $matches[1] ?? $category->name;
            $numberPart = $matches[2] ?? '';
        @endphp

        <div class="swiper-slide">
            <a href="{{ route('shop.byCategorySlug', $category->slug) }}">
                <img loading="lazy" class=""
                    src="{{ asset('uploads/categories/thumbnails/' . $category->image) }}"
                    alt="" style=" border-radius: 50%;" />
            </a>
            <div class="text-center">
                <a href="{{ route('shop.byCategorySlug', $category->slug) }}" class="menu-link fw-medium">
                    {{ $namePart }}<br>
                    <small>{{ $numberPart }}</small>
                </a>
            </div>
        </div>
    @endforeach
</div><!-- /.swiper-wrapper -->

                    </div><!-- /.swiper-container js-swiper-slider -->

                    <div
                        class="products-carousel__prev products-carousel__prev-1 position-absolute top-50 d-flex align-items-center justify-content-center">
                        <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_prev_md" />
                        </svg>
                    </div><!-- /.products-carousel__prev -->
                    <div
                        class="products-carousel__next products-carousel__next-1 position-absolute top-50 d-flex align-items-center justify-content-center">
                        <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                            <use href="#icon_next_md" />
                        </svg>
                    </div><!-- /.products-carousel__next -->
                </div><!-- /.position-relative -->
            </section>

            <div class="mb-3 mb-xl-5 pt-1 pb-4"></div>

            <section class="hot-deals container">
                <h2 class="section-title text-center mb-3 pb-xl-3 mb-xl-4">Sales Collection</h2>
                <div class="row">
                    <div
                        class="col-md-6 col-lg-4 col-xl-20per d-flex align-items-center flex-column justify-content-center py-4 align-items-md-start">
                        <h2>Flash Sale Frenzy,</h2>
                        <h2 class="fw-bold">Grab It Fast!</h2>



                        <a href="{{ route('shop.byCategorySlug', ['slug' => Str::slug('sale')]) }}"
                            class="btn-link default-underline text-uppercase fw-medium mt-3">View All</a>
                    </div>
                    <div class="col-md-6 col-lg-8 col-xl-80per">
                        <div class="position-relative">
                            <div class="swiper-container js-swiper-slider"
                                data-settings='{
                                                        "autoplay": {
                                                        "delay": 5000
                                                    },
                                                    "slidesPerView": 4,
                                                    "slidesPerGroup": 4,
                                                    "effect": "none",
                                                    "loop": false,
                                                    "breakpoints": {
                                                        "320": {
                                                        "slidesPerView": 2,
                                                        "slidesPerGroup": 2,
                                                        "spaceBetween": 14
                                                        },
                                                        "768": {
                                                        "slidesPerView": 2,
                                                        "slidesPerGroup": 3,
                                                        "spaceBetween": 24
                                                        },
                                                        "992": {
                                                        "slidesPerView": 3,
                                                        "slidesPerGroup": 1,
                                                        "spaceBetween": 30,
                                                        "pagination": false
                                                        },
                                                        "1200": {
                                                        "slidesPerView": 4,
                                                        "slidesPerGroup": 1,
                                                        "spaceBetween": 30,
                                                        "pagination": false
                                                        }
                                                    }
                                                    }'>
                                <div class="swiper-wrapper">
                                    @foreach ($saleProducts as $saleProduct)
                                        <div class="swiper-slide product-card product-card_style3">
                                            <div class="pc__img-wrapper">
                                                <a
                                                    href="{{ route('shop.product.details', ['product_slug' => $saleProduct->slug]) }}">
                                                    <img loading="lazy"
                                                        src="{{ asset('uploads/products/' . $saleProduct->image) }}"
                                                        width="258" height="313" alt="" class="pc__img">
                                                    @foreach (explode(',', $saleProduct->images) as $gimg)
                                                        <img loading="lazy"
                                                            src="{{ asset('uploads/products/' . trim($gimg)) }}"
                                                            width="258" height="313" alt=""
                                                            class="pc__img pc__img-second">
                                                    @endforeach
                                                </a>
                                            </div>

                                            <div class="pc__info position-relative">
                                                <h6 class="pc__title"><a href="details.html">{{ $saleProduct->name }}</a>
                                                </h6>
                                                <div class="product-card__price d-flex">
                                                    <span
                                                        class="money price text-secondary">${{ $saleProduct->sale_price == '' ? $saleProduct->regular_price : $saleProduct->sale_price }}</span>
                                                </div>

                                                <div
                                                    class="anim_appear-bottom position-absolute bottom-0 start-0 d-none d-sm-flex align-items-center bg-body">
                                                    <button
                                                        class="btn-link btn-link_lg me-4 text-uppercase fw-medium js-add-cart js-open-aside"
                                                        data-aside="cartDrawer" title="Add To Cart">Add To Cart</button>
                                                    <button
                                                        class="btn-link btn-link_lg me-4 text-uppercase fw-medium js-quick-view"
                                                        data-bs-toggle="modal" data-bs-target="#quickView"
                                                        title="Quick view">
                                                        <span class="d-none d-xxl-block">Quick View</span>
                                                        <span class="d-block d-xxl-none"><svg width="18"
                                                                height="18" viewBox="0 0 18 18" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <use href="#icon_view" />
                                                            </svg></span>
                                                    </button>
                                                    <button class="pc__btn-wl bg-transparent border-0 js-add-wishlist"
                                                        title="Add To Wishlist">
                                                        <svg width="16" height="16" viewBox="0 0 20 20"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <use href="#icon_heart" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach


                                </div><!-- /.swiper-wrapper -->
                            </div><!-- /.swiper-container js-swiper-slider -->
                        </div><!-- /.position-relative -->
                    </div>
                </div>
            </section>

            <div class="mb-3 mb-xl-5 pt-1 pb-4"></div>
            <style>
                .circle-img {
                    width: 100%;
                    /* يأخذ كامل عرض العمود */
                    max-width: 450px;
                    /* أقصى حجم على الديسكتوب */
                    aspect-ratio: 1/1;
                    /* يحافظ على دائري */
                    border-radius: 50%;
                    object-fit: cover;
                    /* يغطي المساحة بدون تشويه */
                    display: block;
                    margin: 0 auto;
                }

                /* Responsive للموبايل */
                @media (max-width: 768px) {
                    .circle-img {
                        max-width: 250px;
                        /* حجم أصغر على الموبايل */
                    }
                }
            </style>
            <section class="category-banner container my-5">
                <div class="row g-4 justify-content-center">

                    <!-- أول صورة -->
                    <div class="col-12 col-md-5 text-center">
                        <div class="category-banner__item mb-5">
                            <img loading="lazy" src="{{ asset('images/home/new collection.jpg') }}" alt="New Collection"
                                class="circle-img" />

                            <div class="category-banner__item-content mt-3 p-2"
                                style="border: 1px solid black; border-radius: 50%;">
                                <h3 class="mb-0">Accessories</h3>
                                <a href="{{ route('shop.byCategorySlug', ['slug' => Str::slug('Accessories')]) }}"
                                    class="btn-link text-uppercase fw-medium">
                                    Shop Now
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- ثاني صورة -->
                    <div class="col-12 col-md-5 text-center">
                        <div class="category-banner__item mb-5 position-relative">
                            <img loading="lazy" src="{{ asset('images/home/daily deals.jpg') }}" alt="Sale Collection"
                                class="circle-img" />

                            {{-- <div class="category-banner__item-mark position-absolute top-0 start-50 translate-middle-x badge bg-danger">
                    Hot Deals
                </div> --}}

                            <div class="category-banner__item-content mt-3 p-2"
                                style="border: 1px solid black; border-radius: 50%;">
                                <h3 class="mb-0">Special Deals</h3>
                                <a href="{{ route('shop.byCategorySlug', ['slug' => Str::slug('sale')]) }}"
                                    class="btn-link text-uppercase fw-medium">
                                    Shop Now
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </section>


            <div class="mb-3 mb-xl-5 pt-1 pb-4"></div>

            {{-- featured products ....
            <section class="products-grid container">
                <h2 class="section-title text-center mb-3 pb-xl-3 mb-xl-4">Featured Products</h2>

                <div class="row">
                    @foreach ($featuredProducts as $fproduct)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="product-card product-card_style3 mb-3 mb-md-4 mb-xxl-5">
                                <div class="pc__img-wrapper">
                                    <a href="{{ route('shop.product.details', ['product_slug' => $fproduct->slug]) }}">
                                        <img loading="lazy" src="{{ asset('uploads/products/' . $fproduct->image) }}"
                                            width="330" height="400" alt="" class="pc__img">
                                    </a>
                                </div>

                                <div class="pc__info position-relative">
                                    <h6 class="pc__title"><a
                                            href="{{ route('shop.product.details', ['product_slug' => $fproduct->slug]) }}">{{ $fproduct->name }}</a>
                                    </h6>
                                    <div class="product-card__price d-flex align-items-center">
                                        <span
                                            class="money price text-secondary">${{ $fproduct->sale_price == '' ? $fproduct->regular_price : $fproduct->sale_price }}</span>
                                    </div>

                                    <div
                                        class="anim_appear-bottom position-absolute bottom-0 start-0 d-none d-sm-flex align-items-center bg-body">
                                        <button
                                            class="btn-link btn-link_lg me-4 text-uppercase fw-medium js-add-cart js-open-aside"
                                            data-aside="cartDrawer" title="Add To Cart">Add To Cart</button>
                                        <button class="btn-link btn-link_lg me-4 text-uppercase fw-medium js-quick-view"
                                            data-bs-toggle="modal" data-bs-target="#quickView" title="Quick view">
                                            <span class="d-none d-xxl-block">Quick View</span>
                                            <span class="d-block d-xxl-none"><svg width="18" height="18"
                                                    viewBox="0 0 18 18" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <use href="#icon_view" />
                                                </svg></span>
                                        </button>
                                        <button class="pc__btn-wl bg-transparent border-0 js-add-wishlist"
                                            title="Add To Wishlist">
                                            <svg width="16" height="16" viewBox="0 0 20 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <use href="#icon_heart" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div><!-- /.row -->
            </section> --}}
        </div>

        <div class="mb-3 mb-xl-5 pt-1 pb-4"></div>

        <section class="category-carousel container">
            <h2 class="section-title text-center mb-3 pb-xl-2 mb-xl-4">Shop by Brand</h2>

            <div class="position-relative">
                <div class="swiper-container js-swiper-slider"
                    data-settings='{
                                    "autoplay": {
                                        "delay": 5000
                                    },
                                    "slidesPerView": 8,
                                    "slidesPerGroup": 1,
                                    "effect": "none",
                                    "loop": true,
                                    "navigation": {
                                        "nextEl": ".products-carousel__next-1",
                                        "prevEl": ".products-carousel__prev-1"
                                    },
                                    "breakpoints": {
                                        "320": {
                                        "slidesPerView": 2,
                                        "slidesPerGroup": 2,
                                        "spaceBetween": 15
                                        },
                                        "768": {
                                        "slidesPerView": 4,
                                        "slidesPerGroup": 4,
                                        "spaceBetween": 30
                                        },
                                        "992": {
                                        "slidesPerView": 6,
                                        "slidesPerGroup": 1,
                                        "spaceBetween": 45,
                                        "pagination": false
                                        },
                                        "1200": {
                                        "slidesPerView": 8,
                                        "slidesPerGroup": 1,
                                        "spaceBetween": 60,
                                        "pagination": false
                                        }
                                    }
                                }'>
                    <div class="swiper-wrapper">
                        @foreach ($brands as $brand)
                            <div class="swiper-slide">
                                <a href="{{ route('shop.index', ['brand' => $brand->slug]) }}">
                                    <img loading="lazy" class="w-100 h-auto mb-3"
                                        src="{{ asset('uploads/brands/thumbnails/' . $brand->image) }}" width="124"
                                        height="124" alt="{{ $brand->name }}" style="border-radius: 50%;" />
                                </a>
                                <div class="text-center">
                                    <a href="{{ route('shop.index', ['brand' => $brand->slug]) }}"
                                        class="menu-link fw-medium">{{ $brand->name }}</a>
                                </div>
                            </div>
                        @endforeach

                    </div><!-- /.swiper-wrapper -->
                </div><!-- /.swiper-container js-swiper-slider -->

                <div
                    class="products-carousel__prev products-carousel__prev-1 position-absolute top-50 d-flex align-items-center justify-content-center">
                    <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_prev_md" />
                    </svg>
                </div><!-- /.products-carousel__prev -->
                <div
                    class="products-carousel__next products-carousel__next-1 position-absolute top-50 d-flex align-items-center justify-content-center">
                    <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                        <use href="#icon_next_md" />
                    </svg>
                </div><!-- /.products-carousel__next -->
            </div><!-- /.position-relative -->
        </section>
        <div class="mb-3 mb-xl-5 pt-1 pb-4"></div>
    </main>
@endsection
