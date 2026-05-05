{{-- 
    Partial View สำหรับแสดงรายการสินค้าในใบขอเบิก เมื่อเกิด Validation Error (old input)
    Variables: $index, $product (Model), $product_name, $product_unit, $requested_quantity, $notes, $selected_batch_id
--}}
<div class="row product-item-row mb-3 border p-3 rounded bg-white shadow-sm" data-product-id="{{ $product->id ?? 0 }}">
    <div class="col-12 mb-2 d-flex justify-content-between align-items-center border-bottom pb-2">
        <span class="fw-bold text-success"><i class="fas fa-box me-1"></i> {{ $product_name }} ({{ $product->product_code ?? 'N/A' }})</span>
        <button type="button" class="btn-close remove-product-item" aria-label="Close"></button>
    </div>

    {{-- Hidden input for product ID --}}
    <input type="hidden" name="products[{{ $index }}][product_id]" value="{{ $product->id ?? 0 }}">

    <div class="col-md-5 mb-3">
        <label for="batch_id_{{ $index }}" class="form-label fw-bold">Lot/Batch ที่ต้องการเบิก <span class="text-danger">*</span></label>
        <select class="form-select batch-select" id="batch_id_{{ $index }}" name="products[{{ $index }}][batch_id]" required>
            {{-- รายการ Lot จะถูกโหลดด้วย AJAX ใน JavaScript หลังจาก DOMContentLoaded --}}
            <option value="">-- โหลดข้อมูล Lot สินค้า... --</option>
            {{-- หากมี selected_batch_id ให้เพิ่ม Option ที่ถูกเลือกไว้ (กรณี Validation Fail) --}}
            @if ($selected_batch_id)
                @php
                    $selectedBatch = \App\Models\Batch::find($selected_batch_id);
                @endphp
                @if ($selectedBatch)
                    <option value="{{ $selectedBatch->id }}" selected 
                        data-max-quantity="{{ $selectedBatch->current_quantity }}">
                        Lot: {{ $selectedBatch->batch_number }} | Exp: {{ $selectedBatch->expiry_date }} | คงเหลือ: {{ $selectedBatch->current_quantity }}
                    </option>
                @endif
            @endif
        </select>
        <small class="text-muted batch-stock-info">รวมคงเหลือ: {{ $product->current_stock ?? 0 }} {{ $product_unit }}</small>
    </div>
    
    <div class="col-md-3 mb-3">
        <label for="requested_quantity_{{ $index }}" class="form-label fw-bold">จำนวนที่ต้องการ <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="number" class="form-control quantity-input @error("products.{$index}.requested_quantity") is-invalid @enderror" 
                   id="requested_quantity_{{ $index }}" 
                   name="products[{{ $index }}][requested_quantity]" 
                   value="{{ old("products.{$index}.requested_quantity", $requested_quantity) }}" min="1" required 
                   data-max-stock="{{ $selectedBatch->current_quantity ?? $product->current_stock ?? 0 }}">
            <span class="input-group-text unit-display">{{ $product_unit }}</span>
        </div>
        @error("products.{$index}.requested_quantity")
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <div class="text-danger small mt-1 quantity-error-message"></div>
        @enderror
    </div>
    
    <div class="col-md-4 mb-3">
        <label for="item_notes_{{ $index }}" class="form-label">หมายเหตุ (รายการ)</label>
        <input type="text" class="form-control" id="item_notes_{{ $index }}" name="products[{{ $index }}][notes]" 
               placeholder="หมายเหตุสำหรับรายการนี้" value="{{ old("products.{$index}.notes", $notes) }}">
    </div>
</div>
