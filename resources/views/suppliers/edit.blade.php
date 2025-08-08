@extends('layouts.app')

@section('title', 'แก้ไขผู้จำหน่าย: ' . $supplier->name)

@section('content')
    <div class="container py-4"> {{-- เพิ่ม .container เพื่อให้ได้ max-width ที่กำหนดใน app.blade.php --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-edit me-2 text-primary"></i> แก้ไขผู้จำหน่าย: <span class="text-secondary">{{ $supplier->name }}</span></h1> {{-- เพิ่ม text-primary และ text-secondary --}}
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary shadow-sm rounded-pill px-4 py-2"> {{-- ปรับสไตล์ปุ่ม --}}
                <i class="fas fa-arrow-alt-circle-left me-2"></i> กลับ
            </a>
        </div>

        {{-- แสดงข้อผิดพลาดจากการ Validation --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
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

        <div class="card shadow-lg rounded-4"> {{-- ปรับสไตล์ card --}}
            <div class="card-body p-4"> {{-- ปรับ padding --}}
                <form id="suppliersEditForm" action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- ใช้ PUT method สำหรับการอัปเดต --}}
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">ชื่อผู้จำหน่าย <span class="text-danger">*</span>
                            <i class="fas fa-info-circle ms-1 custom-tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="ป้อนชื่อจำหน่าย เช่น 'บริษัท ถ้วยทองโอสถ จํากัด', 'บริษัท ซี เนเชอรัล ฟาร์มาซูติคอล จำกัด'"></i>
                        </label> {{-- เพิ่ม fw-bold --}}
                        <input type="text" class="form-control form-control-lg rounded-pill @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $supplier->name) }}" required placeholder="กรอกชื่อผู้จำหน่าย"> {{-- ปรับสไตล์ input และเพิ่ม placeholder --}}
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="contact_person" class="form-label fw-bold">ผู้ติดต่อ
                            <i class="fas fa-info-circle ms-1 custom-tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="ป้อนชื่อผู้ติดต่อ เช่น 'ชื่อบริษัท', 'ชื่อตัวแทนจำหน่วย'"></i>
                        </label> {{-- เพิ่ม fw-bold --}}
                        <input type="text" class="form-control form-control-lg rounded-pill @error('contact_person') is-invalid @enderror" id="contact_person" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" placeholder="ชื่อผู้ติดต่อ"> {{-- ปรับสไตล์ input และเพิ่ม placeholder --}}
                        @error('contact_person')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label fw-bold">เบอร์โทรศัพท์
                            <i class="fas fa-info-circle ms-1 custom-tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="ป้อนเบอร์โทรศัพท์เช่น '0951111111'"></i>
                        </label> {{-- เพิ่ม fw-bold --}}
                        <input type="text" class="form-control form-contro-lg rounded-pill @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}" placeholder="เบอร์โทรศัพท์"> {{-- ปรับสไตล์ input และเพิ่ม placeholder --}}
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">อีเมล
                            <i class="fas fa-info-circle ms-1 custom-tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="ป้อน E-mail เช่น'samble@gmail.com','(ถ้ามี)'"></i>
                        </label> {{-- เพิ่ม fw-bold --}}
                        <input type="email" class="form-control form-control-lg rounded-pill @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $supplier->email) }}" placeholder="อีเมล"> {{-- ปรับสไตล์ input และเพิ่ม placeholder --}}
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label fw-bold">ที่อยู่
                            <i class="fas fa-info-circle ms-1 custom-tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="ป้อนที่อยู่ปัจจุบัน เช่น'5 หมู่ 3 ต.ริมกก อ.เมือง จ.เชียงราย 57100'"></i>
                        </label> {{-- เพิ่ม fw-bold --}}
                        <textarea class="form-control form-control-lg rounded-3 @error('address') is-invalid @enderror" id="address" name="address" rows="4" placeholder="ที่อยู่ผู้จำหน่าย">{{ old('address', $supplier->address) }}</textarea> {{-- ปรับสไตล์ textarea และเพิ่ม placeholder --}}
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- เพิ่มส่วนเลือกสถานะตรงนี้ --}}
                    <div class="mb-4"> {{-- เพิ่ม mb-4 เพื่อระยะห่าง --}}
                        <label for="status" class="form-label fw-bold">สถานะ <span class="text-danger">*</span>
                            <i class="fas fa-info-circle ms-1 custom-tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="เลือกสถานะของหมวดหมู่: 'ใช้งาน' หรือ 'ไม่ใช้งาน'"></i>
                        </label> {{-- เพิ่ม fw-bold --}}
                        <select class="form-select form-select-lg rounded-pill @error('status') is-invalid @enderror" id="status" name="status" required> {{-- ปรับสไตล์ select --}}
                            <option value="active" {{ old('status', $supplier->status) == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                            <option value="inactive" {{ old('status', $supplier->status) == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                <button type="submit" id="submitButton" class="btn btn-primary btn-lg shadow-sm rounded-pill px-5 py-2 w-100">
                    <span id="buttonText"><i class="fas fa-save me-2"></i> บันทึกการแก้ไข</span>
                    <span id="loadingSpinner" class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true" style="display: none;"></span>
                </button>
                </form>
            </div>
        </div>
    </div>
@endsection
