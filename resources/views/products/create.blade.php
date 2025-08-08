@extends('layouts.app')

@section('title', 'เพิ่มสินค้าใหม่')

@section('content')
    <div class="container py-4"> {{-- เพิ่ม .container เพื่อให้ได้ max-width ที่กำหนดใน app.blade.php --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-industry me-2 text-primary"></i> เพิ่มสินค้าใหม่</h1>
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

        <div class="card shadow-lg rounded-4">
            <div class="card-body p-4 p-md=5">
                {{-- เพิ่ม enctype="multipart/form-data" เพื่อรองรับการอัปโหลดไฟล์ --}}
                <form id="productsForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold text-gray-700">
                                    ชื่อสินค้า <span class="text-danger">*</span>
                                    <i class="fas fa-info-circle custom-tooltip-icon ms-1" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="ป้อนชื่อสินค้าเช่น,'paracetamol 500 mg film-coated tablet'">
                                    </i>
                                </label>
                                <input type="text" 
                                        class="form-control form-control-lg rounded-pill @error('name') is-invalid @enderror" 
                                        id="name" 
                                        name="name" 
                                        value="{{ old('name') }}" 
                                        required placeholder="กรอกชื่อสินค้า">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_code" class="form-label fw-bold text-gray-700">
                                    รหัสสินค้า <span class="text-danger">*</span>
                                    <i class="fas fa-info-circle custom-tooltip-icon ms-1" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="ป้อนรหัสสินค้า,'SKU-001'">
                                    </i>
                                </label>
                                <input type="text" 
                                        class="form-control form-control-lg rounded-pill @error('product_code') is-invalid @enderror" 
                                        id="product_code" 
                                        name="product_code" 
                                        value="{{ old('product_code') }}" 
                                        required placeholder="กรอกรหัสสินค้า (เช่น SKU-001)">
                                @error('product_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label fw-bold">
                                    หมวดหมู่ <span class="text-danger">*</span>
                                    <i class="fas fa-info-circle custom-tooltip-icon ms-1" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="เลือกหมวดหมู่ของสินค้าเช่น:,'ยาและเวชภัณฑ์'">
                                    </i>
                                </label>
                                <select class="form-select form-select-lg rounded-pill @error('category_id') is-invalid @enderror" 
                                        id="category_id" 
                                        name="category_id" required>
                                    <option value="">-- เลือกหมวดหมู่ --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_type_id" class="form-label fw-bold">
                                    ประเภทสินค้า
                                    <i class="fas fa-info-circle custom-tooltip-icon ms-1" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="เลือกประเภทสินค้าเช่น:,'วัสดุการแพทย์ทั่วไป','กลุ่มยาฆ่าเชื้อ (Antibiotic)'">
                                    </i>
                                </label>
                                <select class="form-select form-select-lg rounded-pill @error('product_type_id') is-invalid @enderror" id="product_type_id" name="product_type_id">
                                    <option value="">-- เลือกประเภทสินค้า --</option>
                                    @foreach ($productTypes as $productType)
                                        <option value="{{ $productType->id }}" {{ old('product_type_id') == $productType->id ? 'selected' : '' }}>{{ $productType->name }}</option>
                                    @endforeach
                                </select>
                                @error('product_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    {{-- เพิ่มช่องสำหรับ Manufacturer และ Product Type --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="manufacturer_id" class="form-label fw-bold">
                                    ผู้ผลิต
                                    <i class="fas fa-info-circle custom-tooltip-icon ms-1" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="เลือกผู้ผลิตเช่น:,'CEMOL FC (ฟาร์มาสันต์แล็บบอราตอรี่ส์) (paracetamol 500 mg) film-coated tablet'">
                                    </i>
                                </label>
                                <select class="form-select form-select-lg rounded-pill @error('manufacturer_id') is-invalid @enderror" id="manufacturer_id" name="manufacturer_id">
                                    <option value="">-- เลือกผู้ผลิต --</option>
                                    @foreach ($manufacturers as $manufacturer)
                                        <option value="{{ $manufacturer->id }}" {{ old('manufacturer_id') == $manufacturer->id ? 'selected' : '' }}>{{ $manufacturer->name }}</option>
                                    @endforeach
                                </select>
                                @error('manufacturer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="supplier_id" class="form-label fw-bold">
                                    ผู้จำหน่าย <span class="text-danger">*</span>
                                    <i class="fas fa-info-circle custom-tooltip-icon ms-1" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="เลือกผู้จำหน่ายเช่น:,'เชียงรายเภสัช'">
                                    </i>
                                </label>
                                <select class="form-select form-select-lg rounded-pill @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                                    <option value="">-- เลือกผู้จำหน่าย --</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>               
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="unit" class="form-label fw-bold">
                                    หน่วยนับ <span class="text-danger">*</span>
                                    <i class="fas fa-info-circle custom-tooltip-icon ms-1" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="ป้อนเช่น:,'ชิ้น','กล่อง','แผง'">
                                    </i>
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control rounded-pill @error('unit') is-invalid @enderror" 
                                    id="unit" 
                                    name="unit" 
                                    value="{{ old('unit') }}" 
                                    required placeholder="เช่น ชิ้น, กล่อง, แผง">
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="minimum_stock_level" class="form-label fw-bold">
                                    สต็อกต่ำสุดที่ต้องสั่งซื้อ <span class="text-danger">*</span>
                                    <i class="fas fa-info-circle custom-tooltip-icon ms-1" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="ป้อนจำนวนคงเหลือต่ำสุดที่ต้องสั่งซื้อเช่น:,'5' (จำเป็นต้องกรอก)">
                                    </i>
                                </label>
                                <input 
                                    type="number" 
                                    class="form-control rounded-pill @error('minimum_stock_level') is-invalid @enderror" 
                                    id="minimum_stock_level" 
                                    name="minimum_stock_level" 
                                    value="{{ old('minimum_stock_level', 0) }}" 
                                    min="0" 
                                    required placeholder="จำนวนสต็อกขั้นต่ำ">
                                @error('minimum_stock_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cost_price" class="form-label fw-bold">
                                    ราคาต้นทุน (ต่อหน่วย) <span class="text-danger">*</span>
                                    <i class="fas fa-info-circle custom-tooltip-icon ms-1" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="ป้อนราคาที่เราจัดซื้อมา (จำเป็นต้องกรอก)">
                                    </i>
                                </label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    class="form-control rounded-pill @error('cost_price') is-invalid @enderror" 
                                    id="cost_price" 
                                    name="cost_price" 
                                    value="{{ old('cost_price', 0.00) }}" 
                                    min="0" 
                                    required placeholder="ราคาที่จัดซื้อมา">
                                @error('cost_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label fw-bold">
                            สถานะ <span class="text-danger">*</span>
                            <i class="fas fa-info-circle custom-tooltip-icon ms-1" 
                                        data-bs-toggle="tooltip" 
                                        data-bs-placement="top" 
                                        title="สถานะการใช้งานของสินค้า (ใช้งาน/ไม่ใช้งาน)">
                                    </i>
                        </label>
                        <select 
                            class="form-select form-select-lg rounded-pill @error('status') is-invalid @enderror" 
                            id="status" 
                            name="status" required>
                            <option 
                                value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                    ใช้งาน
                            </option>
                            <option 
                                value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                    ไม่ใช้งาน
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- เพิ่มช่องสำหรับอัปโหลดรูปภาพ --}}
                    <div class="mb-3">
                            <label for="image" class="form-label fw-bold">รูปภาพสินค้า</label>
                            <input type="file" class="form-control rounded-pill @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                            <small class="form-text text-muted">รองรับไฟล์: JPG, PNG, GIF, SVG (ไม่เกิน 2MB)</small>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    <button type="submit" class="btn btn-success btn-lg shadow-sm rounded-pill px-5 py-2">
                        <i class="fas fa-save me-2"></i> บันทึก
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('image-preview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
