@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    {{-- ส่วนหัวของ Dashboard พร้อมโลโก้และข้อความ --}}
    <div class="d-flex justify-content-center align-items-center mb-4">
        <h1 class="text-primary fw-bold mb-0">
            <img src="{{ asset('images/hpk_logo.png') }}" alt="Your logo" style="height: 45px; margin-right: 10px; vertical-align: middle;">
            ระบบจัดการคลังสินค้า <span class="text-secondary">โรงพยาบาลห้วยปลากั้งเพื่อสังคม</span>
        </h1>
    </div>

    {{-- ส่วนสำหรับแสดงข้อความแจ้งเตือน (Success/Error)
         ส่วนนี้ถูกจัดการโดย layouts/app.blade.php แล้ว ไม่ต้องใส่ซ้ำที่นี่
    --}}

    <div class="row g-4">
        {{-- Total Products Card --}}
        <div class="col-md-4">
            <div class="card bg-white text-dark shadow-sm rounded-4 p-3 h-100 d-flex flex-column justify-content-between">
                <div class="card-body">
                    <h5 class="card-title text-primary"><i class="fas fa-box me-2"></i> จำนวนสินค้าทั้งหมด</h5>
                    <p class="card-text fs-3 fw-bold">{{ number_format($totalProducts) }} รายการ</p>
                </div>
            </div>
        </div>

        {{-- Total Batches Card --}}
        <div class="col-md-4">
            <div class="card bg-white text-dark shadow-sm rounded-4 p-3 h-100 d-flex flex-column justify-content-between">
                <div class="card-body">
                    <h5 class="card-title text-info"><i class="fas fa-boxes me-2"></i> จำนวนล็อตสินค้าทั้งหมด</h5>
                    <p class="card-text fs-3 fw-bold">{{ number_format($totalBatches) }} ล็อต</p>
                </div>
            </div>
        </div>

        {{-- Total Stock Quantity Card --}}
        <div class="col-md-4">
            <div class="card bg-white text-dark shadow-sm rounded-4 p-3 h-100 d-flex flex-column justify-content-between">
                <div class="card-body">
                    <h5 class="card-title text-success"><i class="fas fa-warehouse me-2"></i> จำนวนสต็อกทั้งหมด</h5>
                    <p class="card-text fs-3 fw-bold">{{ number_format($totalStockQuantity) }} หน่วย</p>
                </div>
            </div>
        </div>

        {{-- Low Stock Products Card --}}
        <div class="col-md-4">
            <div class="card bg-white text-dark shadow-sm rounded-4 p-3 h-100 d-flex flex-column justify-content-between">
                <div class="card-body">
                    <h5 class="card-title text-warning"><i class="fas fa-exclamation-triangle me-2"></i> สินค้าที่สต็อกต่ำกว่าจุดต่ำสุด</h5>
                    <p class="card-text fs-3 fw-bold">{{ number_format($lowStockProductsCount) }} รายการ</p>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0">
                    <a href="{{ route('reports.low_stock_products') }}" class="btn btn-sm btn-outline-warning rounded-pill">
                        <i class="fas fa-arrow-circle-right me-1"></i> ดูรายละเอียด
                    </a>
                </div>
            </div>
        </div>

        {{-- Expiring Batches Card --}}
        <div class="col-md-4">
            <div class="card bg-white text-dark shadow-sm rounded-4 p-3 h-100 d-flex flex-column justify-content-between">
                <div class="card-body">
                    <h5 class="card-title text-danger"><i class="fas fa-calendar-times me-2"></i> ล็อตสินค้าใกล้หมดอายุ</h5>
                    <p class="card-text fs-3 fw-bold">{{ number_format($expiringBatchesCount) }} ล็อต</p>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0">
                    <a href="{{ route('reports.expiring_batches') }}" class="btn btn-sm btn-outline-danger rounded-pill">
                        <i class="fas fa-arrow-circle-right me-1"></i> ดูรายละเอียด
                    </a>
                </div>
            </div>
        </div>

        {{-- Pending Requisitions Card --}}
        <div class="col-md-4">
            <div class="card bg-white text-dark shadow-sm rounded-4 p-3 h-100 d-flex flex-column justify-content-between">
                <div class="card-body">
                    <h5 class="card-title text-secondary"><i class="fas fa-hourglass-half me-2"></i> ใบขอเบิกที่รอดำเนินการ</h5>
                    <p class="card-text fs-3 fw-bold">{{ number_format($pendingRequisitionsCount) }} รายการ</p>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0">
                    <a href="{{ route('requisitions.index') }}?status_filter=pending" class="btn btn-sm btn-outline-secondary rounded-pill">
                        <i class="fas fa-arrow-circle-right me-1"></i> ดูรายละเอียด
                    </a>
                </div>
            </div>
        </div>

        {{-- Other Statistics Cards --}}
        <div class="col-md-4">
            <div class="card bg-white text-dark shadow-sm rounded-4 p-3 h-100 d-flex flex-column justify-content-between">
                <div class="card-body">
                    <h5 class="card-title text-primary"><i class="fas fa-building me-2"></i> จำนวนแผนกทั้งหมด</h5>
                    <p class="card-text fs-3 fw-bold">{{ number_format($totalDepartments) }} แผนก</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-white text-dark shadow-sm rounded-4 p-3 h-100 d-flex flex-column justify-content-between">
                <div class="card-body">
                    <h5 class="card-title text-info"><i class="fas fa-truck me-2"></i> จำนวนผู้จัดจำหน่ายทั้งหมด</h5>
                    <p class="card-text fs-3 fw-bold">{{ number_format($totalSuppliers) }} ราย</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-white text-dark shadow-sm rounded-4 p-3 h-100 d-flex flex-column justify-content-between">
                <div class="card-body">
                    <h5 class="card-title text-success"><i class="fas fa-industry me-2"></i> จำนวนผู้ผลิตทั้งหมด</h5>
                    <p class="card-text fs-3 fw-bold">{{ number_format($totalManufacturers) }} ราย</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-white text-dark shadow-sm rounded-4 p-3 h-100 d-flex flex-column justify-content-between">
                <div class="card-body">
                    <h5 class="card-title text-dark"><i class="fas fa-users-cog me-2"></i> จำนวนผู้ใช้งานทั้งหมด</h5>
                    <p class="card-text fs-3 fw-bold">{{ number_format($totalUsers) }} คน</p>
                </div>
            </div>
        </div>
    </div>
@endsection
