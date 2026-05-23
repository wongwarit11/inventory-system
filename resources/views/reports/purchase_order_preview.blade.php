@extends('layouts.app')

@section('title', 'Preview ใบสั่งซื้อ')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-file-pdf me-2 text-primary"></i> Preview ใบสั่งซื้อ</h1>
    <a href="{{ route('reports.low_stock_products') }}" class="btn btn-secondary rounded-pill px-4">
        <i class="fas fa-arrow-left me-2"></i> กลับ
    </a>
</div>

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form action="{{ route('reports.low_stock_products.purchase_order_preview.export') }}" method="POST">
    @csrf

    {{-- ส่ง product_ids และ supplier_id ไปด้วย --}}
    @foreach($productIds as $id)
        <input type="hidden" name="product_ids[]" value="{{ $id }}">
    @endforeach
    <input type="hidden" name="supplier_id" value="{{ $supplierId }}">

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            {{-- ข้อมูลผู้จัดจำหน่าย --}}
            @if($supplier)
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="p-3 rounded" style="background:#E6F1FB;border:0.5px solid #B5D4F4;">
                        <div style="font-size:12px;font-weight:500;color:#0C447C;margin-bottom:8px;">
                            <i class="fas fa-truck me-2"></i>ผู้จัดจำหน่าย
                        </div>
                        <div style="font-size:14px;font-weight:500;">{{ $supplier->name }}</div>
                        @if($supplier->address)
                        <div style="font-size:12px;color:#5F5E5A;margin-top:4px;">{{ $supplier->address }}</div>
                        @endif
                        @if($supplier->phone)
                        <div style="font-size:12px;color:#5F5E5A;">โทร: {{ $supplier->phone }}</div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- ตารางสินค้า --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>รหัสสินค้า</th>
                            <th>ชื่อสินค้า</th>
                            <th style="width:100px;">คงเหลือ</th>
                            <th style="width:100px;">จุดต่ำสุด</th>
                            <th>หน่วย</th>
                            <th style="width:150px;">
                                จำนวนที่สั่งซื้อ
                                <small class="d-block text-muted fw-normal" style="font-size:10px;">แก้ไขได้</small>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $i => $product)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td style="color:#185FA5;font-weight:500;">{{ $product->product_code }}</td>
                            <td>{{ $product->name }}</td>
                            <td>
                                <span class="badge {{ $product->current_stock == 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                    {{ number_format($product->current_stock) }}
                                </span>
                            </td>
                            <td style="color:#888780;">{{ number_format($product->minimum_stock_level) }}</td>
                            <td style="color:#888780;">{{ $product->unit }}</td>
                            <td>
                                <input type="number"
                                       name="quantities[{{ $product->id }}]"
                                       value="{{ $product->suggested_qty }}"
                                       min="1"
                                       class="form-control form-control-sm"
                                       style="width:110px;">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="6" class="text-end fw-500">รวมทั้งหมด {{ count($products) }} รายการ</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- ปุ่ม --}}
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('reports.low_stock_products') }}" class="btn btn-secondary rounded-pill px-4">
            <i class="fas fa-times me-2"></i> ยกเลิก
        </a>
        <button type="submit" class="btn btn-primary rounded-pill px-5">
            <i class="fas fa-file-pdf me-2"></i> ออกใบสั่งซื้อ PDF
        </button>
    </div>
</form>
@endsection