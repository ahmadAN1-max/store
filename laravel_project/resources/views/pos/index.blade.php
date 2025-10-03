@extends('layouts.pos')

@section('content')
<style>
    :root {
        --primary-color: #0d6efd;
        --secondary-color: #f8f9fa;
        --text-dark: #212529;
        --border-radius: 10px;
        --font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        font-family: var(--font-family);
        background-color: #f5f7fa;
        color: var(--text-dark);
    }

    .container {
        max-width: 1000px;
        margin: auto;
        padding: 2rem 1rem;
        background-color: white;
        border-radius: var(--border-radius);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    h2 {
        font-size: 2rem;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 30px;
        text-align: center;
    }

    input.form-control {
        border-radius: var(--border-radius);
        border: 1px solid #ced4da;
        transition: 0.3s;
    }
    input.form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(13,110,253,0.2);
    }

    #employee {
        width: 150px;
        font-size: 0.9rem;
        padding: 6px 10px;
    }

    table.table {
        width: 100%;
        border-spacing: 0;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    table thead {
        background-color: var(--primary-color);
        color: #fff;
    }

    table th, table td {
        padding: 12px;
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

    .modal-backdrop.show {
        background-color: rgba(0,0,0,0.5);
        z-index: 1040;
    }
    .modal.fade .modal-dialog {
        z-index: 1050;
    }

    #discountInput, #totalAmount {
        font-size: 1.1rem;
        padding: 10px;
        border-radius: var(--border-radius);
    }

    @media (max-width: 768px) {
        h2 { font-size: 1.6rem; }
        .btn { width: 100%; margin-bottom: 10px; }
        .under-checkout-buttons { flex-direction: column; }
    }

    .d-flex-row-gap {
        display: flex;
        gap: 10px;
        align-items: center;
    }

</style>

<div class="container">
<br><br><br><br><br><br><br>
    <div class="d-flex d-flex-row-gap mb-3">
        <input type="text" id="employee" name="employee" class="form-control" placeholder="Employee">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customersModal">
            Select Customer
        </button>
    </div>

    <div id="selectedCustomer" class="alert alert-info d-none mt-3">
        <strong>Customer name:</strong> <span id="customerName"></span> <br>
        <strong>Phone Number:</strong> <span id="customerPhone"></span>
    </div>

    <h2>POS Dashboard</h2>

    <input type="text" id="barcodeInput" class="form-control" placeholder="Scan Barcode..." autofocus style="margin-top:20px;">

    <div style="height: 280px; overflow-y: auto; margin-top: 15px;">
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
                            <td class="price-cell">{{ number_format($item->price,2) }}</td>
                            <td class="sale-price-cell">{{ number_format($item->salePrice,2) }}</td>
                            <td><button class="btn btn-danger btn-sm remove-btn">❌</button></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between mt-3">
        @if (!empty($billItems))
            <button class="btn btn-success" id="checkoutSavedBtn" style="flex:1; padding: 15px; font-size:1.2rem;">SUBTOTAL</button>
            <button class="btn btn-warning" style="flex:1; margin-left:10px;" data-bs-toggle="modal" data-bs-target="#holdBillModal">
                Save
            </button>
        @else
            <button class="btn btn-success" id="checkoutBtn" style="flex:1; padding: 15px; font-size:1.2rem;">SUBTOTAL</button>
            <button class="btn btn-warning" style="flex:1; margin-left:10px;" data-bs-toggle="modal" data-bs-target="#holdBillModal">
                Hold Bill
            </button>
        @endif
        <button class="btn btn-secondary" id="clearBtn" style="margin-left:10px;">Clear</button>
    </div>

    <div class="row g-2 mt-3">
        <div class="col-4">
            <input type="number" id="discountInput" class="form-control" placeholder="Discount">
        </div>
        <div class="col-8">
            <input type="text" id="totalAmount" class="form-control" readonly placeholder="Total" style="font-weight:bold; font-size:1.2rem;">
        </div>
    </div>
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
                <input type="text" id="customerSearch" class="form-control mb-3" placeholder="Search By Name or Phone Number">
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
                                            data-name="{{ $customer->name }}" data-phone="{{ $customer->phone }}" data-bs-dismiss="modal">
                                            Select
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="window.location.href='{{ route('pos.customer-add') }}'">Add Customer</button>
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
                    <input type="text" id="holdName" name="name" value="{{ $bill->name ?? '' }}" class="form-control mb-2" placeholder="Customer Name">
                    <input type="text" id="holdPhone" name="phone_number" value="{{ $bill->phone_number ?? '' }}" class="form-control mb-2" placeholder="Phone Number">
                    <input type="text" name="reference" value="{{ $bill->reference ?? '' }}" class="form-control" placeholder="Reference">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Save Hold</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // هنا ضيف السكريبت اللي عندك للـ POS كما هو بدون تغيير أي فانكشن
</script>
@endsection


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- customers table --}}
    <script>
