@extends('layouts.app')

@section('title', 'เพิ่มตำแหน่งเก็บสินค้า')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus me-2"></i>เพิ่มตำแหน่งเก็บสินค้าใหม่</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('locations.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="zone" class="form-label">โซน <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('zone') is-invalid @enderror" 
                                       id="zone" name="zone" value="{{ old('zone') }}" placeholder="เช่น A, B, C" required>
                                @error('zone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="shelf" class="form-label">ชั้น <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('shelf') is-invalid @enderror" 
                                       id="shelf" name="shelf" value="{{ old('shelf') }}" placeholder="เช่น 1, 2, 3" required>
                                @error('shelf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="slot" class="form-label">ช่อง <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('slot') is-invalid @enderror" 
                                       id="slot" name="slot" value="{{ old('slot') }}" placeholder="เช่น 1, 2, 3" required>
                                @error('slot')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">คำอธิบาย</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" placeholder="คำอธิบายตำแหน่ง">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">
                                    ใช้งาน
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>บันทึก
                            </button>
                            <a href="{{ route('locations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>ยกเลิก
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
