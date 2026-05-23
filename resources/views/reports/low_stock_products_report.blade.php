@extends('layouts.app')

@section('title', 'รายงานสินค้าสต็อกต่ำกว่าจุดต่ำสุด')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-exclamation-triangle me-2"></i> รายงานสินค้าสต็อกต่ำกว่าจุดต่ำสุด</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.low_stock_products.export', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i> ส่งออก Excel
            </a>
            @if(request('supplier_id'))
            <a href="{{ route('reports.low_stock_products.purchase_order', request('supplier_id')) }}"
               class="btn btn-primary" id="btnPdfAll">
                <i class="fas fa-file-pdf me-2"></i> ใบสั่งซื้อ PDF (ทั้งหมด)
            </a>
            @endif
            <button type="button" class="btn btn-warning d-none" id="btnPdfSelected" onclick="exportSelectedPdf()">
                <i class="fas fa-file-pdf me-2"></i> ใบสั่งซื้อ PDF (<span id="selectedCount">0</span> รายการ)
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-alt-circle-left me-2"></i> กลับ Dashboard
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('reports.low_stock_products') }}" class="mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">ค้นหาชื่อสินค้า</label>
                <input type="text" name="search" class="form-control"
                    placeholder="รหัสสินค้า / ชื่อสินค้า"
                    value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">ผู้จัดจำหน่าย</label>
                <select name="supplier_id" class="form-select">
                    <option value="">-- ทั้งหมด --</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}"
                            {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i> ค้นหา
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('reports.low_stock_products') }}" class="btn btn-secondary w-100">
                    <i class="fas fa-times me-1"></i> ล้างตัวกรอง
                </a>
            </div>
        </div>
    </form>

    {{-- แจ้งเตือนการเลือก --}}
    <div id="selectionAlert" class="alert alert-warning d-none mb-3 d-flex align-items-center justify-content-between">
        <span><i class="fas fa-check-square me-2"></i> เลือกสินค้าแล้ว <strong id="selectionCount">0</strong> รายการ</span>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                <i class="fas fa-times me-1"></i> ยกเลิกการเลือก
            </button>
            <button type="button" class="btn btn-sm btn-primary" onclick="exportSelectedPdf()">
                <i class="fas fa-file-pdf me-1"></i> ออกใบสั่งซื้อที่เลือก
            </button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th style="width:40px;">
                                <input type="checkbox" id="checkAll" class="form-check-input"
                                       title="เลือกทั้งหมด">
                            </th>
                            <th>#</th>
                            <th>รหัสสินค้า</th>
                            <th>ชื่อสินค้า</th>
                            <th>หมวดหมู่</th>
                            <th>ผู้จัดจำหน่าย</th>
                            <th>ประเภทสินค้า</th>
                            <th>หน่วยนับ</th>
                            <th>ราคาต้นทุน (ต่อหน่วย)</th>
                            <th>สต็อกปัจจุบัน</th>
                            <th>จุดต่ำสุด</th>
                            <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lowStockProducts as $product)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input product-checkbox"
                                           value="{{ $product->id }}"
                                           data-name="{{ $product->name }}"
                                           data-supplier="{{ $product->supplier_id }}"
                                           data-supplier-name="{{ $product->supplier->name ?? '-' }}">
                                </td>
                                <td>{{ $loop->iteration + ($lowStockProducts->currentPage() - 1) * $lowStockProducts->perPage() }}</td>
                                <td>{{ $product->product_code }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name ?? '-' }}</td>
                                <td>{{ $product->supplier->name ?? '-' }}</td>
                                <td>{{ $product->productType->name ?? '-' }}</td>
                                <td>{{ $product->unit }}</td>
                                <td>{{ number_format($product->cost_price, 2) }}</td>
                                <td>{{ number_format($product->batches->sum('quantity')) }}</td>
                                <td>{{ $product->minimum_stock_level }}</td>
                                <td>
                                    <span class="badge {{ $product->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $product->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center">ไม่พบสินค้าที่สต็อกต่ำกว่าจุดต่ำสุด</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center">
                {{ $lowStockProducts->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
const checkAll = document.getElementById('checkAll');
const selectionAlert = document.getElementById('selectionAlert');
const selectionCount = document.getElementById('selectionCount');
const selectedCount = document.getElementById('selectedCount');

// เลือกทั้งหมด
checkAll.addEventListener('change', function() {
    document.querySelectorAll('.product-checkbox').forEach(cb => {
        cb.checked = this.checked;
    });
    updateSelection();
});

// เลือกรายการ
document.querySelectorAll('.product-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        updateSelection();
        const allChecked = document.querySelectorAll('.product-checkbox:not(:checked)').length === 0;
        checkAll.checked = allChecked;
        checkAll.indeterminate = !allChecked && document.querySelectorAll('.product-checkbox:checked').length > 0;
    });
});

function updateSelection() {
    const checked = document.querySelectorAll('.product-checkbox:checked');
    const count = checked.length;

    if (count > 0) {
        selectionAlert.classList.remove('d-none');
        selectionCount.textContent = count;
        selectedCount.textContent = count;
        document.getElementById('btnPdfSelected').classList.remove('d-none');
    } else {
        selectionAlert.classList.add('d-none');
        document.getElementById('btnPdfSelected').classList.add('d-none');
    }
}

function clearSelection() {
    document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = false);
    checkAll.checked = false;
    checkAll.indeterminate = false;
    updateSelection();
}

function exportSelectedPdf() {
    const checked = document.querySelectorAll('.product-checkbox:checked');
    if (checked.length === 0) {
        alert('กรุณาเลือกสินค้าอย่างน้อย 1 รายการ');
        return;
    }

    const productIds = Array.from(checked).map(cb => cb.value);
    const supplierId = '{{ request('supplier_id') }}';

    const form = document.createElement('form');
    form.method = 'GET';
    form.action = '{{ route('reports.low_stock_products.purchase_order_preview') }}';

    productIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'product_ids[]';
        input.value = id;
        form.appendChild(input);
    });

    if (supplierId) {
        const supplierInput = document.createElement('input');
        supplierInput.type = 'hidden';
        supplierInput.name = 'supplier_id';
        supplierInput.value = supplierId;
        form.appendChild(supplierInput);
    }

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>
@endpush