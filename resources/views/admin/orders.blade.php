@extends('layouts.admin')
@section('content')
    <style>
        .table-striped th:nth-child(1),
        .table-striped td:nth-child(1) {
            width: 100px;
        }

        .table-striped th:nth-child(2),
        .table-striped td:nth-child(2) {
            width: 250px;
        }
    </style>
    <div class="main-content">
        <div class="main-content-inner">
            <div class="main-content-wrap">
                <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                    <h3>Orders</h3>
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
                            <div class="text-tiny">All Order</div>
                        </li>
                    </ul>
                </div>

                <div class="wg-box">
                    <div class="flex items-center justify-between gap10 flex-wrap">
                        <div class="wg-filter flex-grow">
                            <form class="form-search">
                                <fieldset class="name">
                                    <input type="text" placeholder="Search by order nb here..." id="searchInput"
                                        name="name" tabindex="2" value="" aria-required="true" required>

                                </fieldset>
                                <div class="button-submit">
                                    <button class="" type="submit"><i class="icon-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="wg-table table-all-user">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 80px">Order No</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Phone</th>
                                        <th class="text-center">Subtotal</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Order Date</th>
                                        <th class="text-center">Total Items</th>
                                        <th class="text-center">Delivered On</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td class="text-center">{{ '1' . str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                            </td>
                                            <td class="text-center">{{ $order->name }}</td>
                                            <td class="text-center">{{ $order->phone }}</td>
                                            <td class="text-center">${{ $order->subtotal }}</td>

                                            @php
                                                $deliveryCharge = $order->total - $order->subtotal; // افتراضياً
                                                foreach ($order->orderItems as $orderitem) {
                                                    $product = \App\Models\Product::find($orderitem->product_id);
                                                    if ($product && $product->category_id) {
                                                        $category = \App\Models\Category::find($product->category_id);
                                                        if ($category && intval($category->free_delivery) === 1) {
                                                            $deliveryCharge = 0;
                                                            break;
                                                        }
                                                    }
                                                }
                                                $displayTotal = $order->subtotal + $deliveryCharge - $order->discount;
                                            @endphp

                                            <td class="text-center">${{ number_format($displayTotal, 2) }}</td>
                                            {{-- <td class="text-center">
                                    <p>{{$order->address}}</p>
                                    <p>{{$order->locality}}</p>
                                    <p>{{$order->city}}, {{$order->state}}, {{$order->zip}}</p>                                    
                                </td> --}}
                                            <td class="text-center">{{ $order->status }}</td>
                                            <td class="text-center">{{ $order->created_at }}</td>
                                            <td class="text-center">{{ $order->orderItems->count() }}</td>
                                            <td>{{ $order->delivered_date }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.order.items', ['order_id' => $order->id]) }}">
                                                    <div class="list-icon-function view-icon">
                                                        <div class="item eye">
                                                            <i class="icon-eye"></i>
                                                        </div>
                                                    </div>
                                                </a>
                                                <form action="{{ route('admin.order.delete', ['id' => $order->id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="item text-danger delete"
                                                        style="background:none; border:none; padding:0;">
                                                        <i class="icon-trash-2"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="divider"></div>
                    
                </div>
            </div>
        </div>
    @endsection
    @push('scripts')
        <script>
            $(function() {
                $(".delete").on('click', function(e) {
                    e.preventDefault();
                    var selectedForm = $(this).closest('form');
                    swal({
                        title: "Are you sure?",
                        text: "You want to delete this record?",
                        icon: "warning",
                        buttons: ["No!", "Yes!"],
                        dangerMode: true,
                    }).then(function(willDelete) {
                        if (willDelete) {
                            selectedForm.submit();
                        }
                    });
                });
            });
        </script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const searchInput = document.getElementById('searchInput');
                const table = document.querySelector('table tbody');
                const rows = table.getElementsByTagName('tr');

                searchInput.addEventListener('keyup', function() {
                    const filter = searchInput.value.toLowerCase();

                    for (let i = 0; i < rows.length; i++) {
                        const row = rows[i];
                        // العمود الأول في الجدول (Order No) هو الخانة رقم 0 (حسب الترتيب يبدأ من 0)
                        const orderNoCell = row.cells[0];
                        const orderNoText = orderNoCell.textContent || orderNoCell.innerText;

                        if (orderNoText.toLowerCase().indexOf(filter) > -1) {
                            row.style.display = ""; // إظهار الصف
                        } else {
                            row.style.display = "none"; // إخفاء الصف
                        }
                    }
                });
            });
        </script>
    @endpush
