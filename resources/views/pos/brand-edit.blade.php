
@extends('layouts.pos')
@section('content')
<div class="main-content">
<div class="main-content-inner">
    <!-- main-content-wrap -->
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Brand infomation</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{route('pos.index')}}"><div class="text-tiny">Dashboard</div></a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{route('pos.brands')}}"><div class="text-tiny">Brands</div></a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Edit Brand</div>
                </li>
            </ul>
        </div>
        <!-- new-category -->
        <div class="wg-box">
            <form class="form-new-product form-style-1" action="{{route('pos.brand-update')}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{$brand->id}}" />
                <fieldset class="name">
                    <div class="body-title">Brand Name <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Category name" name="name" tabindex="0" value="{{$brand->name}}" aria-required="true" required="">
                </fieldset>
                @error("name") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
                <fieldset class="name">
                    <div class="body-title">Brand Slug <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="Category Slug" name="slug" tabindex="0" value="{{$brand->slug}}" aria-required="true" required="">
                </fieldset>
                @error("slug") <span class="alert alert-danger text-center">{{$message}}</span> @enderror
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
                <div class="bot">
                    <div></div>
                    <button class="tf-button w208" type="submit">Update</button>
                </div>
            </form>
        </div>
        <!-- /new-category -->
    </div>
    <!-- /main-content-wrap -->
</div>                    
</div> 
@endsection

@push("scripts")
    <script>
        $(function () {
            $("#myFile").on("change", function (e) {
                const photoInp = $("#myFile");
                const [file] = this.files;
                if (file) {
                    $("#imgpreview img").attr('src', URL.createObjectURL(file));
                    $("#imgpreview").show();
                }
            });
            $("input[name='name']").on("change", function () {
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