@extends('layouts.app')

@section('title', 'รับเข้าสินค้า')

@section('content')
    <div class="container py-4"> {{-- เพิ่ม .container เพื่อให้ได้ max-width ที่กำหนดใน app.blade.php --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-arrow-alt-circle-down me-2 text-primary"></i> รับเข้าสินค้า</h1>
            <a href="{{ route('stock_transactions.index') }}" class="btn btn-secondary shadow-sm rounded-pill px-4 py-2">
                <i class="fas fa-arrow-alt-circle-left me-2"></i> กลับหน้ารายการสต็อก
            </a>
        </div>

        {{-- แสดงข้อผิดพลาดจากการ Validation --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                <div>
                    <i class="fas fa-exclamation-triangle alert-icon"></i>
                    <h4 class="alert-heading "> พบข้อผิดพลาด:</h4>
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
            <div class="card-body p-4 p-md-5">
                <form id="stock_transactionsFrom" action="{{ route('stock_transactions.receive.store') }}" method="POST">
                    @csrf

                    {{-- Stock_Transactions name field  --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_id" class="form-label fw-bold">
                                    สินค้า <span class="text-danger">*</span>
                                    <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="เลือกรายการสินค้ารับเข้า"></i>
                                </label>
                                <select class="form-select form-select-lg rounded-pill @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                                    <option value="">-- เลือกสินค้า --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" data-unit="{{ $product->unit }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }} ({{ $product->product_code }})</option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="batch_selection_type" class="form-label fw-bold">
                                    เลือกล็อตสินค้า / สร้างใหม่ <span class="text-danger">*</span>
                                    <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="เลือกล็อตสินค้า/สร้างใหม่เช่น: 'L0001'"></i>
                                </label>
                                <select class="form-select form-select-lg rounded-pill @error('batch_id') is-invalid @enderror" id="batch_selection_type" name="batch_selection_type" required>
                                    <option value="">-- เลือกล็อตสินค้า หรือ สร้างใหม่ --</option>
                                    <option value="existing" {{ old('batch_selection_type') == 'existing' ? 'selected' : '' }}>เลือกล็อตสินค้าที่มีอยู่</option>
                                    <option value="new" {{ old('batch_selection_type') == 'new' ? 'selected' : '' }}>สร้างล็อตสินค้าใหม่</option>
                                </select>
                                @error('batch_id') {{-- ใช้ batch_id error เพื่อครอบคลุมทั้งสองกรณี --}}
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- ส่วนสำหรับเลือกล็อตสินค้าที่มีอยู่ --}}
                    <div id="existing_batch_fields" style="display: {{ old('batch_selection_type') == 'existing' ? 'block' : 'none' }};">
                        <div class="mb-3">
                            <label for="batch_id" class="form-label fw-bold">
                                เลือกล็อตสินค้า <span class="text-danger">*</span>
                                <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="เลือกล็อตสินค้าที่ต้องการรับเข้า(ใช้ในกรณีที่มีสินค้าตกหล่นจากรอบส่งที่ผ่านมา)"></i>
                            </label>
                            <select class="form-select form-select-lg rounded-pill @error('batch_id') is-invalid @enderror" id="batch_id" name="batch_id">
                                <option value="">-- เลือกล็อตสินค้า --</option>
                                {{-- Options จะถูกโหลดด้วย AJAX --}}
                            </select>
                            @error('batch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ส่วนสำหรับสร้างล็อตสินค้าใหม่ --}}
                    <div id="new_batch_fields" style="display: {{ old('batch_selection_type') == 'new' ? 'block' : 'none' }};">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="new_batch_number" class="form-label fw-bold">
                                        รหัสล็อตใหม่ <span class="text-danger">*</span>
                                        <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="ระบุล็อตสินค้าใหม่ที่รับเข้ามา เพื่อความสดวกในการตรวจเช็คสินค้าคงคลังและสินค้าวันหมดอายุ"></i>
                                    </label>
                                    <input type="text" class="form-control form-control-lg rounded-pill @error('new_batch_number') is-invalid @enderror" id="new_batch_number" name="new_batch_number" value="{{ old('new_batch_number') }}" placeholder="กรอกรหัสล็อตสินค้าใหม่">
                                    @error('new_batch_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="production_date" class="form-label fw-bold">
                                        วันที่ผลิต
                                        <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="ระบุวันที่ผลิตสินค้าทุกครั้ง(ถ้ามี)"></i>
                                    </label>
                                    <input type="date" class="form-control rounded-pill @error('production_date') is-invalid @enderror" id="production_date" name="production_date" value="{{ old('production_date') }}">
                                    @error('production_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="expiration_date" class="form-label fw-bold">
                                วันหมดอายุ
                                <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="ระบุวันที่หมดอายุทุกครั้ง 'สำคัญ' ใช้ตรวจเช็ควันหมดอายุของสินค้าและการแจ้งเตือน"></i>
                            </label>
                            <input type="date" class="form-control rounded-pill @error('expiration_date') is-invalid @enderror" id="expiration_date" name="expiration_date" value="{{ old('expiration_date') }}">
                            @error('expiration_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quantity" class="form-label fw-bold">
                                    จำนวนที่รับเข้า <span class="text-danger">*</span>
                                    <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="ระบุจำนวนสินค้าที่รับเข้าสต๊อกเป็นตัวเลขเช่น: '100'"></i>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control form-control-lg rounded-start-pill @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', 0) }}" min="1" required placeholder="จำนวน">
                                    <span class="input-group-text rounded-end-pill" id="unit_display">หน่วย</span>
                                </div>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_date" class="form-label fw-bold">
                                    วันที่ทำรายการ <span class="text-danger">*</span>
                                    <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="ระบุวันที่ทำรายการ/วันที่รับเข้า"></i>
                                </label>
                                <input type="date" class="form-control form-control-lg rounded-pill @error('transaction_date') is-invalid @enderror" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                                @error('transaction_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="department_id" class="form-label fw-bold">
                            แผนกที่รับ (ถ้ามี)
                            <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="ระบุแผนกที่รับสินค้า ในที่นี้มีผู้รับผิดเรื่องรับเข้าและเบิกจ่ายคือ 'Storeหลัก'"></i>
                        </label>
                        <select class="form-select form-select-lg rounded-pill @error('department_id') is-invalid @enderror" id="department_id" name="department_id">
                            <option value="">-- เลือกแผนก --</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label fw-bold">
                            หมายเหตุ
                            <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="ระบุหมายเหตุไว้ในทุกกรณีเช่น: 'ของครบตามจำนวนที่แสดงในใบส่งของ' เป็นต้น"></i>
                        </label>
                        <textarea class="form-control rounded-3 @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="หมายเหตุเพิ่มเติมเกี่ยวกับการรับเข้าสินค้า">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" id="submitButton" class="btn btn-primary btn-lg shadow-sm rounded-pill px-5 py-2 w-100">
                        <span id="buttonText"><i class="fas fa-save me-2"></i>บันทึกการรับเข้า</span>
                        <span id="loadingSpinner" class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true" style="display: none;"></span>
                    </button>
                    
                </form>
            </div>
        </div>
    </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const productIdSelect = document.getElementById('product_id');
                const batchSelectionTypeSelect = document.getElementById('batch_selection_type');
                const existingBatchFields = document.getElementById('existing_batch_fields');
                const newBatchFields = document.getElementById('new_batch_fields');
                const batchIdSelect = document.getElementById('batch_id');
                const unitDisplay = document.getElementById('unit_display');

                // Function to update unit display
                function updateUnitDisplay() {
                    const selectedProductOption = productIdSelect.options[productIdSelect.selectedIndex];
                    const unit = selectedProductOption.dataset.unit || 'หน่วย';
                    unitDisplay.textContent = unit;
                }

                // Function to toggle batch fields visibility
                function toggleBatchFields() {
                    const selectedType = batchSelectionTypeSelect.value;
                    if (selectedType === 'existing') {
                        existingBatchFields.style.display = 'block';
                        newBatchFields.style.display = 'none';
                        // Clear new batch fields if they were previously visible
                        document.getElementById('new_batch_number').value = '';
                        document.getElementById('production_date').value = '';
                        document.getElementById('expiration_date').value = '';
                    } else if (selectedType === 'new') {
                        existingBatchFields.style.display = 'none';
                        newBatchFields.style.display = 'block';
                        // Clear existing batch selection
                        batchIdSelect.innerHTML = '<option value="">-- เลือกล็อตสินค้า --</option>';
                    } else {
                        existingBatchFields.style.display = 'none';
                        newBatchFields.style.display = 'none';
                        batchIdSelect.innerHTML = '<option value="">-- เลือกล็อตสินค้า --</option>';
                        document.getElementById('new_batch_number').value = '';
                        document.getElementById('production_date').value = '';
                        document.getElementById('expiration_date').value = '';
                    }
                }

                // Function to load batches via AJAX
                function loadBatchesForProduct(productId) {
                    batchIdSelect.innerHTML = '<option value="">กำลังโหลด...</option>'; // Loading indicator
                    if (productId) {
                        fetch(`/api/products/${productId}/batches`)
                            .then(response => response.json())
                            .then(data => {
                                batchIdSelect.innerHTML = '<option value="">-- เลือกล็อตสินค้า --</option>';
                                data.forEach(batch => {
                                    const option = document.createElement('option');
                                    option.value = batch.id;
                                    option.textContent = `${batch.batch_number} (คงเหลือ: ${batch.quantity})`;
                                    // Retain old selected value after validation error
                                    if (oldBatchId === batch.id.toString()) {
                                        option.selected = true;
                                    }
                                    batchIdSelect.appendChild(option);
                                });
                            })
                            .catch(error => {
                                console.error('Error loading batches:', error);
                                batchIdSelect.innerHTML = '<option value="">ไม่สามารถโหลดล็อตสินค้าได้</option>';
                            });
                    } else {
                        batchIdSelect.innerHTML = '<option value="">-- เลือกล็อตสินค้า --</option>';
                    }
                }

                // Initial setup on page load
                updateUnitDisplay();
                toggleBatchFields();

                // Load batches if product_id and batch_selection_type are 'existing' on old() value
                const oldProductId = "{{ old('product_id') }}";
                const oldBatchSelectionType = "{{ old('batch_selection_type') }}";
                const oldBatchId = "{{ old('batch_id') }}";

                if (oldProductId && oldBatchSelectionType === 'existing') {
                    loadBatchesForProduct(oldProductId);
                }

                // Event Listeners
                productIdSelect.addEventListener('change', function() {
                    updateUnitDisplay();
                    // Reset batch selection type and fields when product changes
                    batchSelectionTypeSelect.value = '';
                    toggleBatchFields();
                });

                batchSelectionTypeSelect.addEventListener('change', function() {
                    toggleBatchFields();
                    const selectedType = this.value;
                    const productId = productIdSelect.value;
                    if (selectedType === 'existing' && productId) {
                        loadBatchesForProduct(productId);
                    }
                });
            });
        </script>
@endsection
