@extends('layouts.app')

@section('title', 'แก้ไขผู้ผลิต: ' . $manufacturer->name)

@section('content')
    <div class="container py-4"> {{-- เพิ่ม .container เพื่อให้ได้ max-width ที่กำหนดใน app.blade.php --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-edit me-2 text-primary"></i> แก้ไขผู้ผลิต: <span class="text-secondary">{{ $manufacturer->name }}</span></h1>
        <a href="{{ route('manufacturers.index') }}" class="btn btn-secondary shadow-sm rounded-pill px-4 py-2">
            <i class="fas fa-arrow-alt-circle-left me-2"></i> กลับ
        </a>
    </div>

    {{-- แสดงข้อผิดพลาดจากการ Validation --}}
    @if ($errors->any())
            <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert"> {{-- ใช้ alert-custom เพื่อสไตล์ที่สอดคล้อง --}}
                <i class="fas fa-exclamation-triangle alert-icon"></i> {{-- ใช้ alert-icon --}}
                <div>
                    <h5 class="alert-heading mb-2">พบข้อผิดพลาด:</h5>
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
            <form id="manufacturersEditForm" action="{{ route('manufacturers.update', $manufacturer->id) }}" method="POST">
                @csrf
                @method('PUT') {{-- ใช้ PUT method สำหรับการอัปเดต --}}
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">ชื่อผู้ผลิต <span class="text-danger">*</span>
		    	        <i class="fas fa-info-circle ms-1 custom-tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="ป้อนชื่อผู้ผลิต เช่น 'บริษัท ถ้วยทองโอสถ จํากัด', 'บริษัท ซี เนเชอรัล ฟาร์มาซูติคอล จำกัด'"></i>
                    </label>
                    <input type="text" class="form-control form-control-lg rounded-pill @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $manufacturer->name) }}" required placeholder="กรอกชื่อผู้ผลิต">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="contact_person" class="form-label fw-bold">ผู้ติดต่อ
                        <i class="fas fa-info-circle ms-1 custom-tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="ป้อนผู้ติดต่อเช่น 'บริษัท ถ้วยทองโอสถ จํากัด', 'บริษัท ซี เนเชอรัล ฟาร์มาซูติคอล จำกัด'"></i>
                    </label>
                    <input type="text" class="form-control form-control-lg rounded-pill @error('contact_person') is-invalid @enderror" id="contact_person" name="contact_person" value="{{ old('contact_person', $manufacturer->contact_person) }}" placeholder="กรอกชื่อผู้ติดต่อ">
                    @error('contact_person')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label fw-bold">เบอร์โทรศัพท์
                        <i class="fas fa-info-circle ms-1 custom-tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="ป้อนเบอร์โทรศัพท์เช่น '0951111111'"></i>
                    </label>
                    <input type="text" class="form-control form-control-lg rounded-pill @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $manufacturer->phone) }}" placeholder="เบอร์โทรศัพท์">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">อีเมล
                        <i class="fas fa-info-circle ms-1 custom-tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="ป้อน E-mail เช่น'samble@gmail.com','(ถ้ามี)'"></i>
                    </label>
                    <input type="email" class="form-control form-control-lg rounded-pill @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $manufacturer->email) }}" placeholder="อีเมล">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label fw-bold">ที่อยู่
                        <i class="fas fa-info-circle ms-1 custom-tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="ป้อนที่อยู่ปัจจุบัน เช่น'5 หมู่ 3 ต.ริมกก อ.เมือง จ.เชียงราย 57100'"></i>
                    </label>
                    <textarea class="form-control rounded-3 @error('address') is-invalid @enderror" id="address" name="address" rows="4" placeholder="ที่อยู่ผู้ผลิต">{{ old('address', $manufacturer->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="status" class="form-label fw-bold">สถานะ <span class="text-danger">*</span>
                        <i class="fas fa-info-circle ms-1 custom-tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="เลือกสถานะของหมวดหมู่: 'ใช้งาน' หรือ 'ไม่ใช้งาน'"></i>
                    </label>
                    <select class="form-select form-select-lg rounded-pill @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="active" {{ old('status', $manufacturer->status) == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                        <option value="inactive" {{ old('status', $manufacturer->status) == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
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
@endsection
