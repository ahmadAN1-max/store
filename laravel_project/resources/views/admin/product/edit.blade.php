@extends('layouts.admin')
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
                            <a href="{{ route('admin.index') }}">
                                <div class="text-tiny">Dashboard</div>
                            </a>
                        </li>
                        <li>
                            <i class="icon-chevron-right"></i>
                        </li>
                        <li>
                            <a href="{{ route('admin.products') }}">
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
                    action="{{ route('admin.product.update') }}">
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



                        <fieldset class="shortdescription">
                            <div class="body-title mb-10">Short Description </div>
                            <textarea class="mb-10 ht-150" name="short_description" placeholder="Short Description" tabindex="0"
                                aria-required="true">{{ $product->short_description ? $product->short_description : ' ' }}</textarea>


                            <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                        </fieldset>

                        <fieldset class="description">
                            <div class="body-title mb-10">Description </div>
                            <textarea class="mb-10" name="description" placeholder="Description" tabindex="0" aria-required="true">{{ $product->description ? $product->description : ' ' }}</textarea>
                            <div class="text-tiny">Do not exceed 100 characters when entering the product name.</div>
                        </fieldset>

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
                                            <input type="text" class="form-control text-center"
                                                name="barcodes[{{ $item->id }}]" value="{{ $item->barcode }}">
                                        </div>
                                    </div>
                                @endforeach

                            </div>

                        </fieldset>
                    </div>
                    <div class="wg-box">
                        <fieldset>
                            <div class="body-title">Upload images <span class="tf-color-1">*</span></div>
                            <div class="upload-image">
                                <input type="hidden" name="remove_image" id="remove_image" value="0">

                                @if ($product->image)
                                    <div class="item" id="imgpreview" data-old-image="{{ $product->image }}"
                                        style="display:inline-block; position:relative; margin-bottom:10px;">
                                        <img src="{{ asset('uploads/products/thumbnails/' . $product->image) }}"
                                            alt=""
                                            style="max-width:150px; max-height:150px; display:block; border:1px solid #ccc;">
                                        <button type="button" onclick="removeImage('imgpreview')"
                                            style="position:absolute; top:5px; right:5px; background:transparent; color:black; border:none; font-size:18px; cursor:pointer;">&times;</button>
                                    </div>
                                @endif

                                <div id="upload-file" class="item">
                                    <label for="myFile"
                                        style="cursor:pointer; display:block; padding:10px; border:1px dashed #999;">
                                        Select image
                                        <input type="file" id="myFile" name="image" accept="image/*"
                                            style="display:none;">
                                    </label>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset>
                            <div class="body-title mb-10">Upload Gallery Images</div>
                            <div class="upload-image mb-16">
                                <input type="hidden" name="remove_gallery_images" id="remove_gallery_images"
                                    value="">

                                @if ($product->images)
                                    @foreach (explode(',', $product->images) as $index => $img)
                                        <div class="item gitems" id="gimg{{ $index }}"
                                            data-old-image="{{ trim($img) }}"
                                            style="display:inline-block; position:relative; margin:5px;">
                                            <img src="{{ asset('uploads/products/thumbnails/' . trim($img)) }}"
                                                alt=""
                                                style="max-width:100px; max-height:100px; display:block; border:1px solid #ccc;">
                                            <button type="button" onclick="removeGalleryImage('{{ $index }}')"
                                                style="position:absolute; top:3px; right:3px; background:transparent; color:black; border:none; font-size:16px; cursor:pointer;">&times;</button>
                                        </div>
                                    @endforeach
                                @endif

                                <div id="galUpload" class="item">
                                    <label for="gFile"
                                        style="cursor:pointer; display:block; padding:10px; border:1px dashed #999;">
                                        Select gallery images
                                        <input type="file" id="gFile" name="images[]" accept="image/*" multiple
                                            style="display:none;">
                                    </label>
                                </div>
                            </div>
                        </fieldset>


                        <div class="cols gap22">
                            <fieldset class="name">
                                <div class="body-title mb-10">Regular Price <span class="tf-color-1">*</span></div>
                                <input class="mb-10" type="text" placeholder="Enter regular price"
                                    name="regular_price" tabindex="0" value="{{ $product->regular_price }}"
                                    aria-required="true" required="">
                            </fieldset>
                            <fieldset class="name">
                                <div class="body-title mb-10">Sale Price <span class="tf-color-1"></span></div>
                                <input class="mb-10" type="text" placeholder="Enter sale price" name="sale_price"
                                    tabindex="0" value="{{ $product->sale_price }}">
                            </fieldset>
                        </div>
                        <div class="cols gap22">
                            <fieldset class="name">
                                <div class="body-title mb-10">SKU <span class="tf-color-1">*</span></div>
                                <input class="mb-10" type="text" placeholder="Enter SKU" name="SKU"
                                    tabindex="0" value="{{ $product->SKU }}" aria-required="true" required="">
                            </fieldset>
                            <fieldset class="name">
                                <div class="body-title mb-10">Quantity <span class="tf-color-1"></span></div>
                                <input class="mb-10" type="text" placeholder="Enter quantity" name="quantity"
                                    tabindex="0" value="{{ $product->quantity }}" aria-required="true" readonly>
                            </fieldset>
                        </div>
                        <div class="cols gap22">
                            <fieldset class="name">
                                <div class="body-title mb-10">Stock</div>
                                <div class="select mb-10">
                                    <select class="" name="stock_status">
                                        <option value="instock"
                                            {{ $product->stock_status == 'instock' ? 'Selected' : '' }}>
                                            InStock</option>
                                        <option value="outofstock"
                                            {{ $product->stock_status == 'outofstock' ? 'Selected' : '' }}>Out of Stock
                                        </option>
                                    </select>
                                </div>
                            </fieldset>
                            <fieldset class="name">
                                <div class="body-title mb-10">Published</div>
                                <div class="select mb-10">
                                    <select name="featured">
                                        <option value="0" {{ $product->featured == 0 ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ $product->featured == 1 ? 'selected' : '' }}>Yes
                                        </option>
                                    </select>
                                </div>

                            </fieldset>





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
            $(function() {
                $("#myFile").on("change", function() {
                    const [file] = this.files;
                    if (file) {
                        const imgUrl = URL.createObjectURL(file);

                        if ($("#imgpreview").length === 0) {
                            $("<div class='item' id='imgpreview'><img src='' class='effect8' style='max-width: 200px; max-height: 200px;'></div>")
                                .insertBefore("#upload-file");
                        }
                        $("#imgpreview img").attr('src', imgUrl);
                        $("#imgpreview").show();
                    }
                });

                $("#gFile").on("change", function(e) {
                    $(".gitems").remove();
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
        <script>
            function removeImage(id) {
                document.getElementById(id).remove();
                // لو كانت الصورة الرئيسية
                if (id === 'imgpreview') {
                    document.getElementById('remove_image').value = 1;
                }
            }

            function removeGalleryImage(index) {
                const el = document.getElementById('gimg' + index);
                const oldImage = el.getAttribute('data-old-image');

                // حذف العنصر من الـ DOM
                el.remove();

                // تحديث hidden input لتحديد الصور المحذوفة
                let removed = document.getElementById('remove_gallery_images').value;
                removed = removed ? removed.split(',') : [];
                removed.push(oldImage);
                document.getElementById('remove_gallery_images').value = removed.join(',');
            }
        </script>
    @endpush
