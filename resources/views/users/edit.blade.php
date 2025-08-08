@extends('layouts.app')

@section('title', 'แก้ไขผู้ใช้งาน: ' . $user->username)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-edit me-2 text-primary"></i> แก้ไขผู้ใช้งาน: <span class="text-secondary">{{ $user->username }}</span></h1>
        <a href="{{ route('users.index') }}" class="btn btn-secondary shadow-sm rounded-pill px-4 py-2">
            <i class="fas fa-arrow-alt-circle-left me-2"></i> กลับ
        </a>
    </div>

    {{-- แสดงข้อผิดพลาดจากการ Validation --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <h5 class="alert-heading mb-2"><i class="fas fa-exclamation-triangle me-2"></i> พบข้อผิดพลาด:</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-lg rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="username" class="form-label fw-bold">ชื่อผู้ใช้งาน <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg rounded-pill @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $user->username) }}" required placeholder="กรอกชื่อผู้ใช้งาน">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fullname" class="form-label fw-bold">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg rounded-pill @error('fullname') is-invalid @enderror" id="fullname" name="fullname" value="{{ old('fullname', $user->fullname) }}" required placeholder="กรอกชื่อ-นามสกุล">
                            @error('fullname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">รหัสผ่าน (เว้นว่างหากไม่ต้องการเปลี่ยน)</label>
                            <input type="password" class="form-control form-control-lg rounded-pill @error('password') is-invalid @enderror" id="password" name="password" placeholder="กรอกรหัสผ่านใหม่ (อย่างน้อย 8 ตัวอักษร)">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-bold">ยืนยันรหัสผ่าน</label>
                            <input type="password" class="form-control form-control-lg rounded-pill" id="password_confirmation" name="password_confirmation" placeholder="ยืนยันรหัสผ่านใหม่">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">อีเมล</label>
                            <input type="email" class="form-control rounded-pill @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="กรอกอีเมล (ถ้ามี)">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="phone" class="form-label fw-bold">เบอร์โทรศัพท์</label>
                            <input type="text" class="form-control rounded-pill @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="กรอกเบอร์โทรศัพท์ (ถ้ามี)">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="role" class="form-label fw-bold">บทบาท <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg rounded-pill @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="">-- เลือกบทบาท --</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                                <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">สถานะ <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg rounded-pill @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                                <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg shadow-sm rounded-pill px-5 py-2 mt-3">
                    <i class="fas fa-save me-2"></i> บันทึกการแก้ไข
                </button>
            </form>
        </div>
    </div>
@endsection
