@extends('layouts.app')
@section('content')
    <style>
        .cart-totals td {
            text-align: right;
        }

        .cart-total th,
        .cart-total td {
            color: green;
            font-weight: bold;
            font-size: 21px !important;
        }

        .qty-control__increase,
        .qty-control__reduce {
            background: transparent;
            border: none;
            font-weight: bold;
            font-size: 28px;
            color: #555;
            cursor: pointer;
            padding: 0 8px;
        }

        .qty-control__increase:hover,
        .qty-control__reduce:hover {
            background: transparent;
            border: none;
            color: black;
        }
    </style>

    <main class="pt-90">

        <div class="mb-4 pb-4"></div>
        <section class="shop-checkout container">
            <h2 class="page-title">Cart</h2>
            <div class="checkout-steps">
                <a href="javascript:void();" class="checkout-steps__item active">
                    <span class="checkout-steps__item-number">01</span>
                    <span class="checkout-steps__item-title">
                        <span>Shopping Bag</span>
                        <em>Manage Your Items List</em>
                    </span>
                </a>
                <a href="javascript:void();" class="checkout-steps__item">
                    <span class="checkout-steps__item-number">02</span>
                    <span class="checkout-steps__item-title">
                        <span>Shipping and Checkout</span>
                        <em>Checkout Your Items List</em>
                    </span>
                </a>
                <a href="javascript:void();" class="checkout-steps__item">
                    <span class="checkout-steps__item-number">03</span>
                    <span class="checkout-steps__item-title">
                        <span>Confirmation</span>
                        <em>Order Confirmation</em>
                    </span>
                </a>
            </div>
            <div class="shopping-cart">
                @if ($cartItems->count() > 0)
                    <div class="cart-table__wrapper">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th></th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cartItems as $cartItem)
                                    <tr>
                                        <td>
                                            <div class="shopping-cart__product-item">
                                                <img loading="lazy"
                                                    src="{{ asset('uploads/products/thumbnails/' . $cartItem->model->image) }}"
                                                    width="120" height="120" alt="" />
                                            </div>
                                        </td>
                                        <td>
                                            <div class="shopping-cart__product-item__detail">
                                                <h4>{{ $cartItem->name }}</h4>

                                                <ul class="shopping-cart__product-item__options">
                                                    <li>Size: {{ $cartItem->options->size }}</li>
                                                </ul>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="shopping-cart__product-price">${{ $cartItem->price }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $productQty = \App\Models\Product::where('parent_id', $cartItem->id)
                                                    ->where('sizes', $cartItem->options->size)
                                                    ->value('quantity');
                                            @endphp

                                            <div class="qty-control position-relative">
                                                <input type="number" name="quantity" id="qty-input-{{ $cartItem->rowId }}"
                                                    value="{{ $cartItem->qty }}" min="1" max="{{ $productQty }}"
                                                    class="qty-control__number text-center">

                                                <form method="POST"
                                                    action="{{ route('cart.reduce.qty', ['rowId' => $cartItem->rowId]) }}">
                                                    @csrf @method('PUT')
                                                    <button type="submit"
                                                        class="qty-control__reduce btn btn-light">-</button>
                                                </form>

                                                <form method="POST"
                                                    action="{{ route('cart.increase.qty', ['rowId' => $cartItem->rowId]) }}">
                                                    @csrf @method('PUT')
                                                    <button type="submit"
                                                        class="qty-control__increase btn btn-light">+</button>
                                                </form>
                                            </div>


                                        </td>
                                        <td>
                                            <span class="shopping-cart__subtotal">${{ $cartItem->subTotal() }}</span>
                                        </td>
                                        <td>
                                            <form method="POST"
                                                action="{{ route('cart.remove', ['rowId' => $cartItem->rowId]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="remove-cart btn btn-link p-0 border-0 bg-transparent"
                                                    style="cursor:pointer;">
                                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="#767676"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M0.259435 8.85506L9.11449 0L10 0.885506L1.14494 9.74056L0.259435 8.85506Z" />
                                                        <path
                                                            d="M0.885506 0.0889838L9.74057 8.94404L8.85506 9.82955L0 0.97449L0.885506 0.0889838Z" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="cart-table-footer">
                            @if (!Session::has('coupon'))
                                <form class="position-relative bg-body" method="POST"
                                    action="{{ route('cart.coupon.apply') }}">
                                    @csrf
                                    <input class="form-control" type="text" name="coupon_code" placeholder="Coupon Code">
                                    <input class="btn-link fw-medium position-absolute top-0 end-0 h-100 px-4"
                                        type="submit" value="APPLY COUPON">
                                </form>
                            @else
                                <form class="position-relative bg-body" method="POST"
                                    action="{{ route('cart.coupon.remove') }}">
                                    @csrf
                                    @method('DELETE')
                                    <input class="form-control text-success fw-bold" type="text" name="coupon_code"
                                        placeholder="Coupon Code" value="{{ session()->get('coupon')['code'] }} Applied!"
                                        readonly>
                                    <input class="btn-link fw-medium position-absolute top-0 end-0 h-100 px-4 text-danger"
                                        type="submit" value="REMOVE COUPON">
                                </form>
                            @endif
                            <form class="position-relative bg-body" method="POST" action="{{ route('cart.empty') }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-light" type="submit">CLEAR CART</button>
                            </form>
                        </div>

                    </div>
                    @php
                        // قيمة الشحن الافتراضية
                        $deliveryCharge = \App\Models\Setting::where('key', 'delivery_charge')->value('value') ?? 0;

                        // جلب جميع الـ product_ids الموجودة في الكارت
                        $productIds = $cartItems->pluck('id')->toArray();

                        // جلب المنتجات مع الفئات
                        $products = \App\Models\Product::with('categories')->whereIn('id', $productIds)->get();

                        // تحقق إذا أي منتج free delivery
                        foreach ($products as $product) {
                            foreach ($product->categories as $category) {
                                if ($category->free_delivery == 1) {
                                    $deliveryCharge = 0;
                                    break 2; // يخرج من الحلقات الاثنين
                                }
                            }
                        }
                    @endphp
                    <div class="shopping-cart__totals-wrapper">
                        <div class="sticky-content">
                            <div class="shopping-cart__totals">
                                <h3>Cart Totals</h3>
                                @if (session()->has('discounts') && session()->has('coupon'))
    <table class="cart-totals">
        <tbody>
            <tr>
                <th>Subtotal</th>
                <td>${{ Cart::instance('cart')->subtotal() }}</td>
            </tr>
            <tr>
                <th>Discount {{ session('coupon.code') }}</th>
                <td>-${{ session('discounts.discount', 0) }}</td>
            </tr>
            <tr>
                <th>Subtotal After Discount</th>
                <td>${{ session('discounts.subtotal', Cart::instance('cart')->subtotal()) }}</td>
            </tr>
            <tr>
                <th>DELIVERY CHARGE</th>
                <td>${{ number_format($deliveryCharge, 2) }}</td>
            </tr>
            <tr class="cart-total">
                <th>Total</th>
                <td>${{ session('discounts.total', 0) - session('discounts.tax', 0) + $deliveryCharge }}</td>
            </tr>
        </tbody>
    </table>
@else
    <table class="cart-totals">
        <tbody>
            <tr>
                <th>Subtotal</th>
                <td>${{ Cart::instance('cart')->subtotal() }}</td>
            </tr>
            <tr>
                <th>DELIVERY CHARGE</th>
                <td>${{ number_format($deliveryCharge, 2) }}</td>
            </tr>
            <tr class="cart-total">
                <th>Total</th>
                <td>${{ Cart::instance('cart')->total() + $deliveryCharge - Cart::instance('cart')->tax() }}</td>
            </tr>
        </tbody>
    </table>
@endif

                   
                            </div>
                            <div class="mobile_fixed-btn_wrapper">
                                <div class="button-wrapper container">
                                    <a href="{{ route('cart.checkout') }}" class="btn btn-primary btn-checkout">PROCEED TO
                                        CHECKOUT</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-md-12 text-center pt-5 pb-5">
                            @if (session('order_success'))
                                <p>{{ session('order_success') }}</p>
                                <a href="{{ route('shop.index') }}" class="btn btn-info">Shop More</a>
                            @else
                                <p>No item found in your cart</p>
                                <a href="{{ route('shop.index') }}" class="btn btn-info">Shop Now</a>
                            @endif
                        </div>
                    </div>
                @endif

            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        $(function() {
            $(".qty-control__increase").on("click", function(e) {
                e.preventDefault();
                $(this).closest('form').submit();
            });

            $(".qty-control__reduce").on("click", function(e) {
                e.preventDefault();
                $(this).closest('form').submit();
            });
        });
    </script>
    <script>
        $(function() {
            $(".qty-control__increase, .qty-control__reduce").on("click", function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                $.ajax({
                    url: form.attr('action'),
                    type: form.find('input[name="_method"]').val() || form.attr('method'),
                    data: form.serialize(),
                    success: function(response) {
                        // هنا تتوقع ترجع الـ JSON من السيرفر يحتوي الكمية الجديدة، والسعر الجديد
                        // مثال للتحديث (عدل حسب بيانات الرد):
                        location.reload(); // أسهل حل مؤقت، يعيد تحميل الصفحة بعد التحديث
                    },
                    error: function() {
                        alert('حدث خطأ حاول لاحقاً');
                    }
                });
            });
        });
    </script>

    <script>
        $(function() {
            $('.remove-cart').on("click", function() {
                $(this).closest('form').submit();
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.qty-control__increase').forEach(button => {
                button.addEventListener('click', function(e) {
                    const input = this.closest('.qty-control').querySelector(
                        'input[type="number"]');
                    const maxQty = parseInt(input.getAttribute('data-max'));
                    const currentQty = parseInt(input.value);
                    if (currentQty >= maxQty) {
                        e.preventDefault();
                        alert("لا يمكن إضافة أكثر من الكمية المتوفرة بالمخزون.");
                    }
                });
            });

            document.querySelectorAll('.qty-control__reduce').forEach(button => {
                button.addEventListener('click', function(e) {
                    const input = this.closest('.qty-control').querySelector(
                        'input[type="number"]');
                    const currentQty = parseInt(input.value);
                    if (currentQty <= 1) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
@endpush
