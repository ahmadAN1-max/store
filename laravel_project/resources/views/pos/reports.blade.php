@extends('layouts.pos')
@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="main-content-wrap">
                <!-- Filter Form -->
                <form method="GET" action="{{ route('report.generate') }}" class="row g-3 align-items-end mb-4">
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from"
                            value="{{ $from ?? date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to"
                            value="{{ $to ?? date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-2">
                        <label for="store" class="form-label">Store</label>
                        <select class="form-select" id="store" name="store" required>
                            <option value="SP" {{ isset($store) && $store == 'SP' ? 'selected' : '' }}>Sarah's Palace</option>
                            <option value="LUXE" {{ isset($store) && $store == 'LUXE' ? 'selected' : '' }}>Luxe Couture</option>
                            <option value="both" {{ isset($store) && $store == 'both' ? 'selected' : '' }}>All Stores
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="report_type" class="form-label">Report Type</label>
                        <select class="form-select" id="report_type" name="report_type" required>
                            <option value="pos" {{ isset($type) && $type == 'pos' ? 'selected' : '' }}>POS Only</option>
                            <option value="web" {{ isset($type) && $type == 'web' ? 'selected' : '' }}>Web Only</option>
                            <option value="both" {{ isset($type) && $type == 'both' ? 'selected' : '' }}>POS and Web
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" name="category_id" id="category_id">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ isset($categoryId) && $categoryId == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 d-grid">
                        <button type="submit" class="btn btn-primary">Generate</button>
                    </div>
                </form>

                @if ($bills && $bills->count() > 0)
                    @php
                        $grandSales = 0;
                        $grandCost = 0;
                        $grandProfit = 0;
                    @endphp

                    @foreach ($bills as $bill)
                        @php
                            $billTotal = 0;
                            $billCost = 0;
                            $billProfit = 0;
                        @endphp

                        <div class="card mb-4">
                            <div
                                class="card-header {{ $bill->billItems->contains(fn($i) => $i->is_return) ? 'bg-danger' : 'bg-primary' }} text-white">
                                <strong>Bill #: {{ $bill->bill_number }}</strong> |
                                Date: {{ $bill->created_at->format('Y-m-d H:i') }} |
                                Customer: {{ $bill->name ?? 'N/A' }} |
                                Phone: {{ $bill->phone_number ?? 'N/A' }}
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            @if ($admin)
                                                <th>Unit Cost</th>
                                            @endif
                                                <th>Price</th>
                                                <th>Qty</th>
                                            @if ($admin)
                                                <th>Profit</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bill->billItems as $item)
                                            @php
                                                $unitCost = $item->product->unit_cost ?? 0;
                                                $qty = $item->quantity;
                                                // السعر الفعلي مع مراعاة المرتجع
                                                $price = $item->is_return ? -abs($item->price) : $item->price;
                                                $profit = ($price - $unitCost) * $qty;
                                                $displayClass = $item->is_return ? 'text-danger' : '';

                                                $billTotal += $price;
                                                $billCost += $item->is_return ? 0 : $unitCost; // المخزون المرتجع ما يحسب تكلفة
                                                $billProfit += $profit;
                                            @endphp
                                            <tr class="{{ $displayClass }}">
                                                <td>{{ $item->product->name }}</td>
                                                @if ($admin)
                                                    <td>${{ number_format($unitCost, 2) }}</td>
                                                @endif
                                                <td>${{ number_format($price, 2) }}</td>
                                                <td>{{ $qty }}</td>
                                                @if ($admin)
                                                    <td>${{ number_format($profit, 2) }}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="p-3">
                                    <strong>Total:</strong> ${{ number_format($billTotal, 2) }} |
                                    @if ($admin)
                                        <strong>Cost:</strong> ${{ number_format($billCost, 2) }} |
                                        <strong>Profit:</strong> ${{ number_format($billProfit, 2) }}|
                                    @endif
                                    <strong>Employee:</strong> {{ $bill->employee_name }}
                                </div>
                            </div>
                        </div>

                        @php
                            $grandSales += $billTotal;
                            $grandCost += $billCost;
                            $grandProfit += $billProfit;
                        @endphp
                    @endforeach

                    <div class="card mt-4">
                        <div class="card-body">
                            <h4>Grand Totals</h4>
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Total Sales:</strong>
                                    ${{ number_format($grandSales, 2) }}</li>
                                @if ($admin)
                                    <li class="list-group-item"><strong>Total Cost:</strong>
                                        ${{ number_format($grandCost, 2) }}</li>
                                    <li class="list-group-item"><strong>Total Profit:</strong>
                                        ${{ number_format($grandProfit, 2) }}</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @else
                    <p>No bills found.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
