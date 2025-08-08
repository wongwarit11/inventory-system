@extends('layouts.app')

@section('title', 'รายงานใบขอเบิกที่รออนุมัติ')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-hourglass-half me-2"></i> รายงานใบขอเบิกที่รออนุมัติ</h1>
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
                            <th>เลขที่ใบขอเบิก</th>
                            <th>วันที่ขอเบิก</th>
                            <th>แผนก</th>
                            <th>ผู้ขอเบิก</th>
                            <th>สถานะ</th>
                            <th>รายการสินค้า</th>
                            <th>หมายเหตุ</th>
                            <th width="100px">การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingRequisitions as $requisition)
                            <tr>
                                <td>{{ $loop->iteration + ($pendingRequisitions->currentPage() - 1) * $pendingRequisitions->perPage() }}</td>
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
                                                <small>ขอ: {{ $item->requested_quantity }}</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>{{ $requisition->notes ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('requisitions.show', $requisition->id) }}" class="btn btn-info btn-sm" title="ดูรายละเอียดและดำเนินการ">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">ไม่พบใบขอเบิกที่รออนุมัติ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center">
                {{ $pendingRequisitions->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
