@extends('layouts.app')

@section('title', 'เพิ่มล็อตสินค้าใหม่')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-plus-circle me-2 text-primary"></i> เพิ่มล็อตสินค้าใหม่</h1>
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
            <form action="{{ route('batches.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="product_id" class="form-label fw-bold">สินค้า <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg rounded-pill @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                                <option value="">-- เลือกสินค้า --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }} ({{ $product->product_code }})</option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="batch_prefix" class="form-label fw-bold">รหัสล็อต <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-lg @error('batch_number') is-invalid @enderror" 
                                    id="batch_prefix" 
                                    placeholder="กรอกชื่อล็อต เช่น PARA500"
                                    oninput="generateBatchNumber()">
                                <span class="input-group-text" id="date_suffix">-{{ date('dmY') }}</span>
                                <input type="hidden" id="batch_number" name="batch_number" value="{{ old('batch_number') }}">
                            </div>
                            <small class="text-muted">รหัสล็อตที่จะได้: <strong id="preview_batch">-</strong></small>
                            @error('batch_number')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="quantity" class="form-label fw-bold">จำนวน <span class="text-danger">*</span></label>
                            <input type="number" class="form-control rounded-pill @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', 0) }}" min="0" required placeholder="จำนวนสินค้าในล็อต">
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="expiration_date" class="form-label fw-bold">วันหมดอายุ</label>
                            <input type="date" class="form-control rounded-pill @error('expiration_date') is-invalid @enderror" id="expiration_date" name="expiration_date" value="{{ old('expiration_date') }}">
                            @error('expiration_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="status" class="form-label fw-bold">สถานะ <span class="text-danger">*</span></label>
                    <select class="form-select form-select-lg rounded-pill @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="location_id" class="form-label fw-bold">ตำแหน่งเก็บสินค้า</label>
                    <select class="form-select form-select-lg rounded-pill @error('location_id') is-invalid @enderror" id="location_id" name="location_id">
                        <option value="">-- เลือกตำแหน่ง --</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->full_location }} (โซน {{ $location->zone }} - ชั้น {{ $location->shelf }} - ช่อง {{ $location->slot }})
                            </option>
                        @endforeach
                    </select>
                    @error('location_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success btn-lg shadow-sm rounded-pill px-5 py-2">
                    <i class="fas fa-save me-2"></i> บันทึก
                </button>
            </form>
        </div>
    </div>
    <script>
        function generateBatchNumber() {
            const prefix = document.getElementById('batch_prefix').value.trim();
            const today = new Date();
            const day = String(today.getDate()).padStart(2, '0');
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const year = today.getFullYear();
            const dateSuffix = day + month + year;
            
            const batchNumber = prefix ? prefix + '-' + dateSuffix : '';
            document.getElementById('batch_number').value = batchNumber;
            document.getElementById('preview_batch').textContent = batchNumber || '-';
            document.getElementById('date_suffix').textContent = '-' + dateSuffix;
        }

        // Generate on page load ถ้ามี old value
        document.addEventListener('DOMContentLoaded', function() {
            const oldValue = '{{ old('batch_number') }}';
            if (oldValue) {
                const parts = oldValue.split('-');
                if (parts.length >= 2) {
                    const prefix = parts.slice(0, -1).join('-');
                    document.getElementById('batch_prefix').value = prefix;
                }
                document.getElementById('preview_batch').textContent = oldValue;
            }
            generateBatchNumber();
        });
    </script>
@endsection
