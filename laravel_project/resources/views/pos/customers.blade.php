@extends('layouts.pos')
@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="main-content-wrap">
                <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                    <h3>customers</h3>
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
                            <div class="text-tiny">customers</div>
                        </li>
                    </ul>
                </div>

                <div class="wg-box">
                    <div class="flex items-center justify-between gap10 flex-wrap">
                        <div class="wg-filter flex-grow">
                            <form method="GET" action="{{ route('pos.customers') }}" class="form-search">
                                <fieldset class="name">
                                    <input type="text" id="searchInput" placeholder="Search here..." name="search"
                                        value="{{ request('search') }}">
                                </fieldset>
                                <div class="button-submit">
                                    <button type="submit"><i class="icon-search"></i></button>
                                </div>
                            </form>
                        </div>
                        <a class="tf-button style-1 w208" href="{{ route('pos.customer-add') }}"><i
                                class="icon-plus"></i>Add new</a>
                    </div>
                    <div class="wg-table table-all-user">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>

                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>City</th>
                                        <th>Address</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $customer)
                                        <tr>
                                            <td>{{ $customer->name }}</td>
                                            <td>{{ $customer->phone }}</td>
                                            <td>{{ $customer->city }}</td>
                                            <td>{{ $customer->address }}</td>
                                            <td>
                                                <div class="list-icon-function">
                                                    <a href="{{ route('pos.customer-edit', ['id' => $customer->id]) }}">
                                                        <div class="item edit">
                                                            <i class="icon-edit-3"></i>
                                                        </div>
                                                    </a>
                                                    <form
                                                        action="{{ route('pos.customer-delete', ['id' => $customer->id]) }}"
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
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                        <div class="divider"></div>
                        
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById('searchInput');
            const table = document.querySelector('table tbody');
            const rows = table.getElementsByTagName('tr');

            searchInput.addEventListener('keyup', function() {
                const filter = searchInput.value.toLowerCase();

                for (let i = 0; i < rows.length; i++) {
                    const row = rows[i];
                    const nameCell = row.cells[0]; // العمود الأول: الاسم
                    const phoneCell = row.cells[1]; // العمود الثاني: الفون

                    const nameText = nameCell.textContent || nameCell.innerText;
                    const phoneText = phoneCell.textContent || phoneCell.innerText;

                    // إذا الكلمة موجودة بالاسم أو الفون بيظهر الصف
                    if (
                        nameText.toLowerCase().indexOf(filter) > -1 ||
                        phoneText.toLowerCase().indexOf(filter) > -1
                    ) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
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
