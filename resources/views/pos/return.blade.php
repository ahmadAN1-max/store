@extends('layouts.pos')
@section('content')
    <style>
        .modal-backdrop.show {
            background-color: transparent !important;
        }

        /* Container */
        .container {
            max-width: 1000px;
        }

        :root {
            --primary-color: #0d6efd;
            --secondary-color: #f8f9fa;
            --text-dark: #212529;
            --border-radius: 10px;
            --font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* أساسيات */
        body {
            font-family: var(--font-family);
            background-color: #ffffff;
            color: var(--text-dark);
        }

        .container {
            max-width: 960px;
            margin: auto;
            padding: 2rem 1rem;
        }

        /* العنوان */
        h2 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 30px;
            text-align: center;
        }

        /* الحقول */
        input.form-control {
            padding: 12px 15px;
            font-size: 1rem;
            border-radius: var(--border-radius);
            border: 1px solid #ced4da;
            transition: border-color 0.3s ease;
        }

        input.form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.2);
        }

        /* جدول المنتجات */
        table.table {
            width: 100%;
            border-spacing: 0;
            border-collapse: collapse;
        }

        table thead {
            background-color: var(--primary-color);
            color: white;
        }

        table th,
        table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }

        table tbody tr:hover {
            background-color: #f1f3f5;
        }

        /* أزرار */
        .btn {
            padding: 12px 20px;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 1rem;
            border: none;
            transition: background-color 0.2s ease;
        }

        .btn-success {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-success:hover {
            background-color: #0b5ed7;
        }

        .btn-warning {
            background-color: #ffc107;
            color: black;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
            padding: 6px 12px;
            font-size: 0.9rem;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        /* Modal */
        .modal-header,
        .modal-footer {
            border: none;
        }

        .modal-title {
            font-size: 1.2rem;
            color: var(--primary-color);
        }

        /* Discount / Total Inputs */
        #discountInput,
        #totalAmount {
            font-size: 1.1rem;
            padding: 12px;
            border-radius: var(--border-radius);
        }

        /* Responsive */
        @media (max-width: 768px) {
            h2 {
                font-size: 1.6rem;
            }

            .btn {
                width: 100%;
                margin-bottom: 10px;
            }

            .under-checkout-buttons {
                flex-direction: column;
            }
        }

        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="main-content-wrap">
                @if (!empty($billItems))
                    {{ $bill->bill_number }}
                    <div class="container">
                        <h2>POS Dashboard</h2>
                        {{-- Checkout big button --}}

                    </div>
                    {{-- Input Scan --}}
                    <input type="text" id="barcodeInput" style="margin-top:30px" class="form-control"
                        placeholder="Scan Barcode..." autofocus>
                    <div style="height: 280px; overflow-y: auto;">
                        {{-- Table --}}
                        <table id="scannedProductsTable" class="table table-bordered mt-3">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Barcode</th>
                                    <th>Size</th>
                                    <th>Price ($)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        @else
                            <div class="container">
                                <h2>POS Dashboard</h2>
                                {{-- Checkout big button --}}

                                {{-- Input Scan --}}
                                <input type="text" id="barcodeInput" style="margin-top:30px" class="form-control"
                                    placeholder="Scan Barcode..." autofocus>
                                <div style="height: 280px; overflow-y: auto;">
                                    {{-- Table --}}
                                    <table id="scannedProductsTable" class="table table-bordered mt-3">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Barcode</th>
                                                <th>Size</th>
                                                <th>Price ($)</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                @endif
                <tbody>
                    @if (!empty($billItems))
                        @foreach ($billItems as $item)
                            <tr data-product-id="{{ $item->product_id }}" data-child-id="{{ $item->child_id }}">
                                <td style="text-align: center">{{ $item->product->name ?? 'N/A' }}</td>
                                <td style="text-align: center">{{ $item->child->barcode ?? 'N/A' }}</td>
                                <td style="text-align: center">{{ $item->child->sizes ?? 'N/A' }}</td>

                                <td class="price-cell" style="text-align: center">{{ number_format($item->price, 2) }}</td>
                                <td style="text-align: center"><button class="btn btn-danger btn-sm remove-btn">❌</button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>

                </table>
            </div>
            <div class="col-md-4" style="margin-left: auto;">

                @if (!empty($billItems))
                    {{-- زر Checkout كبير --}}
                    <button class="btn btn-success mb-3" style="width: 100%; padding: 15px; font-size: 1.25rem;"
                        id="checkoutSavedBtn">
                        Checkout
                    </button>

                    {{-- زرّين Hold Bill و Clear جنب بعض --}}
                    <div class="d-flex justify-content-between mb-3">
                        <button class="btn btn-warning" style="flex: 1; margin-right: 10px;" data-bs-toggle="modal"
                            data-bs-target="#holdBillModal">
                            Hold Bill
                        </button>
                        <button class="btn btn-secondary" style="flex: 1;">
                            Clear
                        </button>
                    </div>

                    {{-- مدخل Discount و Total تحت --}}
                    <div class="row g-2">
                        <div class="col-4">
                            <input type="number" id="discountInput" class="form-control" placeholder="Discount">
                        </div>
                        <div class="col-8">
                            <input type="text" id="totalAmount" class="form-control" readonly placeholder="Total"
                                style="font-weight: bold; font-size: 1.3rem;">
                        </div>
                    </div>
                @else
                    {{-- زر Checkout كبير --}}
                    <button class="btn btn-success mb-3" style="width: 100%; padding: 15px; font-size: 1.25rem;"
                        id="checkoutBtn">
                        Return
                    </button>

                    {{-- زرّين Hold Bill و Clear جنب بعض --}}
                    <div class="d-flex justify-content-between mb-3">
                        <button class="btn btn-secondary" style="flex: 1;" id="clearBtn">
                            Clear
                        </button>
                    </div>
                    {{-- مدخل Discount و Total تحت --}}
                    <div class="row g-2">

                        <div class="col-8">
                            <input type="text" id="totalAmount" class="form-control" readonly placeholder="Total"
                                style="font-weight: bold; font-size: 1.3rem;">
                        </div>
                    </div>
                @endif

            </div>



            {{-- Hold Bill Modal --}}
            <div class="modal fade" id="holdBillModal" tabindex="-1">
                <div class="modal-dialog">
                    <form id="holdBillForm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Hold Bill</h5>
                            </div>
                            <div class="modal-body">
                                <input type="text" name="name" class="form-control mb-2" placeholder="Customer Name">
                                <input type="text" name="phone_number" class="form-control mb-2"
                                    placeholder="Phone Number">
                                <input type="text" name="reference" class="form-control" placeholder="Reference">
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-warning">Save Hold</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    </div>
    <div class="bottom-page">
        <div class="body-text">Copyright © 2025 Sarah's Palace</div>
    </div>
    </div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // (نفس السكريبت الذي أرسلته سابقاً بدون تغيير)
        let scannedProducts = [];

        // Clear button functionality
        $('#clearBtn').on('click', function() {
            scannedProducts = [];
            $('#scannedProductsTable tbody').empty();
            $('#discountInput').val('');
            $('#totalAmount').val('');
            $('#barcodeInput').val('').focus();
        });

        // Barcode enter key
        $('#barcodeInput').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                const barcode = $(this).val().trim();
                if (!barcode) return;

                $.get('/pos/scan/' + barcode, function(child) {
                    if (!child) return alert('Product not found!');

                    scannedProducts.push(child);

                    $('#scannedProductsTable tbody').append(`
                    <tr data-product-id="${child.product_id}" data-child-id="${child.child_id}" style="text-align: center">
                        <td style="text-align: center">${child.name}</td>
                        <td style="text-align: center">${child.barcode}</td>
                        <td style="text-align: center">${child.size}</td>
                        <td class="price-cell" style="text-align: center">${parseFloat(child.price).toFixed(2)}</td>
                        <td style="text-align: center"><button class="btn btn-danger btn-sm remove-btn">❌</button></td>
                    </tr>
                `);

                    $('#barcodeInput').val('').focus();
                    updateTotal();
                }).fail(function() {
                    alert('Barcode not found');
                    $('#barcodeInput').val('').focus();
                });
            }
        });

        // Price cell edit
        $(document).on('click', '.price-cell', function() {
            const cell = $(this);
            const currentPrice = parseFloat(cell.text()) || 0;

            if (cell.find('input').length > 0) return;

            cell.html(
                `<input type="text" class="form-control price-input" value="${currentPrice.toFixed(2)}" style="width:100px;">`
            );
            cell.find('input').focus();
        });

        // Save edited price
        // Save edited price
