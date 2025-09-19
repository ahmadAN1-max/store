@extends('layouts.admin')
@section('content')
    <style>
        .table-transaction>tbody>tr:nth-of-type(odd) {
            --bs-table-accent-bg: #fff !important;

            .modal-backdrop.show {
                background-color: transparent !important;
            }
        }

        .editable-input {
            border: none;
            background: none;
            font: inherit;
            color: inherit;
            padding: 0;
            margin: 0;
            width: auto;
            min-width: 50px;
        }

        .editable-input[readonly] {
            pointer-events: none;
        }

        .editable-input:focus {
            outline: 1px dashed #aaa;
            background-color: #fff;
            border-bottom: 1px solid #ccc;
            pointer-events: auto;
        }
    </style>
    <div class="main-content">
        <div class="main-content-inner">
            <div class="main-content-wrap">

                <!-- Modal Hold Bill -->
                <div class="modal fade" id="holdBillModal" tabindex="-1" aria-labelledby="holdBillModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="holdBillModalLabel">Hold Bill</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="holdBillForm">
                                    <div class="mb-3">
                                        <label for="nameInput" class="form-label">Name <span
                                                class="text-danger">*</span></label>
                                        @if (isset($bill))
                                            <input type="text" value="{{ $bill->name }}" class="form-control"
                                                id="nameInput" placeholder="Enter Customer Name" required>
                                        @else
                                            <input type="text" class="form-control" id="nameInput"
                                                placeholder="Enter Customer Name" required>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label for="phoneInput" class="form-label">Phone Number <span
                                                class="text-danger">*</span></label>
                                        @if (isset($bill))
                                            <input type="tel" class="form-control" id="phoneInput"
                                                value="{{ $bill->phone_number }}" placeholder="Enter Phone Number" required>
                                        @else
                                            <input type="tel" class="form-control" id="phoneInput"
                                                placeholder="Enter Phone Number" required>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label for="referenceInput" class="form-label">Reference <span
                                                class="text-danger">*</span></label>
                                        @if (isset($bill))
                                            <input type="text" class="form-control" id="referenceInput"
                                                value="{{ $bill->reference }}" placeholder="Enter Reference Number"
                                                required>
                                        @else
                                            <input type="text" class="form-control" id="referenceInput"
                                                placeholder="Enter Reference Number" required>
                                        @endif
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="confirmHoldBtn">Save</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                    <h3>Order Details</h3>
                    <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                        <li>
                            <a href="{{ route('admin.index') }}">
                                <div class="text-tiny">Dashboard</div>
                            </a>
                        </li>
                        <li>
                            <i class="icon-chevron-right"></i>
                        </li>
                        <li>
                            <div class="text-tiny">Order Items</div>
                        </li>
                    </ul>
                </div>

                <div class="wg-box mt-5 mb-5">
                    <div class="flex items-center justify-between gap10 flex-wrap">
                        <div class="wg-filter flex-grow">
                            <h5>Ordered Details</h5>
                        </div>
                        <a class="tf-button style-1 w208" href="{{ route('admin.orders') }}">Back</a>
                    </div>

                    <table class="table table-striped table-bordered table-transaction">
                        <tr>
                            <th>Order No</th>
                            <td>{{ '1' . str_pad($transaction->order->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <th>Mobile</th>
                            <td>{{ $transaction->order->phone }}</td>
                            <th>Order Status</th>
                            <td>
                                @if ($transaction->order->status == 'delivered')
                                    <span class="badge bg-success">Delivered</span>
                                @elseif($transaction->order->status == 'canceled')
                                    <span class="badge bg-danger">Canceled</span>
                                @else
                                    <span class="badge bg-warning">Ordered</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Order Date</th>
                            <td>{{ $transaction->order->created_at }}</td>
                            <th>Delivered Date</th>
                            <td>{{ $transaction->order->delivered_date }}</td>
                            <th>Canceled Date</th>
                            <td>{{ $transaction->order->canceled_date }}</td>
                        </tr>
                    </table>
                </div>

                <div class="wg-box mt-5">
                    <div class="flex items-center justify-between gap10 flex-wrap">
                        <div class="wg-filter flex-grow">
                            <h5>Ordered Items</h5>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>

                                <tr>
                                    <th>Name</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Size</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">SKU</th>
                                    <th class="text-center">Category</th>
                                    <th class="text-center">Brand</th>

                                    <th class="text-center">Return Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orderitems as $orderitem)
                                    <tr data-product-id="{{ $orderitem->product_id }}"
                                        data-child-id="{{ $orderitem->product->children->first()->id ?? 0 }}">


                                        <td class="pname">
                                            <div class="image">
                                                <img src="{{ asset('uploads/products/thumbnails') }}/{{ $orderitem->product->image }}"
                                                    alt="" class="image">
                                            </div>
                                            <div class="name">
                                                <a href="{{ route('shop.product.details', ['product_slug' => $orderitem->product->slug]) }}"
                                                    target="_blank"
                                                    class="body-title-2">{{ $orderitem->product->name ?? 'N/A' }}</a>
                                            </div>
                                        </td>
                                        <td class="text-center">${{ $orderitem->price }}</td>
                                        <td class="text-center">{{ $orderitem->size }}</td>
                                        <td class="text-center">{{ $orderitem->quantity }}</td>
                                        <td class="text-center">{{ $orderitem->product->SKU }}</td>
                                        <td class="text-center">
                                            {{ optional($orderitem->product->category)->name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $orderitem->product->brand->name }}</td>

                                        <td class="text-center">{{ $orderitem->rstatus == 0 ? 'No' : 'Yes' }}</td>

                                        <td class="text-center">
                                            <a href="{{ route('shop.product.details', ['product_slug' => $orderitem->product->slug]) }}"
                                                target="_blank">
                                                <div class="list-icon-function view-icon">
                                                    <div class="item eye">
                                                        <i class="icon-eye"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="divider"></div>
                    <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                        {{ $orderitems->links('pagination::bootstrap-5') }}
                    </div>
                </div>
                <div class="wg-box mt-5">
                    <h5>Shipping Address</h5>
                    <div class="my-account__address-item col-md-6">
                        <div class="my-account__address-item__detail">
                            <p>{{ $transaction->order->name }}</p>
                            <p>{{ $transaction->order->address }}</p>
                            <p>{{ $transaction->order->locality }}</p>
                            <p>{{ $transaction->order->city }}, {{ $transaction->order->country }}</p>
                            <p>{{ $transaction->order->landmark }}</p>
                            <p>{{ $transaction->order->zip }}</p>
                            <br />
                            <p>Mobile : {{ $transaction->order->phone }}</p>
                        </div>
                    </div>
                </div>
                <div class="wg-box mt-5">
                    <h5>Transactions</h5>
                    <table class="table table-striped table-bordered table-transaction">
                        <tr>
                            <th>Subtotal</th>

                            <td>$<input type="text" name="total_price" id="total_price" class="editable-input"
                                    value=" {{ $transaction->order->subtotal }}"
                                    onclick="this.removeAttribute('readonly'); this.focus();"
                                    onblur="this.setAttribute('readonly', true)"></td>
                            <th>Delivery Charge</th>

                            @php

                                $deliveryCharge =
                                    \App\Models\Setting::where('key', 'delivery_charge')->value('value') ?? 0;

                                // جلب كل المنتجات مع الفئات (many-to-many)
                                $productIds = Cart::instance('cart')->content()->pluck('id')->toArray();
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

                                $displayTotal = $order->subtotal + $deliveryCharge - $transaction->order->discount;
                            @endphp
                            <td>${{ number_format($deliveryCharge, 2) }}</td>
                            <th>Discount</th>
                            <td>${{ $transaction->order->discount }}</td>
                            <th>Total</th>
                            <td>${{ $displayTotal }}</td>
                        </tr>

                    </table>
                </div>
                <div class="wg-box mt-5">
                    <h5>Update Order Status</h5>
                    <form action="{{ route('admin.order.status.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="order_id" value="{{ $transaction->order->id }}" />
                        <div class="row">
                            <div class="col-md-3">
                                <div class="select">
                                    <select id="order_status" name="order_status">
                                        <option value="ordered"
                                            {{ $transaction->order->status == 'ordered' ? 'selected' : '' }}>Ordered
                                        </option>
                                        <option value="delivered"
                                            {{ $transaction->order->status == 'delivered' ? 'selected' : '' }}>Delivered
                                        </option>


                                        <option value="canceled"
                                            {{ $transaction->order->status == 'canceled' ? 'selected' : '' }}>Canceled
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary tf-button w208">Update</button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" data-bs-toggle="modal" data-bs-target="#holdBillModal"
                                    class="btn btn-primary tf-button w208">Hold
                                    Bill</button>
                            </div>
                        </div>
                    </form>
                    {{-- /          /<input type="hidden" name="total_price" id="total_price"
                        value="{{ $transaction->order->subtotal }}"> --}}
                </div>
            </div>
        </div>
    @endsection
    @push('scripts')
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Hold bill form submit

            $('#confirmHoldBtn').on('click', function() {
                const products = [];

                $('table.table-striped tbody tr').each(function() {
                    const row = $(this);
                    const product_id = row.data('product-id');
                    const child_id = row.data('child-id');
                    const priceText = row.find('td').eq(1).text().trim();
                    const quantityText = row.find('td').eq(3).text().trim();

                    const price = parseFloat(priceText.replace('$', '')) || 0;
                    const quantity = parseInt(quantityText) || 1;

                    if (product_id) {
                        products.push({
                            product_id,
                            child_id,
                            price,
                            quantity
                        });
                    }
                });

                if (products.length === 0) {
                    alert('No products found!');
                    return;
                }
                const total = $('#total_price').val();
                const name = $('#nameInput').val().trim();
                const phone_number = $('#phoneInput').val().trim();
                const reference = $('#referenceInput').val().trim();
                if (!name || !phone_number || !reference) {
                    alert('Please fill in all required fields!');
                    return;
                }

                $.ajax({
                    url: '{{ route('admin.holdBill') }}',
                    method: 'POST',
                    data: {
                        name,
                        phone_number,
                        reference,
                        total,
                        products
                    },
                    success: function(response) {
                        alert('Data sent successfully!');
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('Error sending data!');
                        console.error(xhr.responseText);
                    }
                });
            });
        </script>
    @endpush
