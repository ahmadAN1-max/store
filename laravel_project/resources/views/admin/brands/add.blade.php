@extends('layouts.admin')
@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <!-- main-content-wrap -->
            <div class="main-content-wrap">
                <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                    <h3>Brand infomation</h3>
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
                            <a href="{{ route('admin.brands') }}">
                                <div class="text-tiny">Brands</div>
                            </a>
                        </li>
                        <li>
                            <i class="icon-chevron-right"></i>
                        </li>
                        <li>
                            <div class="text-tiny">New Brand</div>
                        </li>
                    </ul>
                </div>
                <!-- new-category -->
                <div class="wg-box">
                    <form class="form-new-product form-style-1" action="{{ route('admin.brand.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <fieldset class="name">
                            <div class="body-title">Brand Name <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="text" placeholder="Brand name" name="name" tabindex="0"
                                value="{{ old('name') }}" aria-required="true">
                        </fieldset>
                        @error('name')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror
                        <fieldset class="name">
                            <div class="body-title">Brand Slug <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="text" placeholder="Brand Slug" name="slug" tabindex="0"
                                value="{{ old('slug') }}" aria-required="true">
                        </fieldset>
                        @error('slug')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror

                        <fieldset class="main">
                            <div class="body-title">
                                Main Brand <span class="tf-color-1">*</span>
                            </div>
                            <input type="checkbox" name="main" value="1" {{ old('main', true) ? 'checked' : '' }}
                                tabindex="0">
                        </fieldset>

                        @error('main')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror

                        <fieldset>
                            <div class="body-title">Upload images <span class="tf-color-1">*</span></div>
                            <div class="upload-image flex-grow">
                                <div class="item" id="imgpreview" style="display:none">
                                    <img id="preview-img" class="effect8" alt=""
                                        style="max-width: 200px; border:1px solid #ccc; border-radius:5px;">
                                </div>


                                <div id="upload-file" class="item up-load">
                                    <label class="uploadfile" for="myFile">
                                        <span class="icon">
                                            <i class="icon-upload-cloud"></i>
                                        </span>
                                        <span class="body-text">Drop your images here or select <span class="tf-color">click
                                                to
                                                browse</span></span>
                                        <input type="file" id="myFile" name="image" accept="image/*">
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                        @error('image')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror

                        <div class="bot">
                            <div></div>
                            <button class="tf-button w208" type="submit">Save</button>
                        </div>
                    </form>
                </div>
                <!-- /new-category -->
            </div>
            <!-- /main-content-wrap -->
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $("#myFile").on("change", function(e) {
                const [file] = this.files;
                if (file) {
                    let imgUrl = URL.createObjectURL(file);
                    $("#preview-img").attr('src', imgUrl);
                    $("#imgpreview").show();
                }
            });

            $("input[name='name']").on("input", function() {
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