$(document).on('blur', '.price-input', function() {
    const input = $(this);
    let newPrice = parseFloat(input.val());
    const cell = input.closest('.price-cell');

    if (isNaN(newPrice)) newPrice = 0; // فقط تحقق من NaN، السماح بالسالب

    cell.text(newPrice.toFixed(2));
    updateTotal();
});


        $(document).on('keypress', '.price-input', function(e) {
            if (e.which === 13) {
                $(this).blur();
            }
        });

        // Remove product row
        $(document).on('click', '.remove-btn', function() {
            $(this).closest('tr').remove();
            updateTotal();
        });

        // Update total when discount changes
        $('#discountInput').on('input', function() {
            updateTotal();
        });

        function updateTotal() {
            let total = 0;

            $('#scannedProductsTable tbody tr').each(function() {
                let price = parseFloat($(this).find('.price-cell').text()) || 0;
                total += price;
            });

            let discount = parseFloat($('#discountInput').val()) || 0;
            let finalTotal = total - discount;
            if (finalTotal < 0) finalTotal = 0;

            $('#totalAmount').val(finalTotal.toFixed(2));
        }

        // استدعاء تحديث التوتال عند تحميل الصفحة لو فيها عناصر
        $(document).ready(function() {
            updateTotal();
        });

        // حدث عند تعديل الخصم
        $('#discountInput').on('input', function() {
            updateTotal();
        });


        // Hold bill form submit
        $('#holdBillForm').on('submit', function(e) {
            e.preventDefault();

            const name = $('input[name="name"]').val();
            const phone_number = $('input[name="phone_number"]').val();
            const reference = $('input[name="reference"]').val();
            const total_price = parseFloat($('#totalAmount').val()) || 0;

            const products = [];

            $('#scannedProductsTable tbody tr').each(function() {
                const row = $(this);
                const product_id = row.data('product-id');
                const child_id = row.data('child-id');
                const price = parseFloat(row.find('.price-cell').text()) || 0;

                if (product_id && child_id) {
                    products.push({
                        product_id,
                        child_id,
                        price,
                        quantity: 1
                    });
                }
            });

            if (products.length === 0) {
                alert('Bill is empty!');
                return;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route('pos.hold-bill') }}',
                method: 'POST',
                data: {
                    name,
                    phone_number,
                    reference,
                    status: 'unpaid',
                    total_price,
                    'products': products
                },
                success: function(response) {
                    alert('Bill saved!');
                    $('#holdBillModal').modal('hide');
                    location.reload();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('error');
                }
            });
        });

        // Checkout button
        $('#checkoutBtn').on('click', function(e) {
            e.preventDefault();

            const total_price = parseFloat($('#totalAmount').val())*-1 || 0;

            const products = [];

            $('#scannedProductsTable tbody tr').each(function() {
                const row = $(this);
                const product_id = row.data('product-id');
                const child_id = row.data('child-id');
                const price = parseFloat(row.find('.price-cell').text()) || 0;

                if (product_id && child_id) {
                    products.push({
                        product_id,
                        child_id,
                        price,
                        quantity: 1
                    });
                }
            });

            if (products.length === 0) {
                alert('Bill is empty!');
                return;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route('pos.returnBill') }}',
                method: 'POST',
                data: {
                    status: 'paid',
                    total_price,
                    'products': products
                },
         success: function(response) {
                const bill_id = response.bill_id || @json($bill->id ?? '');
                Swal.fire({
                    title: 'Item Returned!',
                    text: 'Do you want to print the bill?',
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed && bill_id) {
                        window.open(`{{ url('pos/bill/print') }}/${bill_id}`, '_blank');
                    } else if (result.isConfirmed && !bill_id) {
                        Swal.fire('Error', 'Bill ID is missing!', 'error');
                    }
                    location.reload();
                });
            },
            error: function(xhr) { console.error(xhr.responseText); Swal.fire('Error', 'Something went wrong!', 'error'); }
        });
    });
    

        // save button
        $('#saveBillBtn').on('click', function(e) {

            e.preventDefault();

            const total_price = parseFloat($('#totalAmount').val()) || 0;
            const bill_number = @json($bill->bill_number ?? '');

            const products = [];

            $('#scannedProductsTable tbody tr').each(function() {
                const row = $(this);
                const product_id = row.data('product-id');
                const child_id = row.data('child-id');
                const price = parseFloat(row.find('.price-cell').text()) || 0;

                if (product_id && child_id) {
                    products.push({
                        bill_number,
                        product_id,
                        child_id,
                        price,
                        quantity: 1
                    });
                }
            });

            if (products.length === 0) {
                alert('Bill is empty!');
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route('pos.save-bill') }}', // تأكد من تعديل هذا الراوت حسب اللوجيك عندك
                method: 'POST',
                data: {
                    status: 'unpaid', // أو حسب اللوجيك
                    total_price,
                    'products': products
                },
                success: function(response) {
                    alert('Bill saved!');
                    window.location.href = "{{ route('pos.index') }}";
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('Error saving bill!');
                }
            });
        });

        // save button
        $('#checkoutSavedBtn').on('click', function(e) {

            e.preventDefault();


            const bill_number = @json($bill->bill_number ?? '');



            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route('pos.checkoutSaved') }}', // تأكد من تعديل هذا الراوت حسب اللوجيك عندك
                method: 'POST',
                data: {
                    status: 'paid', // أو حسب اللوجيك
                    bill_number,

                },
                success: function(response) {
                    alert('Checked out!');
                    window.location.href = "{{ route('pos.index') }}";
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('Error saving bill!');
                }
            });
        });
    </script>
@endpush
