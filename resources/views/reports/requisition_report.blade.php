@extends('layouts.app')

@section('title', 'รายงานการเบิกสินค้า')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-clipboard-list me-2"></i> รายงานการเบิกสินค้า</h1>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            ตัวกรองรายงาน
        </div>
        <div class="card-body">
            <form action="{{ route('reports.requisition') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">จากวันที่</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">ถึงวันที่</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="status_filter" class="form-label">สถานะ</label>
                        <select class="form-select" id="status_filter" name="status_filter">
                            <option value="all" {{ request('status_filter') == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                            <option value="pending" {{ request('status_filter') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status_filter') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="issued" {{ request('status_filter') == 'issued' ? 'selected' : '' }}>Issued</option>
                            <option value="cancelled" {{ request('status_filter') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="department_id" class="form-label">แผนก</label>
                        <select class="form-select" id="department_id" name="department_id">
                            <option value="all" {{ request('department_id') == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i> กรองข้อมูล</button>
                        <a href="{{ route('reports.requisition') }}" class="btn btn-secondary"><i class="fas fa-sync-alt me-2"></i> ล้างตัวกรอง</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>เลขที่ใบขอเบิก</th>
                            <th>วันที่ขอเบิก</th>
                            <th>แผนก</th>
                            <th>ผู้ขอเบิก</th>
                            <th>สถานะ</th>
                            <th>รายการสินค้า</th>
                            <th>หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requisitions as $requisition)
                            <tr>
                                <td>{{ $loop->iteration + ($requisitions->currentPage() - 1) * $requisitions->perPage() }}</td>
                                <td>REQ-{{ $requisition->requisition_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($requisition->requisition_date)->format('d/m/Y') }}</td>
                                <td>{{ $requisition->department->name ?? '-' }}</td>
                                <td>{{ $requisition->user->fullname ?? $requisition->user->username ?? '-' }}</td>
                                <td>
                                    @php
                                        $statusClass = '';
                                        switch($requisition->status) {
                                            case 'pending': $statusClass = 'bg-warning text-dark'; break;
                                            case 'approved': $statusClass = 'bg-primary'; break;
                                            case 'issued': $statusClass = 'bg-success'; break;
                                            case 'cancelled': $statusClass = 'bg-danger'; break;
                                            default: $statusClass = 'bg-secondary'; break;
                                        }
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($requisition->status) }}</span>
                                </td>
                                <td>
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($requisition->items as $item)
                                            <li>
                                                {{ $item->product->name ?? '-' }} ({{ $item->product->product_code ?? '-' }})
                                                <br>
                                                <small>ขอ: {{ $item->requested_quantity }} เบิก: {{ $item->issued_quantity }} {{ $item->product->unit ?? '' }}</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>{{ $requisition->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">ไม่พบข้อมูลรายงานการเบิกสินค้า</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center">
                {{ $requisitions->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
