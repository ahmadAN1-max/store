@extends('layouts.pos')
@section('content')
    <div class="main-content">
        <!-- main-content-wrap -->
        <div class="main-content-inner">
            <!-- main-content-wrap -->
            <div class="main-content-wrap">
                <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                    <h3>Edit Product</h3>
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
                            <div class="text-tiny">Edit product</div>
                        </li>
                    </ul>
                </div>
                <!-- form-add-product -->
                <form class="tf-section-2 form-add-product" method="POST" enctype="multipart/form-data"
                    action="{{ route('pos.product.update') }}">
                    <input type="hidden" name="id" value="{{ $product->id }}" />
                    @csrf
                    @method('PUT')
                    <div class="wg-box">
                        <fieldset class="name">
                            <div class="body-title mb-10">Product name <span class="tf-color-1">*</span></div>
                            <input class="mb-10" type="text" placeholder="Enter product name" name="name"
                                tabindex="0" value="{{ $product->name }}" aria-required="true" required="">


                            <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title mb-10">Slug <span class="tf-color-1">*</span></div>
                            <input class="mb-10" type="text" placeholder="Enter product slug" name="slug"
                                tabindex="0" value="{{ $product->slug }}" aria-required="true" required="">
                            <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                        </fieldset>
                        <div class="gap22 cols">
                            <fieldset class="category">
                                <div class="body-title mb-10">Category <span class="tf-color-1">*</span></div>
                                <div class="category-checkboxes">
                                    @foreach ($categories as $category)
                                        <div>
                                            <label>
                                                <input type="checkbox" name="category_id[]" value="{{ $category->id }}"
                                                    {{ $product->categories->contains($category->id) ? 'checked' : '' }}>
                                                {{ $category->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </fieldset>

                            <style>
                                .category-checkboxes {
                                    max-height: 150px;
                                    /* ارتفاع محدد */
                                    overflow-y: auto;
                                    /* يظهر scroll إذا صار في كتير فئات */
                                    padding-right: 5px;
                                    border: 1px solid #ddd;
                                    border-radius: 5px;
                                }

                                .category-checkboxes div {
                                    margin-bottom: 5px;
                                }
                            </style>

                            <fieldset class="brand">
                                <div class="body-title mb-10">Brand <span class="tf-color-1">*</span></div>
                                <div class="select">
                                    <select class="" name="brand_id">
                                        <option>Choose Brand</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}"
                                                {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </fieldset>
                        </div>

                        <fieldset class="sizes">
                            <div class="body-title mb-10">
                                Sizes <span class="tf-color-1">*</span>
                            </div>

                            <input class="mb-10" type="text" placeholder="Enter sizes" name="sizes" tabindex="0"
                                value="{{ implode(',', $items->pluck('sizes')->toArray()) }}" aria-required="true"
                                required>

                            <div class="text-tiny">
                                Enter product sizes separated by commas (e.g. S, M, L, XL)
                            </div>
                        </fieldset>

                        @error('sizes')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror

                        <fieldset class="sizes">
                            <div class="body-title mb-10">Sizes </div>
                            <div class="row">
                                @foreach ($items as $item)
                                    <div class="col-md-3 col-sm-4 col-6 mb-4">
                                        <div class="p-3 border rounded shadow-sm text-center">
                                            <h6 class="mb-2">{{ $item->sizes }}</h6>

                                            {{-- الكمية --}}
                                            <label class="small mb-1">Quantity</label>
                                            <input type="number" class="form-control text-center mb-2"
                                                name="quantities[{{ $item->id }}]" value="{{ $item->quantity }}"
                                                min="0" required>

                                            {{-- الباركود --}}
                                            <label class="small mb-1">Barcode</label>
                                            <input type="text" class="form-control text-center barcode-input"
                                                name="barcodes[{{ $item->id }}]" value="{{ $item->barcode }}">
                                        </div>
                                    </div>
                                @endforeach


                            </div>

                        </fieldset>
                    </div>
                    <div class="wg-box">

                        <div class="cols gap22">
                            <fieldset class="name">
                                <div class="body-title mb-10">Regular Price <span class="tf-color-1">*</span></div>
                                <input class="mb-10" type="text" placeholder="Enter regular price" name="regular_price"
                                    tabindex="0" value="{{ $product->regular_price }}" aria-required="true"
                                    required="">
                            </fieldset>
                            <fieldset class="name">
                                <div class="body-title mb-10">Sale Price <span class="tf-color-1"></span></div>
                                <input class="mb-10" type="text" placeholder="Enter sale price" name="sale_price"
                                    tabindex="0" value="{{ $product->sale_price }}">
                            </fieldset>
                        </div>
                        <div class="cols gap22">
                            <fieldset class="name">
                                <div class="body-title mb-10">Unit Cost<span class="tf-color-1"></span></div>
                                <input class="mb-10" type="text" placeholder="Enter sale price" name="unit_cost"
                                    tabindex="0" value="{{ $product->unit_cost }}">
                            </fieldset>

                            <fieldset class="name">
                                <div class="body-title mb-10">SKU <span class="tf-color-1">*</span></div>
                                <input class="mb-10" type="text" placeholder="Enter SKU" name="SKU"
                                    tabindex="0" value="{{ $product->SKU }}" aria-required="true" required="">
                            </fieldset>

                        </div>
                        <div class="cols gap22">

                       <fieldset class="name">
    <div class="body-title mb-10">Store</div>
    <div class="select mb-10">
        <select name="store">
            <option value="SP" {{ $product->store == 'SP' ? 'selected' : '' }}>SP</option>
            <option value="Luxe" {{ $product->store == 'Luxe' ? 'selected' : '' }}>Luxe</option>
            <option value="Both" {{ $product->store == 'Both' ? 'selected' : '' }}>Both</option>
        </select>
    </div>
</fieldset>

                        
                            <fieldset class="name">
                                <div class="body-title mb-10">Quantity <span class="tf-color-1"></span></div>
                                <input class="mb-10" type="text" placeholder="Enter quantity" name="quantity"
                                    tabindex="0" value="{{ $product->quantity }}" aria-required="true" readonly>
                            </fieldset>
                            <input type="hidden" name="featured" value="0">
                        </div>
                        <div class="cols gap10">
                            <button class="tf-button w-full" type="submit">Update product</button>
                        </div>
                    </div>
                </form>
                <!-- /form-add-product -->
            </div>
            <!-- /main-content-wrap -->
        </div>
        <!-- /main-content-wrap -->
    @endsection


    @push('scripts')
        <script>
            //enter move between barcodes
            document.addEventListener("DOMContentLoaded", () => {
                const barcodeInputs = document.querySelectorAll(".barcode-input");

                barcodeInputs.forEach((input, index) => {
                    input.addEventListener("keydown", function(e) {
                        if (e.key === "Enter") {
                            e.preventDefault(); // 🚫 ما يعمل submit

                            // روح على الباركود اللي بعده إذا موجود
                            const next = barcodeInputs[index + 1];
                            if (next) {
                                next.focus();
                            }
                        }
                    });
                });
            });

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
