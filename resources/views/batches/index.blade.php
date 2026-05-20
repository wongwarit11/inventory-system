@extends('layouts.app')

@section('title', 'จัดการล็อตสินค้า')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-boxes me-2 text-primary"></i> จัดการล็อตสินค้า</h1>
        <a href="{{ route('batches.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 py-2">
            <i class="fas fa-plus-circle me-2"></i> เพิ่มล็อตสินค้าใหม่
        </a>
    </div>
    <div class="card shadow-lg rounded-4">
        <div class="card-body p-4">
            {{-- Search Form --}}
            <form method="GET" class="mb-3 d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาล็อต (เลขล็อต, สินค้า)" value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search me-1"></i>ค้นหา</button>
                @if(request('search'))
                    <a href="{{ route('batches.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times me-1"></i>ล้าง</a>
                @endif
            </form>
            
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">สินค้า</th>
                            <th scope="col">รหัสล็อต</th>
                            <th scope="col">ตำแหน่ง</th>
                            <th scope="col">จำนวน</th>
                            <th scope="col">วันที่ผลิต</th>
                            <th scope="col">วันหมดอายุ</th>
                            <th scope="col">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($batches as $batch)
                            <tr style="cursor:pointer;" onclick="window.location='{{ route('batches.show', $batch->id) }}'">
                                <td>{{ $loop->iteration + ($batches->currentPage() - 1) * $batches->perPage() }}</td>
                                <td>{{ $batch->product->name ?? '-' }} ({{ $batch->product->product_code ?? '-' }})</td>
                                <td>{{ $batch->batch_number }}</td>
                                <td>
                                    @if ($batch->location)
                                        <span class="badge bg-info">{{ $batch->location->full_location }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
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
                                <td>
                                    <span class="badge rounded-pill px-3 py-2 {{ $batch->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $batch->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                    </span>
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
