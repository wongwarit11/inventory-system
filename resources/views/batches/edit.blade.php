@extends('layouts.app')

@section('title', 'แก้ไขล็อตสินค้า: ' . $batch->batch_number)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-edit me-2 text-primary"></i> แก้ไขล็อตสินค้า: <span class="text-secondary">{{ $batch->batch_number }}</span></h1>
        <a href="{{ route('batches.index') }}" class="btn btn-secondary shadow-sm rounded-pill px-4 py-2">
            <i class="fas fa-arrow-alt-circle-left me-2"></i> กลับ
        </a>
    </div>

    {{-- แสดงข้อผิดพลาดจากการ Validation --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <h5 class="alert-heading mb-2"><i class="fas fa-exclamation-triangle me-2"></i> พบข้อผิดพลาด:</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-lg rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('batches.update', $batch->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="product_id" class="form-label fw-bold">สินค้า <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg rounded-pill @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                                <option value="">-- เลือกสินค้า --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id', $batch->product_id) == $product->id ? 'selected' : '' }}>{{ $product->name }} ({{ $product->product_code }})</option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="batch_number" class="form-label fw-bold">รหัสล็อต <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg rounded-pill @error('batch_number') is-invalid @enderror" id="batch_number" name="batch_number" value="{{ old('batch_number', $batch->batch_number) }}" required placeholder="กรอกรหัสล็อตสินค้า">
                            @error('batch_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="quantity" class="form-label fw-bold">จำนวน <span class="text-danger">*</span></label>
                            <input type="number" class="form-control rounded-pill @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', $batch->quantity) }}" min="0" required placeholder="จำนวนสินค้าในล็อต">
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="production_date" class="form-label fw-bold">วันที่ผลิต</label>
                            <input type="date" class="form-control rounded-pill @error('production_date') is-invalid @enderror" id="production_date" name="production_date" value="{{ old('production_date', $batch->production_date) }}">
                            @error('production_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="expiration_date" class="form-label fw-bold">วันหมดอายุ</label>
                            <input type="date" class="form-control rounded-pill @error('expiration_date') is-invalid @enderror" id="expiration_date" name="expiration_date" value="{{ old('expiration_date', $batch->expiration_date) }}">
                            @error('expiration_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="status" class="form-label fw-bold">สถานะ <span class="text-danger">*</span></label>
                    <select class="form-select form-select-lg rounded-pill @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="active" {{ old('status', $batch->status) == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                        <option value="inactive" {{ old('status', $batch->status) == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success btn-lg shadow-sm rounded-pill px-5 py-2">
                    <i class="fas fa-save me-2"></i> บันทึกการแก้ไข
                </button>
            </form>
        </div>
    </div>
@endsection
