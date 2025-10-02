@extends('layouts.pos')
@section('content')
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #f8f9fa;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --text-dark: #212529;
            --border-radius: 10px;
            --font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--secondary-color);
            color: var(--text-dark);
        }

        .container {
            max-width: 1000px;
            margin: auto;
            padding: 2rem 1rem;
            background-color: #fff;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 30px;
            text-align: center;
        }

        input.form-control,
        select.form-select {
            padding: 10px 12px;
            border-radius: var(--border-radius);
            border: 1px solid #ced4da;
        }

        input.form-control:focus,
        select.form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.15);
        }

        table.table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background-color: var(--primary-color);
            color: #fff;
        }

        table th,
        table td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }

        table tbody tr:hover {
            background-color: #f1f3f5;
        }

        .btn {
            border-radius: var(--border-radius);
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: #157347;
        }

        .btn-warning {
            background-color: var(--warning-color);
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
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        #discountInput,
        #totalAmount {
            font-size: 1.1rem;
            padding: 10px;
            border-radius: var(--border-radius);
        }

        .bottom-page {
            margin-top: 20px;
            text-align: center;
            color: #6c757d;
        }

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
    </style>
    <br><br><br><br><br><br><br><br><br><br><br>
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" id="employee" name="employee" class="form-control" placeholder="Employee" required>
            </div>
            <div class="col-md-6">
                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#customersModal">Select
                    Customer</button>
            </div>
        </div>

        <div id="selectedCustomer" class="alert alert-info d-none">
            <strong>Customer name:</strong> <span id="customerName"></span> <br>
            <strong>Phone Number:</strong> <span id="customerPhone"></span>
        </div>

        <h2>POS Dashboard</h2>

        <input type="text" id="barcodeInput" class="form-control mb-3" placeholder="Scan Barcode..." autofocus>

        <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
            <table id="scannedProductsTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Barcode</th>
                        <th>Size</th>
                        <th>Price ($)</th>
                        <th>Sale Price ($)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($billItems))
                        @foreach ($billItems as $item)
                            <tr data-product-id="{{ $item->product_id }}" data-child-id="{{ $item->child_id }}">
                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                <td>{{ $item->child->barcode ?? 'N/A' }}</td>
                                <td>{{ $item->child->sizes ?? 'N/A' }}</td>
                                <td class="price-cell">{{ number_format($item->price, 2) }}</td>
                                <td class="sale-price-cell">{{ number_format($item->salePrice, 2) }}</td>
                                <td><button class="btn btn-danger btn-sm remove-btn">❌</button></td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="row mt-3">
            <div class="col-md-6 mb-2">
                @if ($admin)
                    <input type="number" id="discountInput" class="form-control" placeholder="Discount">
                @endif
            </div>
            <div class="col-md-6 mb-2">
                <input type="text" id="totalAmount" class="form-control" readonly placeholder="Total">
            </div>
        </div>

        <div class="d-flex justify-content-between mt-3 flex-wrap gap-2">
            <button class="btn btn-success flex-fill" id="checkoutBtn">SUBTOTAL</button>
            <button class="btn btn-warning flex-fill" data-bs-toggle="modal" data-bs-target="#holdBillModal"
                onClick="giveHoldValues()">Hold Bill</button>
            <button class="btn btn-secondary flex-fill" id="clearBtn">Clear</button>
        </div>
    </div>

    <div class="bottom-page">
        <div class="body-text">Copyright © 2025 Sarah's Palace</div>
    </div>

    {{-- Customers Modal --}}
    <div class="modal fade" id="customersModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Find Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="customerSearch" class="form-control mb-3"
                        placeholder="Search By Name or Phone">
                    <div class="table-responsive" style="max-height:400px; overflow-y:auto;">
                        <table class="table table-bordered table-hover" id="customersTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Select</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $customer)
                                    <tr>
                                        <td>{{ $customer->name }}</td>
                                        <td>{{ $customer->phone }}</td>
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm select-customer"
                                                data-name="{{ $customer->name }}" data-phone="{{ $customer->phone }}"
                                                data-bs-dismiss="modal">Select</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="window.location.href='{{ route('pos.customer-add') }}'">Add
                        Customer</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
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
                        <input type="text" id="holdName" value="{{ $bill->name ?? '' }}" name="name"
                            class="form-control mb-2" placeholder="Customer Name">
                        <input type="text" id="holdPhone" value="{{ $bill->phone_number ?? '' }}" name="phone_number"
                            class="form-control mb-2" placeholder="Phone Number">
                        <input type="text" value="{{ $bill->reference ?? '' }}" name="reference"
                            class="form-control" placeholder="Reference">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning w-100">Save Hold</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- customers table --}}
    <script>
        $('.select-customer').on('click', function() {
            let name = $(this).data('name');
            let phone = $(this).data('phone');

            $('#customerName').text(name);
            $('#customerPhone').text(phone);
            $('#selectedCustomer').removeClass('d-none');

            // اعبي الفورم في Hold أو Save
            $('#holdName').val(name);
            $('#holdPhone').val(phone);
            $('#holBillModal input[name="name"]').val(name);
            $('#holdBillModal input[name="phone_number"]').val(phone);
        });



        // فلترة العملاء
        $('#customerSearch').on('keyup', function() {
            let value = $(this).val().toLowerCase();
            $('#customersTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });


        // اختيار العميل
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('select-customer')) {
                let name = e.target.getAttribute('data-name');
                let phone = e.target.getAttribute('data-phone');

                document.getElementById('customerName').textContent = name;
                document.getElementById('customerPhone').textContent = phone;

                document.getElementById('selectedCustomer').classList.remove('d-none');
            }
        });
    </script>


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
                    if (!child) {
                        alert('Product not found!');
                        $('#barcodeInput').val('').focus();
                        return;
                    }

                    // إذا الكمية خالصة أو السعر بالسالب
                    if (parseFloat(child.price) <= 0) {
                        alert('Quantity finished for this product');
                        $('#barcodeInput').val('').focus();
                        return;
                    }

                    scannedProducts.push(child);

                    $('#scannedProductsTable tbody').append(`
                    <tr data-product-id="${child.product_id}" data-child-id="${child.child_id}">
                    <td style="text-align: center">${child.name}</td>
                    <td style="text-align: center">${child.barcode}</td>
                    <td style="text-align: center">${child.size}</td>
                    <td class="price-cell" style="text-align: center" data-base-price="${parseFloat(child.price).toFixed(2)}">
                        ${parseFloat(child.price).toFixed(2)}
                    </td><td class="sale-price-cell" style="text-align: center">${parseFloat(child.salePrice).toFixed(2)}</td>
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

            cell.html(`
               <input type="text" class="form-control price-input" value="${currentPrice.toFixed(2)}" style="width:100px;">
                <span class="price-warning" style="color:red; font-size:12px; display:none;"></span>
             `);
            cell.find('input').focus();
        });



        // عند الخروج من input نحفظ السعر النهائي
        $(document).on('blur', '.price-input', function() {
            const input = $(this);
            let newPrice = parseFloat(input.val()) || 0;
            const cell = input.closest('.price-cell');


            const basePrice = parseFloat(cell.data('base-price')) || 0;
            console.log('newPrice:', newPrice, 'basePrice:', basePrice);

            let isAdmin = {{ $admin ? 'true' : 'false' }};
            let maxDiscount = {{ $maxDiscount }};
            let minAllowed = basePrice - (basePrice * maxDiscount / 100);
            console.log('isAdmin:', isAdmin, 'minAllowed:', minAllowed);

            if (!isAdmin && newPrice < minAllowed) {
                alert("Max discount is " + maxDiscount + "%");
                newPrice = basePrice;
            }


            //  alert("np"+newPrice +"bp"+ basePrice );
            cell.text(newPrice.toFixed(2));
            updateTotal();


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
                const salePrice = parseFloat($(this).find('.sale-price-cell').text()) || 0;
                const regularPrice = parseFloat($(this).find('.price-cell').text()) || 0;
                let finalPrice = regularPrice;


                total += finalPrice;
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

        let bill_number = @json(isset($bill) ? $bill->bill_number : '');

        // Hold bill form submit
        $('#holdBillForm').on('submit', function(e) {
            e.preventDefault();
            if (!bill_number) {
                bill_number = 'BILL-' + Date.now() + '-' + Math.floor(Math.random() * 9000 + 1000);
            }

            const name = $('input[name="name"]').val();
            const phone_number = $('input[name="phone_number"]').val();
            const reference = $('input[name="reference"]').val();
            const total_price = parseFloat($('#totalAmount').val()) || 0;
            const employee = $('input[name="employee"]').val();

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

            // if (products.length === 0) {
            //     Swal.fire({
            //         icon: 'warning',
            //         title: 'Bill is empty!',
            //         text: 'Please add products before holding the bill.'
            //     });
            //     return;
            // }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route('pos.hold-bill') }}',
                method: 'POST',
                data: {
                    bill_number,
                    name,
                    phone_number,
                    reference,
                    status: 'unpaid',
                    total_price,
                    employee,
                    products
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Bill saved!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#holdBillModal').modal('hide');
                    location.reload();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Check console for details.'
                    });
                }
            });
        });

        // Checkout button
        $(document).ready(function() {
            $('#checkoutBtn').on('click', function(e) {
                e.preventDefault();

                const total_price = parseFloat($('#totalAmount').val()) || 0;
                const products = [];
                const phone_number = $('input[name="phone_number"]').val();
                const name = $('input[name="name"]').val();
                const employee = $('input[name="employee"]').val();

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
                    Swal.fire({
                        icon: 'warning',
                        title: 'Bill is empty!',
                        text: 'Please scan products before checkout.'
                    });
                    return;
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ route('pos.checkout') }}',
                    method: 'POST',
                    data: {
                        status: 'paid',
                        total_price,
                        name,
                        phone_number,
                        employee,
                        products
                    },
                    success: function(response) {
                        const bill_id = response.bill_id || @json($bill->id ?? '');

                        Swal.fire({
                            icon: 'success',
                            title: 'Checked Out!',
                            text: 'The bill has been processed.',
                            showCancelButton: true,
                            confirmButtonText: 'Print Bill',
                            cancelButtonText: 'Close',
                            confirmButtonColor: '#0b5ed7'
                        }).then((result) => {
                            if (result.isConfirmed && bill_id) {
                                window.open(`{{ url('pos/bill/print') }}/${bill_id}`,
                                    '_blank');
                            }
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong. Check console for details.'
                        });
                    }
                });
            });
        });

        //        // Save bill button
        // $('#saveBillBtn').on('click', function(e) {
        //     e.preventDefault();

        //     const total_price = parseFloat($('#totalAmount').val()) || 0;
        //     let bill_number = @json(isset($bill) ? $bill->bill_number : '');
        //     const products = [];

        //     $('#scannedProductsTable tbody tr').each(function() {
        //         const row = $(this);
        //         const product_id = row.data('product-id');
        //         const child_id = row.data('child-id') || null; // بدل منع المنتجات بدون طفل
        //         const price = parseFloat(row.find('.price-cell').text()) || 0;
        //         const salePrice = parseFloat(row.find('.sale-price-cell').text()) || 0;

        //         if (product_id) { // يسمح بكل المنتجات
        //             products.push({
        //                 bill_number: bill_number,
        //                 product_id: product_id,
        //                 child_id: child_id,
        //                 price: price,
        //                 salePrice: salePrice,
        //                 quantity: 1
        //             });
        //         }
        //     });

        //     if (products.length === 0) {
        //         Swal.fire({
        //             icon: 'warning',
        //             title: 'Bill is empty!',
        //             text: 'Please add products before saving.'
        //         });
        //         return;
        //     }

        //     $.ajaxSetup({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         }
        //     });

        //     $.ajax({
        //         url: '{{ route('pos.save-bill') }}',
        //         method: 'POST',
        //         data: {
        //             bill_number: bill_number, // إرسال رقم الفاتورة
        //             status: 'unpaid',
        //             total_price: total_price,
        //             products: products
        //         },
        //         success: function(response) {
        //             Swal.fire({
        //                 icon: 'success',
        //                 title: 'Bill saved!',
        //                 showConfirmButton: false,
        //                 timer: 2000
        //             }).then(() => window.location.href = "{{ route('pos.index') }}");
        //         },
        //         error: function(xhr) {
        //             console.error(xhr.responseText);
        //             Swal.fire({
        //                 icon: 'error',
        //                 title: 'Error saving bill!',
        //                 text: 'Check console for details.'
        //             });
        //         }
        //     });
        // });



        // Checkout saved bill
        $('#checkoutSavedBtn').on('click', function(e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route('pos.checkoutSaved') }}',
                method: 'POST',
                data: {
                    bill_number
                },
                success: function(response) {
                    const bill_id = response.bill_id || @json($bill->id ?? '');

                    Swal.fire({
                        icon: 'success',
                        title: 'Checked Out!',
                        showCancelButton: true,
                        confirmButtonText: 'Print Bill',
                        cancelButtonText: 'Close',
                        confirmButtonColor: '#0b5ed7'
                    }).then((result) => {
                        if (result.isConfirmed && bill_id) {
                            window.open(`{{ url('pos/bill/print') }}/${bill_id}`, '_blank');
                        }
                        window.location.href = "{{ route('pos.index') }}";
                    });
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Check console for details.'
                    });
                }
            });
        });
    </script>
@endpush
