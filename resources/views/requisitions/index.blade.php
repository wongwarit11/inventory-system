@extends('layouts.app')

@section('title', 'จัดการใบขอเบิก')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-file-invoice me-2 text-primary"></i> จัดการใบขอเบิก</h1>
        <a href="{{ route('requisitions.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 py-2">
            <i class="fas fa-plus-circle me-2"></i> สร้างใบขอเบิกใหม่
        </a>
    </div>

    <div class="card shadow-lg rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">เลขที่ใบขอเบิก</th>
                            <th scope="col">วันที่ขอเบิก</th>
                            <th scope="col">แผนก</th>
                            <th scope="col">ผู้ขอเบิก</th>
                            <th scope="col">สถานะ</th>
                            <th scope="col">หมายเหตุ</th>
                            <th scope="col" class="text-center" width="180px">การดำเนินการ</th>
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
                                    <span class="badge rounded-pill px-3 py-2 {{ $statusClass }}">{{ ucfirst($requisition->status) }}</span>
                                </td>
                                <td>{{ $requisition->notes ?? '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('requisitions.show', $requisition->id) }}" class="btn btn-info btn-sm me-1 rounded-pill" title="ดูรายละเอียด">
                                        <i class="fas fa-eye"></i> ดู
                                    </a>
                                    {{-- อนุญาตให้แก้ไขได้เฉพาะสถานะ pending และผู้ใช้ที่มีสิทธิ์ --}}
                                    @if ($requisition->status === 'pending' && (Auth::user()->role === 'admin' || Auth::user()->role === 'manager' || Auth::id() === $requisition->user_id))
                                        <a href="{{ route('requisitions.edit', $requisition->id) }}" class="btn btn-warning btn-sm me-1 rounded-pill" title="แก้ไข">
                                            <i class="fas fa-edit"></i> แก้ไข
                                        </a>
                                    @endif
                                    {{-- อนุญาตให้ลบได้เฉพาะสถานะ pending และ Admin เท่านั้น --}}
                                    @if ($requisition->status === 'pending' && Auth::user()->role === 'admin')
                                        <form action="{{ route('requisitions.destroy', $requisition->id) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบใบขอเบิกนี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้!');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm rounded-pill" title="ลบ">
                                                <i class="fas fa-trash-alt"></i> ลบ
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">ไม่พบข้อมูลใบขอเบิก</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $requisitions->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
