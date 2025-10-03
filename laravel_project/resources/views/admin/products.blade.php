@extends('layouts.admin')
@section('content')
<style>
    /* الجدول */
    .table-striped th,
    .table-striped td {
        white-space: nowrap;
        vertical-align: middle;
        text-align: center;
    }

    .table {
        width: 100%;
        min-width: max-content;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .table thead {
        background-color: #0d6efd;
        color: #fff;
        font-weight: 600;
    }

    .table tbody tr:hover {
        background-color: #f1f3f5;
    }

    /* الصور */
    .pname .image {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        overflow: hidden;
        margin-right: 10px;
        display: inline-block;
        vertical-align: middle;
    }

    .pname .image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pname .name {
        display: inline-block;
        vertical-align: middle;
        text-align: left;
    }

    .pname .name a {
        font-weight: 600;
        color: #212529;
        text-decoration: none;
    }

    .pname .name a:hover {
        color: #0d6efd;
    }

    .pname .text-tiny {
        font-size: 0.75rem;
        color: #6c757d;
    }

    /* أزرار */
    .list-icon-function {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .list-icon-function .item {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.2s;
    }

    .list-icon-function .item.edit {
        background-color: #0d6efd;
        color: white;
    }

    .list-icon-function .item.edit:hover {
        background-color: #0b5ed7;
    }

    .list-icon-function .item.delete {
        background-color: #dc3545;
        color: white;
    }

    .list-icon-function .item.delete:hover {
        background-color: #c82333;
    }

    .wg-box {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    .wg-filter input {
        border-radius: 8px;
        padding: 8px 12px;
        width: 220px;
        border: 1px solid #ced4da;
        transition: 0.3s;
    }

    .wg-filter input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 2px rgba(13,110,253,0.2);
    }

    .tf-button {
        padding: 8px 16px;
        border-radius: 8px;
        background-color: #0d6efd;
        color: white;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
        text-decoration: none;
    }

    .tf-button:hover {
        background-color: #0b5ed7;
    }

    /* Breadcrumbs */
    .breadcrumbs li {
        list-style: none;
    }

    .breadcrumbs li a {
        text-decoration: none;
        color: #6c757d;
        font-size: 0.85rem;
    }

    .breadcrumbs li i {
        margin: 0 5px;
        color: #6c757d;
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
                    <li><i class="icon-chevron-right"></i></li>
                    <li><div class="text-tiny">All Products</div></li>
                </ul>
            </div>

            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap mb-3">
                    <div class="wg-filter flex-grow">
                        <form class="form-search">
                            <fieldset class="name">
                                <input type="text" placeholder="Search by SKU here..." id="searchInput" name="search" aria-required="true" />
                            </fieldset>
                            <div class="button-submit">
                                <button class="" type="submit"><i class="icon-search"></i></button>
                            </div>
                        </form>
                    </div>
                    <a class="tf-button style-1 w208" href="{{ route('admin.product.add') }}"><i class="icon-plus"></i>Add new</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>SalePrice</th>
                                <th>SKU</th>
                                {{-- <th>Category</th> --}}
                                <th>Brand</th>
                                <th>Published</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td class="pname" style="max-width: 180px;">

    @if($product->image)
        <div class="image" style="display:inline-block; vertical-align:middle; margin-right:8px;">
            <img src="{{ asset('uploads/products/thumbnails/'.$product->image) }}" 
                 alt="product" 
                 style="width:50px; height:50px; object-fit:cover; border-radius:4px;">
        </div>
    @endif

    <div class="name" style="display:inline-block; max-width:120px; vertical-align:middle; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
        <a href="#" class="body-title-2" title="{{ $product->name }}">
            {{ $product->name }}
        </a>
        <div class="text-tiny mt-1" title="{{ $product->slug }}">
            {{ $product->slug }}
        </div>
    </div>

</td>

                                    <td>${{ $product->regular_price }}</td>
                                    <td>${{ $product->sale_price }}</td>
                                    <td>{{ $product->SKU }}</td>
                                    {{-- <td>
                                        @if ($product->categories && $product->categories->count())
                                            {{ $product->categories->pluck('name')->join(', ') }}
                                        @endif
                                    </td> --}}
                                    <td>{{ $product->brand->name }}</td>
                                    <td>{{ $product->featured == 0 ? 'No' : 'Yes' }}</td>
                                    <td>{{ $product->quantity }}</td>
                                    <td>
                                        <div class="list-icon-function">
                                            <a href="{{ route('admin.product.edit', ['id' => $product->id]) }}">
                                                <div class="item edit"><i class="icon-edit-3"></i></div>
                                            </a>
                                            <form action="{{ route('admin.product.delete', ['id' => $product->id]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="item delete" style="border:none;background:none;"><i class="icon-trash-2"></i></button>
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
