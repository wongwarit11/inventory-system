<div class="product-row row align-items-center mb-3" 
     data-product-id="{{ $product->id ?? '' }}"
     data-index="{{ $index }}">

    {{-- product_id --}}
    <input type="hidden" 
           name="products[{{ $index }}][product_id]" 
           value="{{ $product->id ?? '' }}" 
           class="product-id">

    {{-- ช่องค้นหาสินค้า --}}
    <div class="col-md-5">
        <input type="text"
               class="form-control product-search"
               placeholder="พิมพ์ชื่อสินค้า...">
        <div class="list-group search-results"></div>
    </div>

    {{-- Batch dropdown (โหลดใหม่ผ่าน AJAX) --}}
    <div class="col-md-3 batch-container">
        @include('requisitions.partials.batch_dropdown', [
            'index' => $index,
            'product' => $product,
            'batches' => $batches,
            'totalStock' => $totalStock
        ])
    </div>

    {{-- จำนวนที่ต้องการเบิก --}}
    <div class="col-md-3">
        <div class="input-group">
            <input type="number" 
                   name="products[{{ $index }}][quantity]" 
                   class="form-control qty-input" 
                   min="1" step="1" value="1" required>

            <span class="input-group-text unit-display">
                {{ $product->unit ?? '' }}
            </span>
        </div>

        <div class="text-danger small quantity-error-message"></div>
    </div>

    {{-- ปุ่มลบแถว --}}
    <div class="col-md-1 text-center">
        <button type="button" class="btn btn-outline-danger btn-remove rounded-pill">
            <i class="fas fa-trash-alt"></i>
        </button>
    </div>
</div>
