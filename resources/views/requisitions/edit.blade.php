@extends('layouts.app')

@section('title', 'แก้ไขใบขอเบิก #' . $requisition->requisition_number)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-edit me-2 text-primary"></i> แก้ไขใบขอเบิก: <span class="text-secondary">REQ-{{ $requisition->requisition_number }}</span></h1>
        <a href="{{ route('requisitions.index') }}" class="btn btn-secondary shadow-sm rounded-pill px-4 py-2">
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
            <form action="{{ route('requisitions.update', $requisition->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="department_id" class="form-label fw-bold">แผนกที่ขอเบิก <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg rounded-pill @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
                                <option value="">-- เลือกแผนก --</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id', $requisition->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="requisition_date" class="form-label fw-bold">วันที่ขอเบิก <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-lg rounded-pill @error('requisition_date') is-invalid @enderror" id="requisition_date" name="requisition_date" value="{{ old('requisition_date', \Carbon\Carbon::parse($requisition->requisition_date)->format('Y-m-d')) }}" required>
                            @error('requisition_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="notes" class="form-label fw-bold">หมายเหตุ</label>
                    <textarea class="form-control rounded-3 @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="หมายเหตุเพิ่มเติมเกี่ยวกับใบขอเบิก">{{ old('notes', $requisition->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">

                <h4 class="mb-3"><i class="fas fa-box-open me-2"></i> รายการสินค้าที่ต้องการเบิก</h4>
                <div id="product_items_container">
                    @php $itemIndex = 0; @endphp
                    @if (old('products'))
                        {{-- กรณีมี old input จาก validation error --}}
                        @foreach (old('products') as $index => $oldProduct)
                            @include('requisitions.partials.product_item_edit_row', ['index' => $index, 'products' => $products, 'oldProduct' => $oldProduct])
                            @php $itemIndex = $index + 1; @endphp
                        @endforeach
                    @else
                        {{-- กรณีโหลดข้อมูลจากฐานข้อมูล --}}
                        @forelse ($requisition->items as $index => $item)
                            @include('requisitions.partials.product_item_edit_row', ['index' => $index, 'products' => $products, 'item' => $item])
                            @php $itemIndex = $index + 1; @endphp
                        @empty
                            {{-- หากไม่มีรายการสินค้าในใบขอเบิกนี้เลย (ไม่น่าจะเกิดขึ้น แต่กันไว้) --}}
                            @include('requisitions.partials.product_item_edit_row', ['index' => 0, 'products' => $products])
                            @php $itemIndex = 1; @endphp
                        @endforelse
                    @endif
                </div>

                <button type="button" id="add_product_item" class="btn btn-outline-primary rounded-pill px-4 py-2 mb-4">
                    <i class="fas fa-plus me-2"></i> เพิ่มรายการสินค้า
                </button>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success btn-lg shadow-sm rounded-pill px-5 py-2">
                        <i class="fas fa-save me-2"></i> บันทึกการแก้ไข
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let itemIndex = {{ $itemIndex }}; // เริ่มต้น index จากจำนวนรายการที่มีอยู่แล้ว
            const productItemsContainer = document.getElementById('product_items_container');
            const addProductItemButton = document.getElementById('add_product_item');

            addProductItemButton.addEventListener('click', function() {
                const newRow = document.createElement('div');
                newRow.classList.add('row', 'product-item-row', 'mb-3', 'border', 'p-3', 'rounded');
                newRow.innerHTML = `
                    <input type="hidden" name="products[${itemIndex}][item_id]" value="">
                    <div class="col-md-5 mb-3">
                        <label for="product_id_${itemIndex}" class="form-label">สินค้า <span class="text-danger">*</span></label>
                        <select class="form-select product-select" id="product_id_${itemIndex}" name="products[${itemIndex}][product_id]" required>
                            <option value="">-- เลือกสินค้า --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-unit="{{ $product->unit }}">{{ $product->name }} ({{ $product->product_code }})</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="requested_quantity_${itemIndex}" class="form-label">จำนวนที่ต้องการ <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control quantity-input" id="requested_quantity_${itemIndex}" name="products[${itemIndex}][requested_quantity]" value="1" min="1" required>
                            <span class="input-group-text unit-display">หน่วย</span>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="item_notes_${itemIndex}" class="form-label">หมายเหตุ (รายการ)</label>
                        <input type="text" class="form-control" id="item_notes_${itemIndex}" name="products[${itemIndex}][notes]" placeholder="หมายเหตุสำหรับรายการนี้">
                    </div>
                    <div class="col-md-1 mb-3 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-product-item w-100"><i class="fas fa-trash"></i></button>
                    </div>
                `;
                productItemsContainer.appendChild(newRow);
                updateUnitDisplayForNewRow(newRow);
                addRemoveButtonListener(newRow);
                addSelectChangeListener(newRow);
                itemIndex++;
            });

            function updateUnitDisplayForNewRow(row) {
                const productSelect = row.querySelector('.product-select');
                const unitDisplay = row.querySelector('.unit-display');

                if (productSelect && unitDisplay) {
                    productSelect.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        unitDisplay.textContent = selectedOption.dataset.unit || 'หน่วย';
                    });
                    // Set initial unit if a product is already selected (e.g., from old input or existing data)
                    const selectedOption = productSelect.options[productSelect.selectedIndex];
                    unitDisplay.textContent = selectedOption.dataset.unit || 'หน่วย';
                }
            }

            function addRemoveButtonListener(row) {
                const removeButton = row.querySelector('.remove-product-item');
                if (removeButton) {
                    removeButton.addEventListener('click', function() {
                        row.remove();
                    });
                }
            }

            function addSelectChangeListener(row) {
                const productSelect = row.querySelector('.product-select');
                if (productSelect) {
                    productSelect.addEventListener('change', function() {
                        this.classList.remove('is-invalid');
                        this.nextElementSibling.textContent = '';
                    });
                }
                const quantityInput = row.querySelector('.quantity-input');
                if (quantityInput) {
                    quantityInput.addEventListener('input', function() {
                        this.classList.remove('is-invalid');
                        this.closest('.input-group').nextElementSibling.textContent = '';
                    });
                }
            }

            // Initial setup for existing rows (e.g., from old input after validation error or loaded from DB)
            document.querySelectorAll('.product-item-row').forEach(row => {
                updateUnitDisplayForNewRow(row);
                addRemoveButtonListener(row);
                addSelectChangeListener(row);
            });
        });
    </script>
@endsection
