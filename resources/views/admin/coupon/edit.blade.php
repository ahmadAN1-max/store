@extends('layouts.admin')
@section('content')
    <style>
        /* Dropdown button */
        #categoryDropdown {
            border-radius: 0.5rem;
            padding: 0.6rem 1rem;
            background-color: #fff;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        #categoryDropdown:hover {
            background-color: #f8f9fa;
        }

        /* Dropdown menu */
        .dropdown-menu {
            border-radius: 0.5rem;
            border: 1px solid #ddd;
        }

        /* Scrollbar جميل */
        .dropdown-menu::-webkit-scrollbar {
            width: 6px;
        }

        .dropdown-menu::-webkit-scrollbar-thumb {
            background: #bbb;
            border-radius: 10px;
        }

        .dropdown-menu::-webkit-scrollbar-thumb:hover {
            background: #999;
        }
    </style>
    <div class="main-content">
        <div class="main-content-inner">
            <!-- main-content-wrap -->
            <div class="main-content-wrap">
                <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                    <h3>Coupon infomation</h3>
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
                            <a href="{{ route('admin.coupons') }}">
                                <div class="text-tiny">Coupons</div>
                            </a>
                        </li>
                        <li>
                            <i class="icon-chevron-right"></i>
                        </li>
                        <li>
                            <div class="text-tiny">Edit Coupon</div>
                        </li>
                    </ul>
                </div>
                <!-- new-category -->
                <div class="wg-box">
                    <form class="form-new-product form-style-1" method="POST" action="{{ route('admin.coupon.update') }}">
                        @csrf
                        @method('put')
                        <input type="hidden" name="id" value="{{ $coupon->id }}" />
                        <fieldset class="name">
                            <div class="body-title">Coupon Code <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="text" placeholder="Coupon Code" name="code" tabindex="0"
                                value="{{ $coupon->code }}" aria-required="true" required="">
                        </fieldset>
                        <fieldset class="category">
                            <div class="body-title">Coupon Type</div>
                            <div class="select flex-grow">
                                <select class="" name="type">
                                    <option value="">Select</option>
                                    <option value="fixed" {{ $coupon->type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                    <option value="percent" {{ $coupon->type == 'percent' ? 'selected' : '' }}>Percent
                                    </option>
                                </select>
                            </div>
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title">Value <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="text" placeholder="Coupon Value" name="value" tabindex="0"
                                value="{{ $coupon->value }}" aria-required="true" required="">
                        </fieldset>
                        <fieldset class="name">
                            <div class="body-title">Cart Value <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="text" placeholder="Cart Value" name="cart_value"
                                tabindex="0" value="{{ $coupon->cart_value }}" aria-required="true" required="">
                        </fieldset>


                        <fieldset class="category mb-3">
                            <div class="body-title">Category <span class="tf-color-1">*</span></div>

                            <div class="dropdown w-100">
                                <button
                                    class="btn btn-outline-secondary w-100 d-flex justify-content-between align-items-center"
                                    type="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">

                                    <span id="categoryDropdownLabel" class="text-truncate">All categories</span>
                                    <span class="badge bg-primary ms-2" id="categoryCount"></span>
                                </button>

                                <div class="dropdown-menu w-100 shadow-sm p-2" aria-labelledby="categoryDropdown"
                                    style="max-height: 300px; overflow-y: auto;">
                                    {{-- Select All --}}
                                    <div class="form-check mb-2 ps-2">
                                        <input class="form-check-input" type="checkbox" id="check_all_categories">
                                        <label class="form-check-label fw-semibold" for="check_all_categories">All
                                            categories</label>
                                    </div>
                                    <hr class="my-2">

                                    {{-- Categories list --}}
                                    <div class="px-2">
                                        @foreach ($categories as $category)
                                            <label>
                                                <input type="checkbox" name="category_id[]" value="{{ $category->id }}"
                                                    class="category-item" data-name="{{ $category->name }}"
                                                    {{ in_array($category->id, $couponCategoryIds) ? 'checked' : '' }}>
                                                {{ $category->name }}
                                            </label>
                                        @endforeach

                                    </div>
                                </div>
                            </div>

                            @error('category_id')
                                <span class="alert alert-danger text-center d-block mt-2">{{ $message }}</span>
                            @enderror
                        </fieldset>

                        <fieldset class="name">
                            <div class="body-title">Expiry Date <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="date" placeholder="Expiry Date" name="expiry_date"
                                tabindex="0" value="{{ $coupon->expiry_date }}" aria-required="true" required="">
                        </fieldset>

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
        document.addEventListener('DOMContentLoaded', function() {
            const checkAll = document.getElementById('check_all_categories');
            const items = Array.from(document.querySelectorAll('.category-item'));
            const label = document.getElementById('categoryDropdownLabel');
            const countSpan = document.getElementById('categoryCount');

            function updateLabel() {
                const selected = items.filter(i => i.checked);

                if (selected.length === 0) {
                    label.textContent = 'All categories';
                    countSpan.textContent = '';
                    checkAll.checked = false;
                } else if (selected.length === items.length) {
                    label.textContent = 'All categories';
                    countSpan.textContent = '';
                    checkAll.checked = true;
                } else if (selected.length <= 2) {
                    label.textContent = selected.map(i => i.dataset.name).join(', ');
                    countSpan.textContent = '';
                    checkAll.checked = false;
                } else {
                    label.textContent = 'Multiple selected';
                    countSpan.textContent = `(${selected.length})`;
                    checkAll.checked = false;
                }
            }

            // toggle all
            checkAll?.addEventListener('change', function() {
                items.forEach(i => i.checked = checkAll.checked);
                updateLabel();
            });

            // listen to items
            items.forEach(i => i.addEventListener('change', updateLabel));

            // init on load
            updateLabel();
        });
    </script>
@endpush
