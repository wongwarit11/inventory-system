@extends('layouts.app')

@section('title', 'จัดการล็อตสินค้า')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold"><i class="fas fa-boxes me-2 text-primary"></i> {{ config('app.name') }}:
            <span class="text-secondary">จัดการล็อตสินค้า</span>
        </h1>
        <a href="{{ route('batches.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 py-2">
            <i class="fas fa-plus-circle me-2"></i> เพิ่มล็อตสินค้าใหม่
        </a>
    </div>

    {{-- โค้ดที่เพิ่มเข้าไปใหม่สำหรับช่องค้นหา --}}
    <form action="{{ route('batches.index') }}" method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="ค้นหาด้วย ชื่อสินค้า, ผู้จำหน่าย หรือรหัสล็อต..." value="{{ request('search') }}">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">ค้นหา</button>
            </div>
        </div>
    </form>

    <div class="card shadow-lg rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">สินค้า</th>
                            <th scope="col">ผู้จำหน่าย</th>
                            <th scope="col">รหัสล็อต</th>
                            <th scope="col">จำนวน</th>
                            <th scope="col">วันที่ผลิต</th>
                            <th scope="col">วันหมดอายุ</th>
                            <th scope="col">หมายเหตุ</th>
                            <th scope="col" class="text-center" width="180px">การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($batches as $batch)
                            <tr>
                                <td>{{ $loop->iteration + ($batches->currentPage() - 1) * $batches->perPage() }}</td>
                                <td>{{ $batch->product->name ?? '-' }} ({{ $batch->product->product_code ?? '-' }})</td>
                                <td>{{ $batch->supplier->name ?? '-' }}</td>
                                <td>{{ $batch->batch_number }}</td>
                                <td>{{ number_format($batch->quantity) }} {{ $batch->product->unit ?? '' }}</td>
                                <td>{{ $batch->manufacture_date ? \Carbon\Carbon::parse($batch->manufacture_date)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if ($batch->expiration_date)
                                        @php
                                            $expirationDate = \Carbon\Carbon::parse($batch->expiration_date);
                                            $today = \Carbon\Carbon::now();
                                            $daysUntilExpiration = $today->startOfDay()->diffInDays($expirationDate->startOfDay());
                                            $isExpired = $expirationDate->isPast() && !$expirationDate->isToday();
                                            $isExpiringSoon = $expirationDate->isFuture() && $daysUntilExpiration <= 30;
                                        @endphp

                                        {{ $expirationDate->format('d/m/Y') }}
                                        @if ($isExpired)
                                            <span class="text-danger fw-bold">(หมดอายุแล้ว)</span>
                                        @elseif ($expirationDate->isToday())
                                            <span class="text-danger fw-bold">(หมดอายุวันนี้)</span>
                                        @elseif ($isExpiringSoon)
                                            <span class="text-warning fw-bold">(เหลือ {{ $daysUntilExpiration }} วัน)</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $batch->notes ?? '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('batches.edit', $batch->id) }}" class="btn btn-warning btn-sm me-1 rounded-pill" title="แก้ไข">
                                        <i class="fas fa-edit"></i> แก้ไข
                                    </a>
                                    <form action="{{ route('batches.destroy', $batch->id) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบล็อตสินค้านี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded-pill" title="ลบ">
                                            <i class="fas fa-trash-alt"></i> ลบ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">ไม่พบข้อมูลล็อตสินค้า</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $batches->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection