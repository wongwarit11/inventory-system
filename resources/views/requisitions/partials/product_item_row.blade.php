{{-- resources/views/requisitions/partials/product_item_row.blade.php --}}
{{-- ใช้สำหรับหน้าสร้างใบขอเบิก (create.blade.php) --}}

<div class="row product-item-row mb-3 border p-3 rounded">
    <div class="col-md-5 mb-3">
        <label for="product_id_{{ $index }}" class="form-label">สินค้า <span class="text-danger">*</span></label>
        <select class="form-select product-select @error('products.' . $index . '.product_id') is-invalid @enderror" id="product_id_{{ $index }}" name="products[{{ $index }}][product_id]" required>
            <option value="">-- เลือกสินค้า --</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" data-unit="{{ $product->unit }}" {{ (isset($oldProduct['product_id']) && $oldProduct['product_id'] == $product->id) ? 'selected' : '' }}>
                    {{ $product->name }} ({{ $product->product_code }})
                </option>
            @endforeach
        </select>
        @error('products.' . $index . '.product_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3 mb-3">
        <label for="requested_quantity_{{ $index }}" class="form-label">จำนวนที่ต้องการ <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="number" class="form-control quantity-input @error('products.' . $index . '.requested_quantity') is-invalid @enderror" id="requested_quantity_{{ $index }}" name="products[{{ $index }}][requested_quantity]" value="{{ isset($oldProduct['requested_quantity']) ? $oldProduct['requested_quantity'] : 1 }}" min="1" required>
            <span class="input-group-text unit-display">
                @php
                    $selectedUnit = 'หน่วย';
                    if (isset($oldProduct['product_id'])) {
                        $selectedProduct = $products->firstWhere('id', $oldProduct['product_id']);
                        if ($selectedProduct) {
                            $selectedUnit = $selectedProduct->unit;
                        }
                    }
                @endphp
                {{ $selectedUnit }}
            </span>
        </div>
        @error('products.' . $index . '.requested_quantity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-3 mb-3">
        <label for="item_notes_{{ $index }}" class="form-label">หมายเหตุ (รายการ)</label>
        <input type="text" class="form-control @error('products.' . $index . '.notes') is-invalid @enderror" id="item_notes_{{ $index }}" name="products[{{ $index }}][notes]" value="{{ isset($oldProduct['notes']) ? $oldProduct['notes'] : '' }}" placeholder="หมายเหตุสำหรับรายการนี้">
        @error('products.' . $index . '.notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-1 mb-3 d-flex align-items-end">
        <button type="button" class="btn btn-danger remove-product-item w-100"><i class="fas fa-trash"></i></button>
    </div>
</div>
