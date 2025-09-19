@extends('layouts.pos')
@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="main-content-wrap">
                <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                    <h3>Bills</h3>
                    <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                        <li>
                            <a href="{{ route('pos.index') }}">
                                <div class="text-tiny">Dashboard</div>
                            </a>
                        </li>
                        <li>
                            <i class="icon-chevron-right"></i>
                        </li>
                        <li>
                            <div class="text-tiny">bills</div>
                        </li>
                    </ul>
                </div>

                <div class="wg-box">
                    <div class="flex items-center justify-between gap10 flex-wrap">
                        <div class="wg-filter flex-grow">
                            <form class="form-search">
                                <fieldset class="name">
                                    <input type="text" id="searchInput" placeholder="Search by Reference..."
                                        class="form-control mb-3">
                                    <a href="{{ route('pos.paidBills') }}">Get Paid Bills</a>

                                </fieldset>
                                <div class="button-submit">
                                    <button class="" type="submit"><i class="icon-search"></i></button>
                                </div>
                            </form>
                        </div>
                        {{-- <a class="tf-button style-1 w208" href="{{ route('admin.brands.add') }}"><i
                                class="icon-plus"></i>Add new</a> --}}
                    </div>
                    <div class="wg-table table-all-user">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Reference Number</th>
                                        <th>Name</th>
                                        <th>Phone Number</th>
                                        <th>Date</th>
                                        <th>Total Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bills as $bill)
                                        {{-- @if ($bill->status == 'unpaid') --}}
                                        <tr>
                                            <td>{{ $bill->reference }}</td>
                                            <td>{{ $bill->name }}</td>
                                            <td>{{ $bill->phone_number }}</td>
                                            <td>{{ $bill->updated_at }}</td>
                                            <td>{{ $bill->total_price }}</td>
                                            <td>
                                                <div class="list-icon-function">
                                                    <div class="list-icon-function">
                                                        <!-- زر عرض الفاتورة -->
                                                        <a href="{{ route('pos.index', ['bill_id' => $bill->id]) }}"
                                                            class="btn btn-primary">
                                                            View Bill Items
                                                        </a>
                                                        <a href="{{ route('pos.bill.print', $bill->id) }}" target="_blank"
                                                            class="btn btn-primary">Print Bill</a>
                                                        <form action="{{ route('pos.bill.delete', ['id' => $bill->id]) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="item text-danger delete">
                                                                <i class="icon-trash-2"></i>

                                                            </div>


                                                        </form>
                                                    </div>
                                            </td>
                                        </tr>
                                        {{-- @endif --}}
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                        <div class="divider"></div>
                        <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">

                        </div>
                    </div>
                </div>
            </div>
        </div>


       
    </div>
@endsection

@push('scripts')
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


        $(function() {
            $(".delete").on('click', function(e) {
                e.preventDefault();
                var selectedForm = $(this).closest('form');
                swal({
                    title: "Are you sure?",
                    text: "You want to delete this record?",
                    type: "warning",
                    buttons: ["No!", "Yes!"],
                    confirmButtonColor: '#dc3545'
                }).then(function(result) {
                    if (result) {
                        selectedForm.submit();
                    }
                });
            });
        });
    </script>
@endpush
