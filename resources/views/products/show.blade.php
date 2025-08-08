@extends('layouts.app')

@section('title', 'รายละเอียดสินค้า')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-info-circle me-2"></i> รายละเอียดสินค้า</h1>
        <a href="{{ route('products.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-alt-circle-left me-2"></i> กลับหน้ารายการสินค้า</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">ข้อมูลสินค้า: {{ $product->name }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    @if ($product->image_path)
                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="img-fluid rounded-3 shadow-sm" style="max-width: 250px; height: auto; object-fit: cover;">
                    @else
                        <i class="fas fa-box-open text-muted" style="font-size: 10rem;"></i> {{-- Larger icon placeholder --}}
                        <p class="text-muted mt-2">ไม่มีรูปภาพ</p>
                    @endif
                </div>
                <div class="col-md-8">
                    <dl class="row">
                        <dt class="col-sm-4">รหัสสินค้า:</dt>
                        <dd class="col-sm-8">{{ $product->product_code }}</dd>

                        <dt class="col-sm-4">ชื่อสินค้า:</dt>
                        <dd class="col-sm-8">{{ $product->name }}</dd>

                        <dt class="col-sm-4">หมวดหมู่:</dt>
                        <dd class="col-sm-8">{{ $product->category->name ?? '-' }}</dd>

                        <dt class="col-sm-4">ผู้จัดจำหน่าย:</dt>
                        <dd class="col-sm-8">{{ $product->supplier->name ?? '-' }}</dd>

                        <dt class="col-sm-4">ผู้ผลิต:</dt>
                        <dd class="col-sm-8">{{ $product->manufacturer->name ?? '-' }}</dd>

                        <dt class="col-sm-4">ประเภทสินค้า:</dt>
                        <dd class="col-sm-8">{{ $product->productType->name ?? '-' }}</dd>

                        <dt class="col-sm-4">หน่วยนับ:</dt>
                        <dd class="col-sm-8">{{ $product->unit }}</dd>

                        <dt class="col-sm-4">จุดต่ำสุดที่ต้องสั่งซื้อ:</dt>
                        <dd class="col-sm-8">{{ number_format($product->minimum_stock_level) }}</dd>

                        <dt class="col-sm-4">ราคาต้นทุน (ต่อหน่วย):</dt>
                        <dd class="col-sm-8">{{ number_format($product->cost_price, 2) }} บาท</dd>

                        <dt class="col-sm-4">สถานะ:</dt>
                        <dd class="col-sm-8">
                            <span class="badge {{ $product->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $product->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">สร้างเมื่อ:</dt>
                        <dd class="col-sm-8">{{ $product->created_at->format('d/m/Y H:i:s') }}</dd>

                        <dt class="col-sm-4">อัปเดตล่าสุด:</dt>
                        <dd class="col-sm-8">{{ $product->updated_at->format('d/m/Y H:i:s') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning me-2"><i class="fas fa-edit me-2"></i> แก้ไข</a>
            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบสินค้านี้?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt me-2"></i> ลบ</button>
            </form>
        </div>
    </div>
@endsection