$(document).ready(function() {
    // AJAX Setup CSRF Token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let scannedProducts = [];
    let bill_number = @json(isset($bill) ? $bill->bill_number : '');

    // === اختيار العميل ===
    function selectCustomer(name, phone) {
        $('#customerName').text(name);
        $('#customerPhone').text(phone);
        $('#selectedCustomer').removeClass('d-none');

        // اعبي الفورمات
        $('#holdName').val(name);
        $('#holdPhone').val(phone);
        $('#holdBillModal input[name="name"]').val(name);
        $('#holdBillModal input[name="phone_number"]').val(phone);
    }

    $(document).on('click', '.select-customer', function() {
        let name = $(this).data('name');
        let phone = $(this).data('phone');
        selectCustomer(name, phone);
    });

    // فلترة العملاء
    $('#customerSearch').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('#customersTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // === Clear scanned products ===
    $('#clearBtn').on('click', function() {
        scannedProducts = [];
        $('#scannedProductsTable tbody').empty();
        $('#discountInput').val('');
        $('#totalAmount').val('');
        $('#barcodeInput').val('').focus();
    });

    // === Barcode scanning ===
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

                // عد عدد الصفوف الموجودة لهذا المنتج بالفعل
                const alreadyScanned = $('#scannedProductsTable tbody tr[data-child-id="' + child.child_id + '"]').length;

                if (alreadyScanned >= child.quantity) {
                    alert('Cannot scan more than available quantity!');
                    $('#barcodeInput').val('').focus();
                    return;
                }

                // أضف صف جديد لكل scan
                scannedProducts.push(child);

                $('#scannedProductsTable tbody').append(`
                    <tr data-product-id="${child.product_id}" data-child-id="${child.child_id}" data-quantity="1">
                        <td style="text-align: center">${child.name}</td>
                        <td style="text-align: center">${child.barcode}</td>
                        <td style="text-align: center">${child.size}</td>
                        <td class="price-cell" style="text-align: center" data-base-price="${parseFloat(child.price).toFixed(2)}">
                            ${parseFloat(child.price).toFixed(2)}
                        </td>
                        <td class="sale-price-cell" style="text-align: center">${parseFloat(child.salePrice).toFixed(2)}</td>
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

    // === تعديل السعر ===
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

    $(document).on('blur', '.price-input', function() {
        const input = $(this);
        let newPrice = parseFloat(input.val()) || 0;
        const cell = input.closest('.price-cell');
        const basePrice = parseFloat(cell.data('base-price')) || 0;

        let isAdmin = {{ $admin ? 'true' : 'false' }};
        let maxDiscount = {{ $maxDiscount }};
        let minAllowed = basePrice - (basePrice * maxDiscount / 100);

        if (!isAdmin && newPrice < minAllowed) {
            alert("Max discount is " + maxDiscount + "%");
            newPrice = basePrice;
        }

        cell.text(newPrice.toFixed(2));
        updateTotal();
    });

    // === إزالة صف ===
    $(document).on('click', '.remove-btn', function() {
        $(this).closest('tr').remove();
        updateTotal();
    });

    // === تحديث المجموع الكلي ===
    function updateTotal() {
        let total = 0;
        $('#scannedProductsTable tbody tr').each(function() {
            const row = $(this);
            const regularPrice = parseFloat(row.find('.price-cell').text()) || 0;
            const quantity = parseInt(row.data('quantity')) || 1;
            total += regularPrice * quantity;
        });

        let discount = parseFloat($('#discountInput').val()) || 0;
        let finalTotal = total - discount;
        if (finalTotal < 0) finalTotal = 0;

        $('#totalAmount').val(finalTotal.toFixed(2));
    }

    $('#discountInput').on('input', function() {
        updateTotal();
    });

    // === Hold Bill ===
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
            const quantity = parseInt(row.data('quantity')) || 1;

            if (product_id) {
                products.push({ product_id, child_id, price, quantity });
            }
        });

        $.post('{{ route("pos.hold-bill") }}', {
            bill_number, name, phone_number, reference, status: 'unpaid', total_price, employee, products
        }, function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Bill saved!',
                showConfirmButton: false,
                timer: 1500
            });
            $('#holdBillModal').modal('hide');
            window.location.href = "{{ route('pos.index') }}";
        }).fail(function(xhr) {
            console.error(xhr.responseText);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Check console.' });
        });
    });

    // === Checkout ===
    $('#checkoutBtn').on('click', function(e) {
        e.preventDefault();

        const total_price = parseFloat($('#totalAmount').val()) || 0;
        const name = $('input[name="name"]').val();
        const phone_number = $('input[name="phone_number"]').val();
        const employee = $('input[name="employee"]').val();

        const products = [];
        $('#scannedProductsTable tbody tr').each(function() {
            const row = $(this);
            const product_id = row.data('product-id');
            const child_id = row.data('child-id');
            const price = parseFloat(row.find('.price-cell').text()) || 0;
            const quantity = parseInt(row.data('quantity')) || 1;

            if (product_id) {
                products.push({ product_id, child_id, price, quantity });
            }
        });

        if (products.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Bill is empty!', text: 'Please scan products before checkout.' });
            return;
        }

        $.post('{{ route("pos.checkout") }}', { status: 'paid', total_price, name, phone_number, employee, products }, function(response) {
            const bill_id = response.bill_id || bill_number;
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
                    window.open(`{{ url("pos/bill/print") }}/${bill_id}`, '_blank');
                }
                window.location.href = "{{ route('pos.index') }}";
            });
        }).fail(function(xhr) {
            console.error(xhr.responseText);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Check console.' });
        });
    });

    // === Checkout saved bill ===
    $('#checkoutSavedBtn').on('click', function(e) {
        e.preventDefault();
        $.post('{{ route("pos.checkoutSaved") }}', { bill_number }, function(response) {
            const bill_id = response.bill_id || bill_number;
            Swal.fire({
                icon: 'success',
                title: 'Checked Out!',
                showCancelButton: true,
                confirmButtonText: 'Print Bill',
                cancelButtonText: 'Close',
                confirmButtonColor: '#0b5ed7'
            }).then((result) => {
                if (result.isConfirmed && bill_id) {
                    window.open(`{{ url("pos/bill/print") }}/${bill_id}`, '_blank');
                }
                window.location.href = "{{ route('pos.index') }}";
            });
        }).fail(function(xhr) {
            console.error(xhr.responseText);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Check console.' });
        });
    });

    // === عند تحميل الصفحة ===
    updateTotal();
});
</script>

@endpush
