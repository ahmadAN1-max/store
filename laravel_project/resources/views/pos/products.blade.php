@extends('layouts.pos')
@section('content')
    <style>
        .table-striped th,
        .table-striped td {
            white-space: nowrap;
        }

        .table {
            width: 100%;
            min-width: max-content;
        }
    </style>

    <div class="main-content">

        <div class="main-content-inner">
            <div class="main-content-wrap">
                <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                    <h3>All Products</h3>
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
                            <div class="text-tiny">All Products</div>
                        </li>
                    </ul>
                </div>

                <div class="wg-box">
                    <div class="flex items-center justify-between gap10 flex-wrap">
                        <div class="wg-filter flex-grow">
                            <form class="form-search" onsubmit="return false;">
                                <fieldset class="name">
                                    <input type="text" placeholder="Search by SKU or Barcode here..." id="searchInput"
                                        name="search" aria-required="true" autocomplete="off" />
                                </fieldset>
                                <div class="button-submit">
                                    <button class="" type="submit"><i class="icon-search"></i></button>
                                </div>
                            </form>
                        </div>
                        @if ($admin)
                        <a class="tf-button style-1 w208" href="{{ route('pos.product-add') }}"><i class="icon-plus"></i>Add
                            new</a>
                            @endif
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" style="table-layout: auto; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>SKU</th>
                                    <th>Sizes</th>
                                    <th>Quantity</th>
                                    @if ($admin)
                                    <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                
                                @foreach ($products as $product)
                             
                              
                                    @php
                                    
                                        // جمع جميع باركودات الأطفال في سلسلة نصية مفصولة بفواصل
                                        $barcodes = $product->children->pluck('barcode')->implode(',');
                                    @endphp
                                    <tr data-barcode="{{ strtolower($barcodes) }}">
                                        <td class="pname" style="white-space: nowrap;">
                                            @if ($product->image)
                                                <div class="image">
                                                    <img src="{{ asset('uploads/products/thumbnails') }}/{{ $product->image }}"
                                                        alt="image" class="image">
                                                </div>
                                            @endif

                                            <div class="name">
                                                <a href="#" class="body-title-2">{{ $product->name }}</a>
                                                <div class="text-tiny mt-3">{{ $product->slug }}</div>
                                            </div>
                                        </td>
                                        <td style="white-space: nowrap;">${{ $product->regular_price }}</td>

                                        <td style="white-space: nowrap;">{{ $product->SKU }}</td>
                                        <td>
                                            @foreach ($product->children as $child)
                                                @if ($child->quantity == 0)
                                                    <span class="badge bg-secondary"
                                                        style="text-decoration: line-through; opacity: 0.6;">
                                                        {{ $child->sizes }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-primary">
                                                        {{ $child->sizes }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </td>

                              
                                        <td style="white-space: nowrap;">{{ $product->quantity }}</td>
                                        @if ($admin)
                                        <td style="white-space: nowrap;">
                                            <div class="list-icon-function">
                                                <a href="{{ route('pos.product-edit', ['id' => $product->id]) }}">
                                                    <div class="item edit">
                                                        <i class="icon-edit-3"></i>
                                                    </div>
                                                </a>
                                                <form action="{{ route('pos.product.delete', ['id' => $product->id]) }}"
                                                    method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class="item text-danger delete" style="cursor: pointer;">
                                                        <i class="icon-trash-2"></i>
                                                    </div>
                                                </form>
                                            </div>
                                        </td>
                                        @endif
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById('searchInput');
            const table = document.querySelector('table tbody');
            const rows = table.getElementsByTagName('tr');

            searchInput.addEventListener('keyup', function() {
                const filter = searchInput.value.toLowerCase();

                for (let i = 0; i < rows.length; i++) {
                    const row = rows[i];
                    const nameCell = row.cells[0]; // عمود Sizes
                    const skuCell = row.cells[2]; // عمود SKU
                    const sizesCell = row.cells[3]; // عمود Sizes
                    const barcodeData = row.getAttribute('data-barcode') || '';

                    const nameText = nameCell.textContent || nameCell.innerText;
                    const skuText = skuCell.textContent || skuCell.innerText;
                    const sizesText = sizesCell.textContent || sizesCell.innerText;

                    if (
                        nameText.toLowerCase().indexOf(filter) > -1 ||
                        skuText.toLowerCase().indexOf(filter) > -1 ||
                        sizesText.toLowerCase().indexOf(filter) > -1 ||
                        barcodeData.toLowerCase().indexOf(filter) > -1
                    ) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                }
            });
        });
    </script>
@endpush
