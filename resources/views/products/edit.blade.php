    @extends('layouts.app')

    @section('title', 'แก้ไขสินค้า: ' . $product->name)

    @section('content')
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-edit me-2 text-primary"></i> แก้ไขสินค้า: <span class="text-secondary">{{ $product->name }}</span></h1>
            <a href="{{ route('products.index') }}" class="btn btn-secondary shadow-sm rounded-pill px-4 py-2">
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
                {{-- เพิ่ม enctype="multipart/form-data" เพื่อรองรับการอัปโหลดไฟล์ --}}
                <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">ชื่อสินค้า <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg rounded-pill @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required placeholder="กรอกชื่อสินค้า">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_code" class="form-label fw-bold">รหัสสินค้า <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg rounded-pill @error('product_code') is-invalid @enderror" id="product_code" name="product_code" value="{{ old('product_code', $product->product_code) }}" required placeholder="กรอกรหัสสินค้า (เช่น SKU-001)">
                                @error('product_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label fw-bold">หมวดหมู่ <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg rounded-pill @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="">-- เลือกหมวดหมู่ --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_type_id" class="form-label fw-bold">ประเภทสินค้า</label>
                                <select class="form-select form-select-lg rounded-pill @error('product_type_id') is-invalid @enderror" id="product_type_id" name="product_type_id">
                                    <option value="">-- เลือกประเภทสินค้า --</option>
                                    @foreach ($productTypes as $productType)
                                        <option value="{{ $productType->id }}" {{ old('product_type_id', $product->product_type_id) == $productType->id ? 'selected' : '' }}>{{ $productType->name }}</option>
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
                                <label for="manufacturer_id" class="form-label fw-bold">ผู้ผลิต</label>
                                <select class="form-select form-select-lg rounded-pill @error('manufacturer_id') is-invalid @enderror" id="manufacturer_id" name="manufacturer_id">
                                    <option value="">-- เลือกผู้ผลิต --</option>
                                    @foreach ($manufacturers as $manufacturer)
                                        <option value="{{ $manufacturer->id }}" {{ old('manufacturer_id', $product->manufacturer_id) == $manufacturer->id ? 'selected' : '' }}>{{ $manufacturer->name }}</option>
                                    @endforeach
                                </select>
                                @error('manufacturer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="supplier_id" class="form-label fw-bold">ผู้จำหน่าย <span class="text-danger">*</span></label>
                                <select class="form-select form-select-lg rounded-pill @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                                    <option value="">-- เลือกผู้จำหน่าย --</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
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
                                <label for="unit" class="form-label fw-bold">หน่วยนับ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-pill @error('unit') is-invalid @enderror" id="unit" name="unit" value="{{ old('unit', $product->unit) }}" required placeholder="เช่น ชิ้น, กล่อง, แผง">
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="minimum_stock_level" class="form-label fw-bold">สต็อกต่ำสุดที่ต้องสั่งซื้อ <span class="text-danger">*</span></label>
                                <input type="number" class="form-control rounded-pill @error('minimum_stock_level') is-invalid @enderror" id="minimum_stock_level" name="minimum_stock_level" value="{{ old('minimum_stock_level', $product->minimum_stock_level) }}" min="0" required placeholder="จำนวนสต็อกขั้นต่ำ">
                                @error('minimum_stock_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cost_price" class="form-label fw-bold">ราคาต้นทุน (ต่อหน่วย) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control rounded-pill @error('cost_price') is-invalid @enderror" id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" min="0" required placeholder="ราคาต้นทุน">
                                @error('cost_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label fw-bold">สถานะ <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg rounded-pill @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                            <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
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

                    {{-- แสดงรูปภาพปัจจุบันและตัวเลือกในการลบ --}}
                    @if ($product->image_path)
                        <div class="mb-4">
                            <label class="form-label fw-bold">รูปภาพปัจจุบัน:</label><br>
                            <img src="{{ asset('storage/' . $product->image_path) }}" alt="Product Image" class="img-thumbnail" style="max-width: 200px; height: auto;" id="image-preview">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                <label class="form-check-label" for="remove_image">
                                    ลบรูปภาพปัจจุบัน
                                </label>
                            </div>
                        </div>
                    @else
                        {{-- เพิ่ม div สำหรับแสดง preview เผื่อกรณีที่ยังไม่มีรูปภาพเดิม --}}
                        <div class="mb-4" id="image-preview-container" style="display: none;">
                            <label class="form-label fw-bold">รูปภาพตัวอย่าง:</label><br>
                            <img src="#" alt="Image Preview" class="img-thumbnail" style="max-width: 200px; height: auto; display: none;" id="image-preview">
                        </div>
                    @endif

                    <button type="submit" class="btn btn-success btn-lg shadow-sm rounded-pill px-5 py-2">
                        <i class="fas fa-save me-2"></i> บันทึกการแก้ไข
                    </button>
                </form>
            </div>
        </div>

        <script>
            function previewImage(event) {
                const reader = new FileReader();
                reader.onload = function() {
                    const output = document.getElementById('image-preview');
                    const container = document.getElementById('image-preview-container');

                    // ตรวจสอบว่ามี container และ output หรือไม่ก่อนใช้งาน
                    if (output) {
                        output.src = reader.result;
                        output.style.display = 'block';
                    }
                    if (container) {
                        container.style.display = 'block'; // แสดง container เมื่อมีรูปภาพ
                    }
                };
                // ตรวจสอบว่ามีไฟล์ที่เลือกหรือไม่
                if (event.target.files && event.target.files[0]) {
                    reader.readAsDataURL(event.target.files[0]);
                } else {
                    // ถ้าไม่มีไฟล์เลือก ให้ซ่อน preview
                    const output = document.getElementById('image-preview');
                    const container = document.getElementById('image-preview-container');
                    if (output) {
                        output.src = '#';
                        output.style.display = 'none';
                    }
                    if (container) {
                        container.style.display = 'none';
                    }
                }

                // ถ้ามีการเลือกรูปภาพใหม่ ให้ยกเลิกการเลือก "ลบรูปภาพปัจจุบัน"
                const removeImageCheckbox = document.getElementById('remove_image');
                if (removeImageCheckbox) {
                    removeImageCheckbox.checked = false;
                }
            }
        </script>
    @endsection
    