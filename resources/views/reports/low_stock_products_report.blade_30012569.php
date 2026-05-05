@extends('layouts.app')

@section('title', 'รายงานสินค้าสต็อกต่ำกว่าจุดต่ำสุด')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-exclamation-triangle me-2"></i> รายงานสินค้าสต็อกต่ำกว่าจุดต่ำสุด</h1>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-alt-circle-left me-2"></i> กลับ Dashboard
    </a>
</div>

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card shadow-sm">
    <div class="card-body">

        {{-- 🔍 Search + Export --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text"
                       id="search"
                       class="form-control"
                       placeholder="ค้นหา: รหัสสินค้า / ชื่อสินค้า">
            </div>

            <div class="col-md-6 text-end">
                <form method="GET" action="{{ route('reports.low-stock.export') }}" id="exportForm">
                    <input type="hidden" name="search" id="exportSearch">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> ส่งออก Excel
                    </button>
                </form>
            </div>
        </div>

        <p class="text-muted mb-3" id="totalRecords">
            ทั้งหมด {{ $lowStockProducts->total() }} รายการ
        </p>

        {{-- 📊 Table --}}
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>รหัสสินค้า</th>
                        <th>ชื่อสินค้า</th>
                        <th>หมวดหมู่</th>
                        <th>ผู้จัดจำหน่าย</th>
                        <th>จำนวนคงเหลือ</th>
                        <th>จุดต่ำสุด</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>

                <tbody id="productTableBody">
                    @foreach ($lowStockProducts as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $product->product_code }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? '-' }}</td>
                            <td>{{ $product->supplier->name ?? '-' }}</td>
                            <td>{{ $product->stock_sum ?? 0 }}</td>
                            <td>{{ $product->minimum_stock_level }}</td>
                            <td>
                                <span class="badge bg-danger">ต่ำกว่ากำหนด</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- 📄 Pagination --}}
        <div class="d-flex justify-content-center mt-3" id="paginationLinks">
            {{ $lowStockProducts->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {

    let typingTimer;
    const delay = 400;

    function syncSearchToExport() {
        let search = $('#search').val();
        $('#exportSearch').val(search);
    }

    function fetchLowStockData(page = 1) {
        let search = $('#search').val();

        // ✅ sync ค่าค้นหาทุกครั้ง
        syncSearchToExport();

        $.ajax({
            url: "{{ route('reports.low-stock.search') }}",
            type: "GET",
            data: {
                search: search,
                page: page
            },
            dataType: "json",
            success: function (response) {

                if (!response.table) {
                    console.error('Response ไม่ถูกต้อง:', response);
                    return;
                }

                $('#productTableBody').html(response.table);
                $('#paginationLinks').html(response.pagination);
                $('#totalRecords').text('ทั้งหมด ' + response.total + ' รายการ');
            },
            error: function (xhr) {
                console.error('AJAX error:', xhr.responseText);
            }
        });
    }

    // ✅ realtime search
    $('#search').on('input', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function () {
            fetchLowStockData(1);
        }, delay);
    });

    // ✅ ajax pagination
    $(document).on('click', '#paginationLinks a', function (e) {
        e.preventDefault();

        let href = $(this).attr('href');
        if (!href) return;

        let page = new URL(href).searchParams.get('page');
        fetchLowStockData(page);
    });

    // ✅ กันพลาดกรณีกด export โดยยังไม่พิมพ์อะไร
    $('#exportForm').on('submit', function () {
        syncSearchToExport();
    });

});
</script>

@endpush
