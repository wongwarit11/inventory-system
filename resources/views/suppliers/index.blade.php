@extends('layouts.app') {{-- บอกว่าไฟล์ Blade นี้จะใช้โครงสร้าง Layout หลักที่ชื่อ 'app' --}}

@section('title', 'จัดการผู้จำหน่าย') {{-- กำหนด title เฉพาะหน้านี้ --}}

@section('content') {{-- กำหนดส่วนของเนื้อหาหลัก --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-truck-moving me-2 text-primary"></i> จัดการผู้จำหน่าย</h1> {{-- เพิ่ม text-primary --}}
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 py-2"> {{-- ปรับสไตล์ปุ่ม --}}
            <i class="fas fa-plus-circle me-2"></i> เพิ่มผู้จำหน่ายใหม่
        </a>
    </div>
    <div class="card shadow-lg rounded-4"> {{-- ปรับสไตล์ card --}}
        <div class="card-body p-4"> {{-- ปรับ padding --}}
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle"> {{-- เพิ่ม align-middle --}}
                    <thead class="table-primary"> {{-- เปลี่ยนสีหัวตาราง --}}
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">ชื่อผู้จำหน่าย</th>
                            <th scope="col">ผู้ติดต่อ</th>
                            <th scope="col">เบอร์โทรศัพท์</th>
                            <th scope="col">อีเมล</th>
                            <th scope="col">ที่อยู่</th>
                            <th scope="col">สถานะ</th>
                            <th scope="col" class="text-center" width="180px">การดำเนินการ</th> {{-- เพิ่ม text-center --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $supplier)
                            <tr>
                                <td>{{ $loop->iteration + ($suppliers->currentPage() - 1) * $suppliers->perPage() }}</td> {{-- แก้ไข pagination iteration --}}
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->contact_person ?? '-' }}</td>
                                <td>{{ $supplier->phone ?? '-' }}</td>
                                <td>{{ $supplier->email ?? '-' }}</td>
                                <td>{{ $supplier->address ?? '-' }}</td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2 {{ $supplier->status == 'active' ? 'bg-success' : 'bg-secondary' }}"> {{-- ปรับสไตล์ badge --}}
                                        {{ $supplier->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                    </span>
                                </td>
                                <td class="text-center"> {{-- เพิ่ม text-center --}}
                                    <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-warning btn-sm me-1 rounded-pill" title="แก้ไข"> {{-- ปรับสไตล์ปุ่ม --}}
                                        <i class="fas fa-edit"></i> แก้ไข
                                    </a>
                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบผู้จำหน่ายรายนี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้!');"> {{-- เพิ่มข้อความยืนยัน --}}
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded-pill" title="ลบ"> {{-- ปรับสไตล์ปุ่ม --}}
                                            <i class="fas fa-trash-alt"></i> ลบ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">ไม่พบข้อมูลผู้จำหน่าย</td> {{-- อัปเดต colspan และเพิ่ม py-4 --}}
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center mt-3"> {{-- เพิ่ม mt-3 --}}
                {{ $suppliers->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
