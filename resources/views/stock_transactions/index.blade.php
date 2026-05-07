@extends('layouts.app')

@section('title', 'รายการสต็อก')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-history me-2 text-primary"></i> รายการสต็อก</h1>
        {{-- ปุ่มสำหรับสร้างรายการใหม่ (ถ้ามี) --}}
        {{-- <a href="{{ route('stock_transactions.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 py-2">
            <i class="fas fa-plus-circle me-2"></i> สร้างรายการสต็อกใหม่
        </a> --}}
    </div>
    <div class="card shadow-lg rounded-4">
        <div class="card-body p-4">
            {{-- Search Form --}}
            <form method="GET" class="mb-3 d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="ค้นหารายการ (สินค้า, เอกสาร)" value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search me-1"></i>ค้นหา</button>
                @if(request('search'))
                    <a href="{{ route('stock_transactions.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i>ล้าง</a>
                @endif
            </form>
            
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
                            <th scope="col">หมวดหมู่</th>
                            <th scope="col">แผนก</th>
                            <th scope="col">ผู้ทำรายการ</th>
                            <th scope="col">เอกสารอ้างอิง</th>
                            <th scope="col">หมายเหตุ</th>
                            {{-- <th scope="col" class="text-center" width="100px">การดำเนินการ</th> --}}
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
                                <td>{{ $transaction->batch->product->category->name ?? '-' }}</td>
                                <td>{{ $transaction->department->name ?? '-' }}</td>
                                <td>{{ $transaction->user->fullname ?? $transaction->user->username ?? '-' }}</td>
                                <td>{{ $transaction->notes ?? '-' }}</td>
                                {{--
                                <td class="text-center">
                                    <a href="#" class="btn btn-info btn-sm me-1 rounded-pill" title="ดูรายละเอียด">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                                --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">ไม่พบข้อมูลรายการสต็อก</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $transactions->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
