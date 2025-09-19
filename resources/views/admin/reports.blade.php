@extends('layouts.admin')
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('admin-report.generate') }}" class="row g-3 align-items-end mb-4">
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="date_from" name="date_from"
                        value="{{ $from ?? date('Y-m-d') }}" required>
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="date_to" name="date_to"
                        value="{{ $to ?? date('Y-m-d') }}" required>
                </div>
                
                <div class="col-md-2">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select" name="category_id" id="category_id">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ isset($categoryId) && $categoryId==$category->id ? 'selected' : '' }}>
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
                        <div class="card-header {{ $bill->billItems->contains(fn($i) => $i->is_return) ? 'bg-danger' : 'bg-primary' }} text-white">
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
                                        <th>Unit Cost</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Profit</th>
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
                                            <td>${{ number_format($unitCost,2) }}</td>
                                            <td>${{ number_format($price,2) }}</td>
                                            <td>{{ $qty }}</td>
                                            <td>${{ number_format($profit,2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="p-3">
                                <strong>Total:</strong> ${{ number_format($billTotal,2) }} |
                                <strong>Cost:</strong> ${{ number_format($billCost,2) }} |
                                <strong>Profit:</strong> ${{ number_format($billProfit,2) }}
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
                            <li class="list-group-item"><strong>Total Sales:</strong> ${{ number_format($grandSales,2) }}</li>
                            <li class="list-group-item"><strong>Total Cost:</strong> ${{ number_format($grandCost,2) }}</li>
                            <li class="list-group-item"><strong>Total Profit:</strong> ${{ number_format($grandProfit,2) }}</li>
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
