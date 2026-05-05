<div class="table-responsive">
    <table class="table table-hover table-striped align-middle">
        <thead class="table-primary">
            <tr>
                <th scope="col">#</th>
                <th scope="col">วันที่ทำรายการ</th>
                <th scope="col">ประเภท</th>
                <th scope="col">สินค้า</th>
                <th scope="col">ล็อตสินค้า</th>
                <th scope="col">จำนวน</th>
                <th scope="col">แผนก</th>
                <th scope="col">ผู้ทำรายการ</th>
                <th scope="col">เอกสารอ้างอิง</th>
                <th scope="col">หมายเหตุ</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td>{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y H:i') }}</td>
                    <td>
                        @php
                            $typeClass = '';
                            $typeText = '';
                            switch($transaction->transaction_type) {
                                case 'in': $typeClass = 'bg-success'; $typeText = 'รับเข้า'; break;
                                case 'out': $typeClass = 'bg-danger'; $typeText = 'จ่ายออก'; break;
                                case 'adjustment_in': $typeClass = 'bg-info'; $typeText = 'ปรับเพิ่ม'; break;
                                case 'adjustment_out': $typeClass = 'bg-warning text-dark'; $typeText = 'ปรับลด'; break;
                                default: $typeClass = 'bg-secondary'; $typeText = 'ไม่ระบุ'; break;
                            }
                        @endphp
                        <span class="badge rounded-pill px-3 py-2 {{ $typeClass }}">{{ $typeText }}</span>
                    </td>
                    <td>{{ $transaction->product->name ?? '-' }} ({{ $transaction->product->product_code ?? '-' }})</td>
                    <td>{{ $transaction->batch->batch_number ?? '-' }}</td>
                    <td>
                        @if (in_array($transaction->transaction_type, ['out', 'adjustment_out']))
                            <span class="text-danger">-{{ number_format(abs($transaction->quantity)) }}</span>
                        @else
                            <span class="text-success">+{{ number_format($transaction->quantity) }}</span>
                        @endif
                        {{ $transaction->product->unit ?? '' }}
                    </td>
                    <td>{{ $transaction->department->name ?? '-' }}</td>
                    <td>{{ $transaction->user->fullname ?? $transaction->user->username ?? '-' }}</td>
                    <td>{{ $transaction->reference_doc ?? '-' }}</td>
                    <td>{{ $transaction->notes ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-4">ไม่พบข้อมูลรายการสต็อกที่ตรงกับเงื่อนไข</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination Links --}}
<div class="d-flex justify-content-center mt-3">
    {{ $transactions->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
</div>
