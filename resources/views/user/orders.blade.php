@extends('layouts.app')
@section('content')
    <style>
        .table > :not(caption) > tr > th {
            padding: 0.75rem 1.5rem !important;
            background-color: #6a6e51 !important;
            color: #fff;
            text-align: center;
        }

        .table > :not(caption) > tr > td {
            padding: 0.75rem 1.5rem !important;
            vertical-align: middle;
            text-align: center;
            border-color: #6a6e51;
        }

        .table-bordered > :not(caption) > tr > th,
        .table-bordered > :not(caption) > tr > td {
            border-width: 1px;
            border-color: #6a6e51 !important;
        }

        .badge.bg-success {
            background-color: #28a745 !important;
            padding: 5px 10px;
            border-radius: 12px;
            font-weight: 600;
        }

        .badge.bg-danger {
            background-color: #dc3545 !important;
            padding: 5px 10px;
            border-radius: 12px;
            font-weight: 600;
        }

        .badge.bg-warning {
            background-color: #ffc107 !important;
            padding: 5px 10px;
            border-radius: 12px;
            font-weight: 600;
            color: #212529 !important;
        }

        /* أيقونة العين */
        .list-icon-function.view-icon .item.eye i {
            font-size: 18px;
            color: #6a6e51;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .list-icon-function.view-icon .item.eye i:hover {
            color: #b9a16b;
        }
    </style>

    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="my-account container">
            <h2 class="page-title">Orders</h2>
            <div class="row">
                <div class="col-lg-2">
                    @include('user.account-nav')
                </div>
                <div class="col-lg-10">
                    <div class="wg-table table-all-user">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 80px">Order No</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Subtotal</th>
                                        <th>Tax</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Order Date</th>
                                        <th>Items</th>
                                        <th>Delivered On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td>{{ '1' . str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                                            <td>{{ $order->name }}</td>
                                            <td>{{ $order->phone }}</td>
                                            <td>${{ number_format($order->subtotal, 2) }}</td>
                                            <td>${{ number_format($order->tax, 2) }}</td>
                                            <td>${{ number_format($order->total, 2) }}</td>
                                            <td>
                                                @if ($order->status == 'delivered')
                                                    <span class="badge bg-success">Delivered</span>
                                                @elseif($order->status == 'canceled')
                                                    <span class="badge bg-danger">Canceled</span>
                                                @else
                                                    <span class="badge bg-warning">Ordered</span>
                                                @endif
                                            </td>
                                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                            <td>{{ $order->orderItems->count() }}</td>
                                            <td>{{ $order->delivered_date ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('user.account.order.details', ['order_id' => $order->id]) }}" title="View Details">
                                                    <div class="list-icon-function view-icon">
                                                        <div class="item eye">
                                                            <i class="fa fa-eye"></i>
                                                        </div>
                                                    </div>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="divider"></div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $orders->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
