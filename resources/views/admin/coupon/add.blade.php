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
            <div class="main-content-wrap">

                <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                    <h3>Coupon Information</h3>
                    <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                        <li>
                            <a href="{{ route('admin.index') }}">
                                <div class="text-tiny">Dashboard</div>
                            </a>
                        </li>
                        <li><i class="icon-chevron-right"></i></li>
                        <li>
                            <a href="{{ route('admin.coupons') }}">
                                <div class="text-tiny">Coupons</div>
                            </a>
                        </li>
                        <li><i class="icon-chevron-right"></i></li>
                        <li>
                            <div class="text-tiny">New Coupon</div>
                        </li>
                    </ul>
                </div>

                <div class="wg-box">
                    <form class="form-new-product form-style-1" method="POST" action="{{ route('admin.coupon.store') }}">
                        @csrf

                        <fieldset class="name">
                            <div class="body-title">Coupon Code <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="text" name="code" placeholder="Coupon Code"
                                value="{{ old('code') }}" aria-required="true">
                        </fieldset>
                        @error('code')
                            <span class="alert alert-danger text-center d-block">{{ $message }}</span>
                        @enderror

                        <fieldset class="category">
                            <div class="body-title">Coupon Type <span class="tf-color-1">*</span></div>
                            <div class="select flex-grow">
                                <select name="type">
                                    <option value="">Select</option>
                                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                    <option value="percent" {{ old('type') == 'percent' ? 'selected' : '' }}>Percent
                                    </option>
                                </select>
                            </div>
                        </fieldset>
                        @error('type')
                            <span class="alert alert-danger text-center d-block">{{ $message }}</span>
                        @enderror

                        <fieldset class="name">
                            <div class="body-title">Value <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="text" name="value" placeholder="Coupon Value"
                                value="{{ old('value') }}" aria-required="true">
                        </fieldset>
                        @error('value')
                            <span class="alert alert-danger text-center d-block">{{ $message }}</span>
                        @enderror

                        <fieldset class="name">
                            <div class="body-title">Cart Value <span class="tf-color-1">*</span></div>
                            <input class="flex-grow" type="text" name="cart_value" placeholder="Cart Value"
                                value="{{ old('cart_value', 0) }}" aria-required="true">
                        </fieldset>
                        @error('cart_value')
                            <span class="alert alert-danger text-center d-block">{{ $message }}</span>
                        @enderror

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
                                        <label class="form-check-label fw-semibold" for="check_all_categories">Choose Catgory</label>
                                    </div>
                                    <hr class="my-2">

                                    {{-- Categories list --}}
                                    <div class="px-2">
                                        @foreach ($categories as $cat)
                                            <div class="form-check mb-1">
                                                <input class="category-item" type="checkbox" value="{{ $cat->id }}"
                                                    data-name="{{ $cat->name }}" id="cat_{{ $cat->id }}"
                                                    name="category_id[]" @checked(collect(old('category_id', request('category_id', [])))->contains($cat->id))>
                                                <label class="form-check-label"
                                                    for="cat_{{ $cat->id }}">{{ $cat->name }}</label>
                                            </div>
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
                            <input class="flex-grow" type="date" name="expiry_date" value="{{ old('expiry_date') }}"
                                aria-required="true">
                        </fieldset>
                        @error('expiry_date')
                            <span class="alert alert-danger text-center d-block">{{ $message }}</span>
                        @enderror

                        <div class="bot">
                            <div></div>
                            <button class="tf-button w208" type="submit">Save</button>
                        </div>
                    </form>
                </div>

            </div>
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
