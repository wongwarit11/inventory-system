{{-- Dropdown เลือก Lot/Batch + แสดงจำนวนคงเหลือรวม --}}
<select name="products[{{ $index }}][batch_id]" 
        class="form-select batch-select" required>

    <option value="">-- เลือก Lot/Batch --</option>

    @foreach($batches as $batch)
        @php
            $qty = $batch->quantity ?? 0;
            $exp = $batch->expiry_date 
                ? \Carbon\Carbon::parse($batch->expiry_date)->format('d/m/Y') 
                : '-';
        @endphp

        <option value="{{ $batch->id }}" data-max-quantity="{{ $qty }}">
            Lot: {{ $batch->batch_number }}
            | Exp: {{ $exp }}
            | คงเหลือ: {{ number_format($qty) }}
        </option>
    @endforeach
</select>

<div class="small text-muted batch-stock-info mt-1">
    คงเหลือทั้งหมด: {{ number_format($totalStock) }} {{ $product->unit ?? '' }}
</div>
