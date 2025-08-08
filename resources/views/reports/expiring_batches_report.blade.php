@extends('layouts.app')

@section('title', 'รายงานล็อตสินค้าใกล้หมดอายุ')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-calendar-times me-2"></i> รายงานล็อตสินค้าใกล้หมดอายุ (ภายใน {{ $expirationThresholdDays }} วัน)</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary"><i class="fas fa-arrow-alt-circle-left me-2"></i> กลับ Dashboard</a>
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
                            <th>ล็อตสินค้า</th>
                            <th>สต็อกปัจจุบัน</th>
                            <th>วันที่หมดอายุ</th>
                            <th>สถานะสินค้า</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expiringBatches as $batch)
                            <tr>
                                <td>{{ $loop->iteration + ($expiringBatches->currentPage() - 1) * $expiringBatches->perPage() }}</td>
                                <td>{{ $batch->product->product_code ?? '-' }}</td>
                                <td>{{ $batch->product->name ?? '-' }}</td>
                                <td>{{ $batch->batch_number }}</td>
                                <td>{{ number_format($batch->quantity) }} {{ $batch->product->unit ?? '' }}</td>
                                <td>{{ \Carbon\Carbon::parse($batch->expiration_date)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge {{ $batch->product->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $batch->product->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">ไม่พบล็อตสินค้าที่ใกล้หมดอายุ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center">
                {{ $expiringBatches->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
