@extends('layouts.pos')
@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="main-content-wrap">
                <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                    <h3>customer infomation</h3>
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
                            <a href="{{ route('pos.customers') }}">
                                <div class="text-tiny">customers</div>
                            </a>
                        </li>
                        <li>
                            <i class="icon-chevron-right"></i>
                        </li>
                        <li>
                            <div class="text-tiny">New customer</div>
                        </li>
                    </ul>
                </div>
                <!-- new-category -->
                <div class="wg-box">
                    <form class="form-new-product form-style-1" action="{{ route('pos.customer-store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <fieldset class="name">
                            <div class="body-title">customer Name <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="text" placeholder="customer name" name="name"
                                tabindex="0" value="{{ old('name') }}" aria-required="true">
                        </fieldset>
                        @error('name')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror
                        <fieldset class="name">
                            <div class="body-title">Phone Number <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="text" placeholder="phone number" name="phone"
                                tabindex="0" value="{{ old('phone') }}" aria-required="true">
                        </fieldset>
                        @error('phone')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror
                        <fieldset class="name">
                            <div class="body-title">City <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="text" placeholder="city" name="city"
                                tabindex="0" value="{{ old('city') }}" aria-required="true">
                        </fieldset>
                        @error('city')
                            <span class="alert alert-danger text-center">{{ $message }}</span>
                        @enderror <fieldset class="name">
                            <div class="body-title">Address <span class="tf-color-1">*</span></div>
                           <textarea class="flex-grow" placeholder="address" name="address" tabindex="0" aria-required="true">{{ old('address') }}</textarea>

                        </fieldset>
                        @error('address')
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
