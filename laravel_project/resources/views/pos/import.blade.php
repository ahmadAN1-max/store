{{-- @extends('layouts.pos')
@section('content')
    <div class="main-content">
        <div class="main-content-inner">
<div class="container">
    <h3>Import Products from Excel</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{route('pos.importExcel')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group mb-3">
            <label for="excel_file">Choose Excel File</label>
            <input type="file" name="excel_file" id="excel_file" class="form-control" required accept=".xlsx,.xls">
        </div>
        <button type="submit" class="btn btn-primary">Import</button>
    </form>
</div>

        </div>
    </div>
@endsection --}}