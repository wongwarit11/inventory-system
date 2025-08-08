@extends('layouts.app')

@section('title', 'จัดการแผนก')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-building me-2 text-primary"></i> จัดการแผนก</h1>
        <a href="{{ route('departments.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 py-2">
            <i class="fas fa-plus-circle me-2"></i> เพิ่มแผนกใหม่
        </a>
    </div>
    <div class="card shadow-lg rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">ชื่อแผนก</th>
                            <th scope="col">คำอธิบาย</th>
                            <th scope="col">สถานะ</th>
                            <th scope="col" class="text-center" width="180px">การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($departments as $department)
                            <tr>
                                <td>{{ $loop->iteration + ($departments->currentPage() - 1) * $departments->perPage() }}</td>
                                <td>{{ $department->name }}</td>
                                <td>{{ $department->description ?? '-' }}</td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2 {{ $department->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $department->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-warning btn-sm me-1 rounded-pill" title="แก้ไข">
                                        <i class="fas fa-edit"></i> แก้ไข
                                    </a>
                                    <form action="{{ route('departments.destroy', $department->id) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบแผนกนี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้!');">
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
                                <td colspan="5" class="text-center py-4">ไม่พบข้อมูลแผนก</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $departments->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
