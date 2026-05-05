@extends('layouts.app')

@section('title', 'รายละเอียดล็อตสินค้า')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-boxes me-2"></i> รายละเอียดล็อตสินค้า</h1>
        <a href="{{ route('batches.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-alt-circle-left me-2"></i> กลับหน้ารายการล็อตสินค้า</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">ข้อมูลล็อตสินค้า: {{ $batch->batch_number }}</h5>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-4">สินค้า:</dt>
                <dd class="col-sm-8">{{ $batch->product->name ?? '-' }} ({{ $batch->product->product_code ?? '-' }})</dd>

                <dt class="col-sm-4">รหัสล็อตออนไลน์:</dt>
                <dd class="col-sm-8">{{ $batch->batch_number }}</dd>

                <dt class="col-sm-4">ตำแหน่งเก็บ:</dt>
                <dd class="col-sm-8">
                    @if ($batch->location)
                        <strong>{{ $batch->location->full_location }}</strong><br>
                        <small class="text-muted">โซน {{ $batch->location->zone }} - ชั้น {{ $batch->location->shelf }} - ช่อง {{ $batch->location->slot }}</small>
                    @else
                        <span class="text-muted">ยังไม่กำหนด</span>
                    @endif
                </dd>

                <dt class="col-sm-4">จำนวน:</dt>
                <dd class="col-sm-8">{{ number_format($batch->quantity) }} {{ $batch->product->unit ?? '' }}</dd>

                <dt class="col-sm-4">วันผลิต:</dt>
                <dd class="col-sm-8">{{ $batch->manufacture_date ? \Carbon\Carbon::parse($batch->manufacture_date)->format('d/m/Y') : '-' }}</dd>

                <dt class="col-sm-4">วันหมดอายุ:</dt>
                <dd class="col-sm-8">{{ $batch->expiration_date ? \Carbon\Carbon::parse($batch->expiration_date)->format('d/m/Y') : '-' }}</dd>

                <dt class="col-sm-4">สถานะ:</dt>
                <dd class="col-sm-8">
                    <span class="badge {{ $batch->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ $batch->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                    </span>
                </dd>

                <dt class="col-sm-4">สร้างเมื่อ:</dt>
                <dd class="col-sm-8">{{ $batch->created_at->format('d/m/Y H:i:s') }}</dd>

                <dt class="col-sm-4">อัปเดตล่าสุด:</dt>
                <dd class="col-sm-8">{{ $batch->updated_at->format('d/m/Y H:i:s') }}</dd>
            </dl>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('batches.barcode', $batch->id) }}" class="btn btn-info me-2"><i class="fas fa-barcode me-2"></i> พิมพ์บาร์โค้ด</a>
            <a href="{{ route('batches.edit', $batch->id) }}" class="btn btn-warning me-2"><i class="fas fa-edit me-2"></i> แก้ไข</a>
            <form action="{{ route('batches.destroy', $batch->id) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบล็อตสินค้านี้?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt me-2"></i> ลบ</button>
            </form>
        </div>
    </div>
@endsection
