@extends('layouts.app')  {{-- ระบุว่าไฟล์นี้ใช้ layout หลักคือ layouts/app.blade.php --}}


@section('title', 'เพิ่มผู้ผลิตใหม่') {{-- กำหนด Title เฉพาะสำหรับหน้านี้ --}}

@section('content')   {{-- เนื้อหาของหน้านี้จะถูกแทรกเข้าไปใน @yield('content') ของ layouts/app.blade.php --}}
<div class="container mt-4"> {{-- ใช้ .container เพื่อให้ได้ max-width ที่กำหนดใน app.blade.php --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-industry me-2 text-primary"></i> เพิ่มผู้ผลิตใหม่</h1>
        {{-- Back button, using Bootstrap btn classes and custom rounded-pill --}}
        <a href="#" onclick="history.back(); return false;" class="btn btn-secondary shadow-sm rounded-pill px-4 py-2">
                <i class="fas fa-arrow-alt-circle-left me-2"></i> กลับ
            </a>
    </div>

    {{-- Display validation errors from Laravel --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-custom mb-4" role="alert">
            <i class="fas fa-exclamation-triangle alert-icon"></i>
            <div>
                <h4 class="alert-heading">โอ้! มีบางอย่างผิดพลาด!</h4>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-lg border-0 rounded-xl">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('manufacturers.store') }}" method="POST" id="manufacturerForm"> {{-- Added ID for JavaScript --}}
                @csrf

                {{-- Manufacturer Name Field --}}
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold text-gray-700">
                        ชื่อผู้ผลิต <span class="text-danger">*</span>
                        <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                           data-bs-toggle="tooltip" data-bs-placement="top"
                           title="ป้อนชื่อเต็มของผู้ผลิตหรือบริษัท"></i>
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           class="form-control form-control-lg rounded-pill @error('name') is-invalid @enderror"
                           value="{{ old('name') }}"
                           placeholder="ป้อนชื่อผู้ผลิต"
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Contact Person Field --}}
                <div class="mb-3">
                    <label for="contact_person" class="form-label fw-bold text-gray-700">
                        ผู้ติดต่อ
                        <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                           data-bs-toggle="tooltip" data-bs-placement="top"
                           title="ชื่อบุคคลที่สามารถติดต่อได้ของผู้ผลิต"></i>
                    </label>
                    <input type="text"
                           name="contact_person"
                           id="contact_person"
                           class="form-control form-control-lg rounded-pill @error('contact_person') is-invalid @enderror"
                           value="{{ old('contact_person') }}"
                           placeholder="ป้อนชื่อผู้ติดต่อ">
                    @error('contact_person')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Phone Field --}}
                <div class="mb-3">
                    <label for="phone" class="form-label fw-bold text-gray-700">
                        เบอร์โทรศัพท์
                        <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                           data-bs-toggle="tooltip" data-bs-placement="top"
                           title="เบอร์โทรศัพท์สำหรับติดต่อผู้ผลิต"></i>
                    </label>
                    <input type="text"
                           name="phone"
                           id="phone"
                           class="form-control form-control-lg rounded-pill @error('phone') is-invalid @enderror"
                           value="{{ old('phone') }}"
                           placeholder="ป้อนเบอร์โทรศัพท์">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email Field --}}
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold text-gray-700">
                        อีเมล
                        <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                           data-bs-toggle="tooltip" data-bs-placement="top"
                           title="ที่อยู่อีเมลของผู้ผลิต (ถ้ามี)"></i>
                    </label>
                    <input type="email"
                           name="email"
                           id="email"
                           class="form-control form-control-lg rounded-pill @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="ป้อนอีเมล">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Address Field --}}
                <div class="mb-3">
                    <label for="address" class="form-label fw-bold text-gray-700">
                        ที่อยู่
                        <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                           data-bs-toggle="tooltip" data-bs-placement="top"
                           title="ที่อยู่เต็มของผู้ผลิต"></i>
                    </label>
                    <textarea name="address"
                              id="address"
                              rows="4"
                              class="form-control @error('address') is-invalid @enderror"
                              placeholder="ป้อนที่อยู่">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status Field --}}
                <div class="mb-4">
                    <label for="status" class="form-label fw-bold text-gray-700">
                        สถานะ <span class="text-danger">*</span>
                        <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                           data-bs-toggle="tooltip" data-bs-placement="top"
                           title="สถานะการใช้งานของผู้ผลิต (ใช้งาน/ไม่ใช้งาน)"></i>
                    </label>
                    <select name="status"
                            id="status"
                            class="form-select rounded-pill @error('status') is-invalid @enderror"
                            required>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Submit Button with Loading State --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill" id="submitButton">
                        <span id="buttonText"><i class="fas fa-save me-2"></i> บันทึกผู้ผลิต</span>
                        <span id="loadingSpinner" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true" style="display: none;"></span>
                        <span id="loadingText" style="display: none;">กำลังบันทึก...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('manufacturerForm');
        const submitButton = document.getElementById('submitButton');
        const buttonText = document.getElementById('buttonText');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const loadingText = document.getElementById('loadingText');

        form.addEventListener('submit', function() {
            // Disable the button to prevent multiple submissions
            submitButton.disabled = true;
            // Hide original text and show spinner and loading text
            buttonText.style.display = 'none';
            loadingSpinner.style.display = 'inline-block';
            loadingText.style.display = 'inline-block';
        });

        // Initialize Bootstrap Tooltips (ensure this is also in app.blade.php or only here)
        // If app.blade.php already initializes all tooltips, this block can be removed.
        // For safety, I'll keep it here, but it might be redundant if global init works.
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush
