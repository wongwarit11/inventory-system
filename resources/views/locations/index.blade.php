@extends('layouts.app')

@section('title', 'ตำแหน่งเก็บสินค้า')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-map-marker-alt me-2"></i>ตำแหน่งเก็บสินค้า</h2>
                @if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'manager'))
                    <a href="{{ route('locations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>เพิ่มตำแหน่งใหม่
                    </a>
                @endif
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if ($locations->count())
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th><i class="fas fa-map-marker me-2"></i>ตำแหน่ง</th>
                                <th>โซน</th>
                                <th>ชั้น</th>
                                <th>ช่อง</th>
                                <th>คำอธิบาย</th>
                                <th>สถานะ</th>
                                <th>จำนวน Batch</th>
                                <th>การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($locations as $key => $location)
                                <tr>
                                    <td>{{ $locations->firstItem() + $key }}</td>
                                    <td><strong>{{ $location->full_location }}</strong></td>
                                    <td>{{ $location->zone }}</td>
                                    <td>{{ $location->shelf }}</td>
                                    <td>{{ $location->slot }}</td>
                                    <td>{{ $location->description ?? '-' }}</td>
                                    <td>
                                        @if ($location->is_active)
                                            <span class="badge bg-success"><i class="fas fa-check me-1"></i>ใช้งาน</span>
                                        @else
                                            <span class="badge bg-danger"><i class="fas fa-times me-1"></i>ปิดใช้งาน</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $location->batches()->count() }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('locations.show', $location) }}" class="btn btn-info btn-sm" title="ดูรายละเอียด">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'manager'))
                                            <a href="{{ route('locations.edit', $location) }}" class="btn btn-warning btn-sm" title="แก้ไข">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('locations.destroy', $location) }}" method="POST" style="display: inline;" onsubmit="return confirm('ยืนยันการลบ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="ลบ">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $locations->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">ยังไม่มีตำแหน่งเก็บสินค้า</p>
                    @if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'manager'))
                        <a href="{{ route('locations.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>เพิ่มตำแหน่งใหม่
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
