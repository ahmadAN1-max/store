@extends('layouts.pos')
@section('content')
    <div class="main-content">

        <!-- main-content-wrap -->
        <div class="main-content-inner">
            <!-- main-content-wrap -->
            <div class="main-content-wrap">
                <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                    <h3>Add Product</h3>
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
                            <a href="{{ route('pos.products') }}">
                                <div class="text-tiny">Products</div>
                            </a>
                        </li>
                        <li>
                            <i class="icon-chevron-right"></i>
                        </li>
                        <li>
                            <div class="text-tiny">Add product</div>
                        </li>
                    </ul>
                </div>
                <!-- form-add-product -->
                <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data"
                    action="{{ route('pos.product.store') }}">
                    @csrf
                    <div class="wg-box">
                        <fieldset class="name">
                            <div class="body-title mb-10">Product name <span class="tf-color-1">*</span></div>
                            <input class="mb-10" type="text" placeholder="Enter product name" name="name"
                                tabindex="0" value="" aria-required="true">
                            <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                        </fieldset>
                        @error('name')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror
                        <fieldset class="name">
                            <div class="body-title mb-10">Slug <span class="tf-color-1"></span></div>
                            <input class="mb-10" type="text" placeholder="Enter product slug" name="slug"
                                tabindex="0" value="" readonly>
                            <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                        </fieldset>
                        @error('slug')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror
                        <div class="gap22 cols">
           <fieldset class="category">
    <div class="body-title mb-10">Category <span class="tf-color-1">*</span></div>
    <div class="category-checkboxes">
        @foreach ($categories as $category)
            <div>
                <label>
                    <input type="checkbox" name="category_id[]" value="{{ $category->id }}">
                    {{ $category->name }}
                </label>
            </div>
        @endforeach
    </div>
</fieldset>

@if ($errors->has('category_id'))
    <span class="alert alert-danger text-center">{{ $errors->first('category_id') }}</span>
@elseif ($errors->has('category_id.0'))
    <span class="alert alert-danger text-center">{{ $errors->first('category_id.0') }}</span>
@endif

<style>
.category-checkboxes {
    max-height: 150px; /* ارتفاع محدد */
    overflow-y: auto;  /* يضيف scroll إذا زادت الفئات */
    padding-right: 5px; /* لتجنب قطع النص عند scroll */
    border: 1px solid #ddd; /* اختياري، لتوضيح الحدود */
    border-radius: 5px;
}
.category-checkboxes div {
    margin-bottom: 5px; /* مسافة بسيطة بين كل checkbox */
}
</style>


                            <fieldset class="brand">
                                <div class="body-title mb-10">Brand <span class="tf-color-1">*</span></div>
                                <div class="select">
                                    <select class="" name="brand_id">
                                        <option value="">Choose Brand</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </fieldset>
                            @error('brand_id')
                                <span class="alert alert-danger text-center">{{ $message }}</span>
                            @enderror
                        </div>
                        <input type="hidden" name="featured" value="0">

                        <fieldset class="sizes">
                            <div class="body-title mb-10">Sizes <span class="tf-color-1">*</span></div>
                            <input class="mb-10" type="text" placeholder="Enter sizes " name="sizes" tabindex="0"
                                value="" aria-required="true" required>
                            <div class="text-tiny">Enter product sizes separated by commas (e.g. S, M, L, XL)</div>
                        </fieldset>
                        @error('sizes')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="wg-box">
                        <div class="cols gap22">
                            <fieldset class="name">
                                <div class="body-title mb-10">Regular Price <span class="tf-color-1">*</span></div>
                                <input class="mb-10" type="text" placeholder="Enter regular price" name="regular_price"
                                    tabindex="0" value="" aria-required="true">
                            </fieldset>
                            @error('regular_price')
                                <span class="alert alert-danger text-center">{{ $message }}</span>
                            @enderror
                            <fieldset class="name">
                                <div class="body-title mb-10">Sale Price <span class="tf-color-1"></span></div>
                                <input class="mb-10" type="text" placeholder="Enter sale price" name="sale_price"
                                    tabindex="0" value="">

                            </fieldset>
                            @error('sale_price')
                                <span class="alert alert-danger text-center">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="cols gap22">
                            <fieldset class="name">
                                <div class="body-title mb-10">Unit Cost<span class="tf-color-1">*</span></div>
                                <input class="mb-10" type="text" placeholder="Enter Unit Cost" name="unit_cost"
                                    tabindex="0" value="">

                            </fieldset>
                            @error('unit_cost')
                                <span class="alert alert-danger text-center">{{ $message }}</span>
                            @enderror



                            <fieldset class="name">
                                <div class="body-title mb-10">SKU <span class="tf-color-1">*</span></div>
                                <input class="mb-10" type="text" placeholder="Enter SKU" name="SKU"
                                    tabindex="0" value="" aria-required="true">
                            </fieldset>
                            @error('SKU')
                                <span class="alert alert-danger text-center">{{ $message }}</span>
                            @enderror


                        </div>
                        <div class="cols gap22">
                            <fieldset class="name">
                                <div class="body-title mb-10">Stock</div>
                                <div class="select mb-10">
                                    <select class="" name="stock_status">
                                        <option value="instock">InStock</option>
                                        <option value="outofstock">Out of Stock</option>
                                    </select>
                                </div>
                            </fieldset>
                            @error('stock_status')
                                <span class="alert alert-danger text-center">{{ $message }}</span>
                            @enderror
                            <fieldset class="name">
                                <div class="body-title mb-10">Quantity <span class="tf-color-1"></span></div>
                                <input class="mb-10" type="text" placeholder="Enter quantity" name="quantity"
                                    tabindex="0" value="0" readonly>
                            </fieldset>
                            @error('quantity')
                                <span class="alert alert-danger text-center">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="cols gap10">
                            <button class="tf-button w-full" type="submit">Add product</button>
                        </div>
                    </div>
                </form>
                <!-- /form-add-product -->
            </div>
            <!-- /main-content-wrap -->
        </div>
        <!-- /main-content-wrap -->

        <div class="bottom-page">
            <div class="body-text">Copyright © 2024 SurfsideMedia</div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        $(function() {
            $("#myFile").on("change", function(e) {
                const photoInp = $("#myFile");
                const [file] = this.files;
                if (file) {
                    $("#imgpreview img").attr('src', URL.createObjectURL(file));
                    $("#imgpreview").show();
                }
            });
            $("#gFile").on("change", function(e) {
                $(".gitems").remove();
                const gFile = $("gFile");
                const gphotos = this.files;
                $.each(gphotos, function(key, val) {
                    $("#galUpload").prepend(
                        `<div class="item gitems"><img src="${URL.createObjectURL(val)}" alt=""></div>`
                    );
                });
            });
            $("input[name='name']").on("change", function() {
                $("input[name='slug']").val(StringToSlug($(this).val()));
            });

        });

        function StringToSlug(Text) {
            return Text.toLowerCase()
                .replace(/[^\w ]+/g, "")
                .replace(/ +/g, "-");
        }
    </script>
@endpush
