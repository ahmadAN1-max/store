@extends('layouts.admin')
@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="main-content-wrap">
                <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                    <h3>Brands</h3>
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
                            <div class="text-tiny">Brands</div>
                        </li>
                    </ul>
                </div>

                <div class="wg-box">
                    <div class="flex items-center justify-between gap10 flex-wrap">
                        <div class="wg-filter flex-grow">
                            <form class="form-search">
                                <fieldset class="name">
                                    <input type="text" id="searchInput" placeholder="Search here..." class="" name="name"
                                        tabindex="2" value="" aria-required="true" required="">
                                </fieldset>
                                <div class="button-submit">
                                    <button class="" type="submit"><i class="icon-search"></i></button>
                                </div>
                            </form>
                        </div>
                        <a class="tf-button style-1 w208" href="{{ route('admin.brands.add') }}"><i
                                class="icon-plus"></i>Add new</a>
                    </div>
                    <div class="wg-table table-all-user">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="brandTable">
                                <thead>
                                    <tr>

                                        <th style="text-align: center;">Name</th>
                                        <th style="text-align: center;">Slug</th>
                                        <th style="text-align: center;">Main</th>
                                        <th style="text-align: center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($brands as $brand)
                                        <tr>
                                            <td class="pname" style="text-align: center;">
                                                <div class="image">
                                                    @if ($brand->image)
                                                                  <img src="{{ asset('uploads/brands/thumbnails/' . $brand->image) }}"
                                                        alt="{{ $brand->name }}" class="image"> 
                                                    @endif
                                         
                                                </div>
                                                <div class="name">
                                                    <a href="#" class="body-title-2">{{ $brand->name }}</a>
                                                </div>
                                            </td>
                                            <td style="text-align: center;">{{ $brand->slug }}</td>
                                            <td style="text-align: center;"><a href="#"
                                                    target="_blank">{{ $brand->main ? 'main brand' : ' ' }}</a></td>
                                            <td style="text-align: center;">
                                                <div class="list-icon-function">
                                                    <a href="{{ route('admin.brands.edit', ['id' => $brand->id]) }}">
                                                        <div class="item edit">
                                                            <i class="icon-edit-3"></i>
                                                        </div>
                                                    </a>
                                                    <form action="{{ route('admin.brand.delete', ['id' => $brand->id]) }}"
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
                        <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">

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

        $(document).ready(function(){
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#brandTable tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
    </script>
@endpush
