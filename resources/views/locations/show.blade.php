@extends('layouts.app')

@section('title', 'รายละเอียดตำแหน่งเก็บสินค้า')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-map-marker-alt me-2"></i>{{ $location->full_location }}</h2>
                <div>
                    <a href="{{ route('locations.edit', $location) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>แก้ไข
                    </a>
                    <a href="{{ route('locations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>กลับ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>รายละเอียดตำแหน่ง</h6>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">โซน:</dt>
                        <dd class="col-sm-8"><strong>{{ $location->zone }}</strong></dd>

                        <dt class="col-sm-4">ชั้น:</dt>
                        <dd class="col-sm-8"><strong>{{ $location->shelf }}</strong></dd>

                        <dt class="col-sm-4">ช่อง:</dt>
                        <dd class="col-sm-8"><strong>{{ $location->slot }}</strong></dd>

                        <dt class="col-sm-4">สถานะ:</dt>
                        <dd class="col-sm-8">
                            @if ($location->is_active)
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>ใช้งาน</span>
                            @else
                                <span class="badge bg-danger"><i class="fas fa-times me-1"></i>ปิดใช้งาน</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">คำอธิบาย:</dt>
                        <dd class="col-sm-8">{{ $location->description ?? '-' }}</dd>

                        <dt class="col-sm-4">เพิ่มเมื่อ:</dt>
                        <dd class="col-sm-8">{{ $location->created_at->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-4">อัปเดตเมื่อ:</dt>
                        <dd class="col-sm-8">{{ $location->updated_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-boxes me-2"></i>สรุป Batch ที่เก็บ</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="display-4 text-primary">{{ $location->batches()->count() }}</h3>
                        <p class="text-muted">Batch ทั้งหมด</p>
                    </div>
                    <hr>
                    <div>
                        <small class="text-muted">
                            <i class="fas fa-cubes me-1"></i>จำนวน Batch ที่เก็บอยู่ในตำแหน่งนี้
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-list me-2"></i>รายการ Batch ที่เก็บในตำแหน่งนี้</h6>
        </div>
        <div class="card-body">
            @if ($batches->count())
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>เลขล็อต</th>
                                <th>ชื่อสินค้า</th>
                                <th>จำนวน</th>
                                <th>วันผลิต</th>
                                <th>วันหมดอายุ</th>
                                <th>สถานะ</th>
                                <th>การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($batches as $key => $batch)
                                <tr>
                                    <td>{{ $batches->firstItem() + $key }}</td>
                                    <td><strong>{{ $batch->batch_number }}</strong></td>
                                    <td>{{ $batch->product->name }}</td>
                                    <td>{{ $batch->quantity }}</td>
                                    <td>{{ $batch->manufacture_date->format('d/m/Y') }}</td>
                                    <td>
                                        @if ($batch->expiration_date)
                                            {{ $batch->expiration_date->format('d/m/Y') }}
                                            @if ($batch->expiration_date->isPast())
                                                <span class="badge bg-danger">หมดอายุ</span>
                                            @elseif ($batch->expiration_date->diffInDays() < 30)
                                                <span class="badge bg-warning">ใกล้หมดอายุ</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $batch->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($batch->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('batches.show', $batch) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $batches->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">ไม่มี Batch ที่เก็บในตำแหน่งนี้</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
