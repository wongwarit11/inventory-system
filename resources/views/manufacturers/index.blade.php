@extends('layouts.app')

@section('title', 'จัดการผู้ผลิต')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-industry me-2 text-primary"></i> จัดการผู้ผลิต</h1>
        <a href="{{ route('manufacturers.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 py-2">
            <i class="fas fa-plus-circle me-2"></i> เพิ่มผู้ผลิตใหม่
        </a>
    </div>

    <div class="card shadow-lg rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">ชื่อผู้ผลิต</th>
                            <th scope="col">ผู้ติดต่อ</th>
                            <th scope="col">เบอร์โทรศัพท์</th>
                            <th scope="col">อีเมล</th>
                            <th scope="col">ที่อยู่</th>
                            <th scope="col">สถานะ</th>
                            <th scope="col" class="text-center" width="180px">การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($manufacturers as $manufacturer)
                            <tr>
                                <td>{{ $loop->iteration + ($manufacturers->currentPage() - 1) * $manufacturers->perPage() }}</td>
                                <td>{{ $manufacturer->name }}</td>
                                <td>{{ $manufacturer->contact_person ?? '-' }}</td>
                                <td>{{ $manufacturer->phone ?? '-' }}</td>
                                <td>{{ $manufacturer->email ?? '-' }}</td>
                                <td>{{ $manufacturer->address ?? '-' }}</td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2 {{ $manufacturer->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $manufacturer->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('manufacturers.edit', $manufacturer->id) }}" class="btn btn-warning btn-sm me-1 rounded-pill" title="แก้ไข">
                                        <i class="fas fa-edit"></i> แก้ไข
                                    </a>
                                    <form action="{{ route('manufacturers.destroy', $manufacturer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบผู้ผลิตรายนี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้!');">
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
                                <td colspan="8" class="text-center py-4">ไม่พบข้อมูลผู้ผลิต</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $manufacturers->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
