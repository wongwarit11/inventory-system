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
const STORAGE_KEY = 'low_stock_selected_{{ request('supplier_id') }}_{{ request('search') }}';

// โหลด selected ids จาก localStorage
function loadSelected() {
    try {
        return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
    } catch(e) {
        return [];
    }
}

// บันทึก selected ids ลง localStorage
function saveSelected(ids) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
}

// อัปเดต UI ทั้งหมด
function updateUI() {
    const ids = loadSelected();
    const count = ids.length;

    // อัปเดต checkboxes ในหน้านี้
    document.querySelectorAll('.product-checkbox').forEach(cb => {
        cb.checked = ids.includes(cb.value);
    });

    // อัปเดต checkAll
    const allCbs = document.querySelectorAll('.product-checkbox');
    const allChecked = allCbs.length > 0 && Array.from(allCbs).every(cb => ids.includes(cb.value));
    checkAll.checked = allChecked;
    checkAll.indeterminate = !allChecked && count > 0 && Array.from(allCbs).some(cb => ids.includes(cb.value));

    // อัปเดต selection alert
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

const checkAll = document.getElementById('checkAll');
const selectionAlert = document.getElementById('selectionAlert');
const selectionCount = document.getElementById('selectionCount');
const selectedCount = document.getElementById('selectedCount');

// เลือกทั้งหมดในหน้านี้
checkAll.addEventListener('change', function() {
    const ids = loadSelected();
    document.querySelectorAll('.product-checkbox').forEach(cb => {
        cb.checked = this.checked;
        if (this.checked) {
            if (!ids.includes(cb.value)) ids.push(cb.value);
        } else {
            const idx = ids.indexOf(cb.value);
            if (idx > -1) ids.splice(idx, 1);
        }
    });
    saveSelected(ids);
    updateUI();
});

// เลือกรายการเดียว
document.querySelectorAll('.product-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        const ids = loadSelected();
        if (this.checked) {
            if (!ids.includes(this.value)) ids.push(this.value);
        } else {
            const idx = ids.indexOf(this.value);
            if (idx > -1) ids.splice(idx, 1);
        }
        saveSelected(ids);
        updateUI();
    });
});

function clearSelection() {
    saveSelected([]);
    updateUI();
}

function exportSelectedPdf() {
    const ids = loadSelected();
    if (ids.length === 0) {
        alert('กรุณาเลือกสินค้าอย่างน้อย 1 รายการ');
        return;
    }

    const supplierId = '{{ request('supplier_id') }}';
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = '{{ route('reports.low_stock_products.purchase_order_preview') }}';

    ids.forEach(id => {
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

// โหลด selected เมื่อหน้าโหลด
document.addEventListener('DOMContentLoaded', function() {
    updateUI();
});
</script>
@endpush