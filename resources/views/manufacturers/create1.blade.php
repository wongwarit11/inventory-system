@extends('layouts.app')

@section('title', 'เพิ่มผู้ผลิตใหม่')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-plus-circle me-2 text-primary"></i> เพิ่มผู้ผลิตใหม่</h1>
        <a href="{{ route('manufacturers.index') }}" class="btn btn-secondary shadow-sm rounded-pill px-4 py-2">
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
            <form action="{{ route('manufacturers.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">ชื่อผู้ผลิต <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg rounded-pill @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="กรอกชื่อผู้ผลิต">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="contact_person" class="form-label fw-bold">ผู้ติดต่อ</label>
                    <input type="text" class="form-control rounded-pill @error('contact_person') is-invalid @enderror" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" placeholder="ชื่อผู้ติดต่อ">
                    @error('contact_person')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label fw-bold">เบอร์โทรศัพท์</label>
                    <input type="text" class="form-control rounded-pill @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="เบอร์โทรศัพท์">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">อีเมล</label>
                    <input type="email" class="form-control rounded-pill @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="อีเมล">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label fw-bold">ที่อยู่</label>
                    <textarea class="form-control rounded-3 @error('address') is-invalid @enderror" id="address" name="address" rows="4" placeholder="ที่อยู่ผู้ผลิต">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
                <button type="submit" class="btn btn-success btn-lg shadow-sm rounded-pill px-5 py-2">
                    <i class="fas fa-save me-2"></i> บันทึก
                </button>
            </form>
        </div>
    </div>
@endsection
