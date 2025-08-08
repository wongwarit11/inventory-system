@extends('layouts.app')

@section('title', 'รายงานสินค้าสต็อกต่ำกว่าจุดต่ำสุด')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-exclamation-triangle me-2"></i> รายงานสินค้าสต็อกต่ำกว่าจุดต่ำสุด</h1>
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
                            <th>หมวดหมู่</th>
                            <th>ผู้ผลิต</th>
                            <th>ประเภทสินค้า</th>
                            <th>หน่วยนับ</th>
                            <th>สต็อกปัจจุบัน</th>
                            <th>จุดต่ำสุด</th>
                            <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lowStockProducts as $product)
                            <tr>
                                <td>{{ $loop->iteration + ($lowStockProducts->currentPage() - 1) * $lowStockProducts->perPage() }}</td>
                                <td>{{ $product->product_code }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name ?? '-' }}</td>
                                <td>{{ $product->manufacturer->name ?? '-' }}</td>
                                <td>{{ $product->productType->name ?? '-' }}</td>
                                <td>{{ $product->unit }}</td>
                                <td>
                                    {{ number_format($product->batches->sum('quantity')) }}
                                </td>
                                <td>{{ $product->minimum_stock_level }}</td>
                                <td>
                                    <span class="badge {{ $product->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $product->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">ไม่พบสินค้าที่สต็อกต่ำกว่าจุดต่ำสุด</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center">
                {{ $lowStockProducts->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
