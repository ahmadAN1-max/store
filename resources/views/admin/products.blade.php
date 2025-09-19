@extends('layouts.admin')
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
                            <a href="{{ route('admin.index') }}">
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
                            <form class="form-search">
                                <fieldset class="name">
                                    <input type="text" placeholder="Search by SKU here..." id="searchInput" name="search"
                                        aria-required="true" />
                                </fieldset>
                                <div class="button-submit">
                                    <button class="" type="submit"><i class="icon-search"></i></button>
                                </div>
                            </form>
                        </div>
                        <a class="tf-button style-1 w208" href="{{ route('admin.product.add') }}"><i
                                class="icon-plus"></i>Add new</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" style="table-layout: auto; width: 100%;">
                            <thead>
                                <tr>

                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>SalePrice</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Featured</th>
                                    <th>Stock</th>
                                    <th>Quantity</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                
                                    <tr>

                                        <td class="pname" style="  white-space: nowrap;">
                                            @if($product->image)
                                            <div class="image">
                                                <img src="{{ asset('uploads/products/thumbnails/'.$product->image) }}"
                                                    alt="" class="image">
                                            </div>
                                            @endif
                                            <div class="name">
                                                <a href="#" class="body-title-2">{{ $product->name }}</a>
                                                <div class="text-tiny mt-3">{{ $product->slug }}</div>
                                            </div>
                                        </td>
                                        <td style="  white-space: nowrap;">${{ $product->regular_price }}</td>
                                        <td style="  white-space: nowrap;">${{ $product->sale_price }}</td>
                                        <td style="  white-space: nowrap;">{{ $product->SKU }}</td>
                                                       <td style="  white-space: nowrap;">  
                                        @if ($product->categories && $product->categories->count())
                                   
                                            {{ $product->categories->pluck('name')->join(', ') }}
                                            @endif
                                        </td>
                                        <td style="  white-space: nowrap;">{{ $product->brand->name }}</td>
                                        <td style="  white-space: nowrap;">{{ $product->featured == 0 ? 'No' : 'Yes' }}
                                        </td>
                                        <td style="  white-space: nowrap;">{{ $product->stock_status }}</td>
                                        <td style="  white-space: nowrap;">{{ $product->quantity }}</td>
                                        <td style="  white-space: nowrap;">
                                            <div class="list-icon-function">
                                                {{-- <div class="item eye">
                                                    <i class="icon-eye"></i>
                                                </div> --}}
                                                <a href="{{ route('admin.product.edit', ['id' => $product->id]) }}">
                                                    <div class="item edit">
                                                        <i class="icon-edit-3"></i>
                                                    </div>
                                                </a>
                                                <form action="{{ route('admin.product.delete', ['id' => $product->id]) }}"
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
            const skuCell = row.cells[3]; 
            const skuText = skuCell.textContent || skuCell.innerText;

            if (skuText.toLowerCase().indexOf(filter) > -1) {
                row.style.display = "";
            } else {
                row.style.display = "none"; 
            }
        }
    });
});
</script>

@endpush
