@if($products->count() > 0)
    @foreach($products as $key => $product)
        <tr>
            <td>{{ ($products->currentPage() - 1) * $products->perPage() + $key + 1 }}</td>
            <td>{{ $product->code ?? '-' }}</td>
            <td>{{ $product->name ?? '-' }}</td>

            <td>
                {{ $product->category->name ?? 'ไม่ระบุหมวดหมู่' }}
            </td>

            <td>
                {{ $product->supplier->name ?? 'ไม่ระบุซัพพลายเออร์' }}
            </td>

            <td class="text-danger fw-bold">
                {{ number_format($product->stock_sum ?? 0) }}
            </td>

            <td>
                {{ number_format($product->minimum_stock_level ?? 0) }}
            </td>

            <td>
                @if(($product->stock_sum ?? 0) <= ($product->minimum_stock_level ?? 0))
                    <span class="badge bg-danger">ต่ำกว่ากำหนด</span>
                @else
                    <span class="badge bg-success">ปกติ</span>
                @endif
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="8" class="text-center text-muted">
            ไม่พบข้อมูล
        </td>
    </tr>
@endif
