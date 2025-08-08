@extends('layouts.app')

@section('title', 'เพิ่มหมวดหมู่ใหม่')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-plus-circle me-2 text-primary"></i> เพิ่มหมวดหมู่ใหม่</h1>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary shadow-sm rounded-pill px-4 py-2">
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
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf {{-- CSRF Token เพื่อความปลอดภัย --}}
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">ชื่อหมวดหมู่ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg rounded-pill @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="กรอกชื่อหมวดหมู่สินค้า">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">คำอธิบาย</label>
                    <textarea class="form-control rounded-3 @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="คำอธิบายเพิ่มเติมเกี่ยวกับหมวดหมู่">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                {{-- เพิ่มส่วนเลือกสถานะตรงนี้ --}}
                <div class="mb-4"> {{-- เพิ่ม mb-4 เพื่อระยะห่าง --}}
                    <label for="status" class="form-label fw-bold">สถานะ <span class="text-danger">*</span></label>
                    <select class="form-select form-select-lg rounded-pill @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-success btn-lg shadow-sm rounded-pill px-5 py-2">
                    <i class="fas fa-save me-2"></i> บันทึก
                </button>
            </form>
        </div>
    </div>
@endsection
