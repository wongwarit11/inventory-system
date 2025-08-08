@extends('layouts.app')

{{-- แก้ไข title ให้ใช้ $productType->name --}}
@section('title', 'แก้ไขประเภทสินค้า: ' . $productType->name)

@section('content')
    <div class="container py-4"> {{-- เพิ่ม .container เพื่อให้ได้ max-width ที่กำหนดใน app.blade.php --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            {{-- แก้ไขการแสดงชื่อประเภทสินค้าให้ใช้ $productType->name --}}
            <h1><i class="fas fa-edit me-2 text-primary"></i> แก้ไขประเภทสินค้า: <span class="text-secondary">{{ $productType->name }}</span></h1>
            <a href="{{ route('product-types.index') }}" class="btn btn-secondary shadow-sm rounded-pill px-4 py-2">
                <i class="fas fa-arrow-alt-circle-left me-2"></i> กลับ
            </a>
        </div>

        {{-- แสดงข้อผิดพลาดจากการ Validation --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-custom alert-dismissible fade show " role="alert">
                <i class="fas fa-exclamation-triangle alert-icon"></i> {{-- ใช้ alert-icon --}}
                <div>
                    <h5 class="alert-heading mb-2"> พบข้อผิดพลาด:</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-lg rounded-4">
            <div class="card-body p-4">
                {{-- แก้ไข route สำหรับ form action ให้ใช้ $productType->id --}}
                <form id="product-typesEditForm" action="{{ route('product-types.update', $productType->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">
                            ชื่อประเภทสินค้า <span class="text-danger">*</span>
                            <i class="fas fa-info-circle ms-1 custom-tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="ป้อนประเภทสินค้า เช่น 'วัสดุการแพทย์ทั่วไป', 'วัสดุทันตกรรม', 'วัสดุเอกซเรย์'"></i>
                        </label>
                        {{-- แก้ไข value ให้ใช้ $productType->name --}}
                        <input type="text" class="form-control form-control-lg rounded-pill @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $productType->name) }}" required placeholder="กรอกชื่อประเภทสินค้า">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">คำอธิบาย
                            <i class="fas fa-info-circle ms-1 custom-tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="คำอธิบายเพิ่มเติมเกี่ยวกับประเภทสินค้า (ไม่บังคับ)"></i>
                        </label>
                        {{-- แก้ไข value ให้ใช้ $productType->description --}}
                        <textarea class="form-control rounded-3 @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="คำอธิบายเพิ่มเติมเกี่ยวกับประเภทสินค้า">{{ old('description', $productType->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="status" class="form-label fw-bold">
                            สถานะ <span class="text-danger">*</span>
                            <i class="fas fa-info-circle ms-1 custom-tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="เลือกสถานะของแผนก: 'ใช้งาน' หรือ 'ไม่ใช้งาน'"></i>
                        </label>
                        {{-- แก้ไข selected option ให้ใช้ $productType->status --}}
                        <select class="form-select form-select-lg rounded-pill @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="active" {{ old('status', $productType->status) == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                            <option value="inactive" {{ old('status', $productType->status) == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" id="submitButton" class="btn btn-success btn-lg shadow-sm rounded-pill px-5 py-2 w-100">
                        <span id="buttonText"><i class="fas fa-save me-2"></i> บันทึกการแก้ไข</span>
                        <span id="loadingSpinner" class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true" style="display: none;"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
