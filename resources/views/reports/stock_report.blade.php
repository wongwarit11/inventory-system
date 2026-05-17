@extends('layouts.app')

@section('title', 'รายงานสต็อกปัจจุบัน')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-chart-pie me-2"></i> รายงานสต็อกปัจจุบัน</h1>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>รหัสสินค้า</th>
                            <th>ชื่อสินค้า</th>
                            <th>หมวดหมู่</th>
                            <th>ประเภทสินค้า</th>
                            <th>ผู้ผลิต</th>
                            <th>ล็อตสินค้า</th>
                            <th>ตำแหน่ง</th>
                            <th>วันหมดอายุ</th>
                            <th>ราคาต้นทุน (ต่อหน่วย)</th>
                            <th>มูลค่ารวม</th>
                            <th>จุดต่ำสุดที่ต้องสั่งซื้อ</th>
                            <th>จำนวนคงเหลือ</th>
                            <th>หน่วยนับ</th>
                            <th>สถานะสินค้า</th>
                            <th>สถานะสต็อก</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($batches as $batch)
                            <tr>
                                <td>{{ $loop->iteration + ($batches->currentPage() - 1) * $batches->perPage() }}</td>
                                <td>{{ $batch->product->product_code ?? '-' }}</td>
                                <td>{{ $batch->product->name ?? '-' }}</td>
                                <td>{{ $batch->product->category->name ?? '-' }}</td>
                                <td>{{ $batch->product->productType->name ?? '-' }}</td>
                                <td>{{ $batch->product->manufacturer->name ?? '-' }}</td>
                                <td>{{ $batch->batch_number ?? '-' }}</td>
                                <td>
                                    @if ($batch->location)
                                        {{ $batch->location->full_location }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($batch->expiration_date)
                                        @php
                                            $expirationDate = \Carbon\Carbon::parse($batch->expiration_date);
                                            $today = \Carbon\Carbon::now();

                                            // คำนวณจำนวนวันที่เหลือโดยใช้ startOfDay() เพื่อให้ได้จำนวนเต็ม
                                            // และใช้ diffInDays() เพื่อหาผลต่างของวัน
                                            $daysUntilExpiration = $today->startOfDay()->diffInDays($expirationDate->startOfDay());

                                            // ตรวจสอบสถานะของวันหมดอายุเทียบกับวันนี้
                                            $isExpired = $expirationDate->isPast() && !$expirationDate->isToday();
                                            $isExpiringSoon = $expirationDate->isFuture() && $daysUntilExpiration <= 30; // 30 วันหรือน้อยกว่า
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
                                <td>{{ number_format($batch->product->cost_price, 2) ?? '-' }}</td>
                                <td>{{ number_format($batch->quantity * ($batch->product->cost_price ?? 0), 2) }}</td>
                                <td>
                                    @if ($batch->quantity <= $batch->product->minimum_stock_level)
                                        <span class="text-danger fw-bold">
                                            {{ number_format($batch->product->minimum_stock_level) ?? '-' }} (ดำเนินการซื้อด่วน)
                                        </span>
                                    @else
                                        {{ number_format($batch->product->minimum_stock_level) ?? '-' }}
                                    @endif
                                </td>
                                <td>{{ $batch->quantity }}</td>
                                <td>{{ $batch->product->unit ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $batch->product->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $batch->product->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $currentQty = $batch->quantity;
                                        $minLevel = $batch->product->minimum_stock_level ?? 0;
                                    @endphp
                                    @if($currentQty <= 0)
                                        <span class="badge bg-danger">หมดแล้ว</span>
                                    @elseif($currentQty <= $minLevel)
                                        <span class="badge bg-warning text-dark">ใกล้หมด</span>
                                    @else
                                        <span class="badge bg-success">ปกติ</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="16" class="text-center">ไม่พบข้อมูลสต็อก</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center">
                {{ $batches->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
