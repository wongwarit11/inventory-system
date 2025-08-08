@extends('layouts.app')

@section('title', 'เพิ่มหมวดหมู่ใหม่')

@section('content')
    <div class="container py-4"> {{-- เพิ่ม .container เพื่อให้ได้ max-width ที่กำหนดใน app.blade.php --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-plus-circle me-2 text-primary"></i> เพิ่มหมวดหมู่ใหม่</h1>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary shadow-sm rounded-pill px-4 py-2">
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
                <form id="categoryCreateForm" action="{{ route('categories.store') }}" method="POST">
                    @csrf {{-- CSRF Token เพื่อความปลอดภัย --}}
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">
                            ชื่อหมวดหมู่ <span class="text-danger">*</span>
                            <i class="fas fa-info-circle ms-1 custom-tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="ป้อนชื่อหมวดหมู่ เช่น 'อุปกรณ์สำนักงาน', 'เครื่องใช้ไฟฟ้า', 'เฟอร์นิเจอร์'"></i>
                        </label>
                        <input type="text" class="form-control form-control-lg rounded-pill @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="กรอกชื่อหมวดหมู่สินค้า">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">
                            คำอธิบาย
                            <i class="fas fa-info-circle ms-1 custom-tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="คำอธิบายเพิ่มเติมเกี่ยวกับหมวดหมู่นี้ (ไม่บังคับ)"></i>
                        </label>
                        <textarea class="form-control rounded-3 @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="คำอธิบายเพิ่มเติมเกี่ยวกับหมวดหมู่">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- เพิ่มส่วนเลือกสถานะตรงนี้ --}}
                    <div class="mb-4"> {{-- เพิ่ม mb-4 เพื่อระยะห่าง --}}
                        <label for="status" class="form-label fw-bold">
                            สถานะ <span class="text-danger">*</span>
                            <i class="fas fa-info-circle ms-1 custom-tooltip-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="เลือกสถานะของหมวดหมู่: 'ใช้งาน' หรือ 'ไม่ใช้งาน'"></i>
                        </label>
                        <select class="form-select form-select-lg rounded-pill @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" id="submitButton" class="btn btn-primary btn-lg shadow-sm rounded-pill px-5 py-2 w-100">
                        <span id="buttonText"><i class="fas fa-plus-circle me-2"></i> เพิ่มหมวดหมู่</span>
                        <span id="loadingSpinner" class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true" style="display: none;"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts') {{-- เพิ่ม JavaScript เฉพาะหน้านี้ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            const form = document.getElementById('categoryCreateForm');
            const submitButton = document.getElementById('submitButton');
            const buttonText = document.getElementById('buttonText');
            const loadingSpinner = document.getElementById('loadingSpinner');

            form.addEventListener('submit', function(event) {
                // Show Loading State
                submitButton.disabled = true;
                buttonText.style.display = 'none';
                loadingSpinner.style.display = 'inline-block';
                submitButton.classList.add('btn-secondary', 'cursor-not-allowed');
                submitButton.classList.remove('btn-primary'); // Remove primary color
            });

            // If there are validation errors after submission (Laravel redirect back with errors)
            // Reset button state when page loads
            @if ($errors->any())
                submitButton.disabled = false;
                buttonText.style.display = 'inline';
                loadingSpinner.style.display = 'none';
                submitButton.classList.remove('btn-secondary', 'cursor-not-allowed');
                submitButton.classList.add('btn-primary'); // Add primary color back
            @endif
        });
    </script>
@endpush
