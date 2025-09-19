@extends('layouts.pos')
@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="main-content-wrap">
                <!DOCTYPE html>
                <html lang="en">

                <head>
                    <meta charset="UTF-8">
                    <title>Print Bill #{{ $bill->bill_number }}</title>
                    <style>
                        /* تنسيقات الطباعة */
                        body {
                            font-family: Arial, sans-serif;
                            margin: 20px;
                            color: #000;
                        }

                        .bill-container {
                            width: 100%;
                            max-width: 800px;
                            margin: auto;
                        }

                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-bottom: 15px;
                        }

                        th,
                        td {
                            border: 1px solid #333;
                            padding: 8px;
                            text-align: left;
                        }

                        th {
                            background-color: #eee;
                        }

                        @media print {

                            .no-print {
                                display: none;
                            }
                        }
                    </style>
                    <style>
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


                </head>

                <body>
                    <div id="billContainer" class="bill-container">
                        <h2>Invoice #{{ $bill->id }}</h2>
                        @if ($bill->status == 'unpaid' || $bill->status == 'unpaidWebsite')
                             <h3>{{ $bill->status }}</h3>
                        @endif
                        <p>
                            <strong>Customer Name:</strong>
                            <input type="text" class="editable-input" value="{{ $bill->name ?? 'customer' }}" 
                                onclick="this.removeAttribute('readonly'); this.focus();"
                                onblur="this.setAttribute('readonly', true)">
                        </p>
                               <p>
                            <strong>Phone Number:</strong>
                            <input type="text" class="editable-input" value="{{ $bill->phone_number ?? '****' }}" 
                                onclick="this.removeAttribute('readonly'); this.focus();"
                                onblur="this.setAttribute('readonly', true)">
                        </p>
                          <p>
                            <strong>Employee:</strong>
                            <input type="text" class="editable-input" value="{{ $bill->employee_name ?? '****' }}" 
                                onclick="this.removeAttribute('readonly'); this.focus();"
                                onblur="this.setAttribute('readonly', true)">
                        </p>
                        
                        <p><strong>Date:</strong> {{ $bill->created_at->format('Y-m-d H:i') }}</p>

                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Size</th>
                                    <th>Quantity</th>
                                    <th>Price ($)</th>
                                    <th>Total ($)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bill->billItems as $item)
                                    <tr>
                                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                                        <td>{{ $item->child->sizes ?? 'N/A' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->price, 2) }}</td>
                                        <td>{{ number_format($item->price * $item->quantity, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <p><strong>Discount:</strong> {{ number_format($bill->discount ?? 0, 2) }} $</p>
                        <p><strong>Total Price:</strong> {{ number_format($bill->total_price, 2) }} $</p>

                        <style>
                            .no-print {
                                margin-top: 20px;
                                display: flex;
                                gap: 15px;
                                justify-content: center;
                                /* لتوسيط الأزرار أفقياً */
                            }

                            .no-print button,
                            .no-print a {
                                padding: 12px 25px;
                                font-size: 1rem;
                                font-weight: 600;
                                border: none;
                                border-radius: 8px;
                                cursor: pointer;
                                text-decoration: none;
                                transition: background-color 0.3s ease, color 0.3s ease;
                                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
                                user-select: none;
                            }

                            .no-print button {
                                background-color: #0d6efd;
                                /* أزرق Bootstrap */
                                color: white;
                            }

                            .no-print button:hover {
                                background-color: #084cd6;
                            }

                            .no-print a {
                                background-color: #6c757d;
                                /* رمادي Bootstrap */
                                color: white;
                                display: inline-flex;
                                align-items: center;
                                justify-content: center;
                            }

                            .no-print a:hover {
                                background-color: #565e64;
                                text-decoration: none;
                                color: white;
                            }

                            /* متجاوب للموبايل */
                            @media (max-width: 480px) {
                                .no-print {
                                    flex-direction: column;
                                    gap: 10px;
                                }

                                .no-print button,
                                .no-print a {
                                    width: 100%;
                                    padding: 14px 0;
                                    font-size: 1.1rem;
                                }
                            }
                            
                        </style>

                        <div class="no-print">
                           <button onclick="printBill()">Print Invoice</button>

                            <a href="{{ route('pos.index') }}">Back to POS</a>
                        </div>

                    </div>
                </body>

                </html>


            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
function printBill() {
    var printContents = document.getElementById('billContainer').innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}
</script>

@endpush