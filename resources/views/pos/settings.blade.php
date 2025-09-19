@extends('layouts.pos')
@section('content')
    <div class="main-content">
        <div class="container py-5">

            <div class="row justify-content-center g-4">

                <!-- Settings Card -->
                <div class="col-md-6">
                    <div class="card shadow-sm rounded-4">
                        <div class="card-body">
                            <h4 class="card-title text-center mb-4">⚙️ Settings</h4>
                            <form action="{{ route('pos.setMaxDiscount') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="maxDiscount" class="form-label">Maximum Discount (%)</label>
                                    <input type="number" name="maxDiscount" id="maxDiscount" value="{{ $maxDiscount }}"
                                        step="1" min="0" max="100"
                                        class="form-control @error('maxDiscount') is-invalid @enderror" required>
                                    @error('maxDiscount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary w-100">💾 Save Settings</button>
                            </form>
                        </div>
                    </div>


                    <!-- Import Products Card -->
                    <div class="card shadow-sm rounded-4" style="margin-top:10px">
                        <div class="card-body">
                            <h4 class="card-title text-center mb-4">📁 Import Products</h4>
                            <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <input type="file" name="file" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-success w-100">⬆️ Import</button>
                                <div class="alert alert-warning mt-3" role="alert">
                                    ⚠️ Notice: The producing company is not responsible for any errors resulting from
                                    uploading an inconsistent or incorrect Excel file.
                                </div>

                            </form>
                        </div>
                    </div>

                    <div class="card shadow-sm rounded-4" style="margin-top:10px">
                        <div class="card-body">
                            <h4 class="card-title text-center mb-4">📁 Import Barcodes</h4>
                            <!-- Export -->
                            <form action="{{ route('products.exportBarcodes') }}" method="GET" class="mb-3">
                                <button type="submit" class="btn btn-primary w-100">⬇️ Export</button>
                            </form>
                            <form action="{{ route('products.importBarcodes') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <input type="file" name="file" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-success w-100">⬆️ Import Barcodes</button>

                                <div class="alert alert-warning mt-3" role="alert">
                                    ⚠️ Notice: The producing company is not responsible for any errors resulting from
                                    uploading an inconsistent or incorrect Excel file.
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <style>
        body {
            background-color: #f5f6fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border: none;
        }

        .card-title {
            color: #0d6efd;
        }

        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
    </style>
@endsection
