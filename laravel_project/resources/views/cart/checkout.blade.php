@extends('layouts.app')
@section('content')
    <style>
        .cart-total th,
        .cart-total td {
            color: green;
            font-weight: bold;
            font-size: 21px !important;
        }
    </style>

    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="shop-checkout container">
            <h2 class="page-title">Shipping and Checkout</h2>

            <form name="checkout-form" action="{{ route('cart.place.order') }}" method="POST">
                @csrf
                <div class="checkout-form">
                    <div class="billing-info__wrapper">
                        <h4>SHIPPING DETAILS</h4>

                        @php
                            $customer = Auth::check()
                                ? \App\Models\Customer::where('user_id', Auth::id())->first()
                                : null;
                        @endphp

                        @if ($customer)
                            <div class="my-account__address-list">
                                <div class="my-account__address-item">
                                    <div class="my-account__address-item__detail">
                                        <p>{{ $customer->name ?? Auth::user()->name }}</p>
                                        <p>{{ $customer->address }}</p>
                                        <p>{{ $customer->city }}</p>
                                        <p>Phone: {{ $customer->phone }}</p>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-info mt-3" data-bs-toggle="modal"
                                data-bs-target="#editAddressModal">
                                Edit Address
                            </button>
                        @else
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="name"
                                            value="{{ old('name') }}" required>
                                        <label>Full Name *</label>
                                        <span class="text-danger">
                                            @error('name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="phone"
                                            value="{{ old('phone') }}" required>
                                        <label>Phone Number *</label>
                                        <span class="text-danger">
                                            @error('phone')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="city"
                                            value="{{ old('city') }}" required>
                                        <label>City *</label>
                                        <span class="text-danger">
                                            @error('city')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating my-3">
                                        <input type="text" class="form-control" name="address"
                                            value="{{ old('address') }}" required>
                                        <label>Address *</label>
                                        <span class="text-danger">
                                            @error('address')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" class="form-control" name="subtotal"
                                value="{{ Cart::instance('cart')->subtotal() }}" required>
                            @if (Session::has('discounts'))
                                <input type="hidden" class="form-control" name="discount"
                                    value="{{ Session('discounts')['discount'] }}">
                            @endif
                        @endif

                        <div class="checkout__payment-methods mt-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode" value="cod" id="mode_cod"
                                    checked>
                                <label class="form-check-label" for="mode_cod">Cash on delivery</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-4">PLACE ORDER</button>
                    </div>

                    <div class="checkout__totals-wrapper">
                        <div class="sticky-content">
                            <div class="checkout__totals">
                                <h3>Your Order</h3>
                                <table class="checkout-cart-items">
                                    <thead>
                                        <tr>
                                            <th>PRODUCT</th>
                                            <th class="text-right">SUBTOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (Cart::instance('cart')->content() as $item)
                                            <tr>
                                                <td>{{ $item->name }} x {{ $item->qty }}</td>
                                                <td class="text-right">${{ $item->subtotal }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                @php
                                    $deliveryCharge =
                                        \App\Models\Setting::where('key', 'delivery_charge')->value('value') ?? 0;

                                    // جلب كل المنتجات مع الفئات (many-to-many)
                                    $productIds = Cart::instance('cart')->content()->pluck('id')->toArray();
                                    $products = \App\Models\Product::with('categories')
                                        ->whereIn('id', $productIds)
                                        ->get();

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



                                <table class="checkout-totals">
                                    <tbody>
                                        <tr>
                                            <th>Subtotal</th>
                                            <td class="text-right">${{ Cart::instance('cart')->subtotal() }}</td>
                                        </tr>
                                        @if (Session::has('discounts'))
                                            <tr>
                                                <th>Discount ({{ Session('coupon')['code'] }})</th>
                                                <td class="text-right">-${{ Session('discounts')['discount'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>After Discount</th>
                                                <td class="text-right">${{ Session('discounts')['subtotal'] }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>Delivery</th>
                                            <td class="text-right">${{ number_format($deliveryCharge, 2) }}</td>
                                        </tr>
                                        <tr class="cart-total">
                                            <th>Total</th>
                                            <td class="text-right">

                                                @if (Session::has('discounts'))
                                                    ${{ Session('discounts')['total'] + $deliveryCharge - Session('discounts')['tax'] }}
                                                @else
                                                    ${{ Cart::instance('cart')->total() + $deliveryCharge - Cart::instance('cart')->tax() }}
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </main>

    @if ($customer)
        <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('user.update.address') }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Shipping Address</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-floating mb-3">
                                <input type="text" name="address" value="{{ $customer->address }}" class="form-control"
                                    required>
                                <label>Address</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" name="city" value="{{ $customer->city }}" class="form-control"
                                    required>
                                <label>City</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" name="phone" value="{{ $customer->phone }}"
                                    class="form-control" required pattern="^(?:\d{8}|\+961\d{8}|00961\d{8})$"
                                    title="Enter a valid phone number">
                                <label>Phone</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection
