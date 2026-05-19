@extends('layouts.app')

@section('title', 'แก้ไขรายการรับเข้า')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-edit me-2 text-primary"></i> แก้ไขรายการรับเข้า</h1>
    <a href="{{ route('stock_transactions.index') }}" class="btn btn-secondary rounded-pill px-4">
        <i class="fas fa-arrow-alt-circle-left me-2"></i> กลับ
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-lg rounded-4">
    <div class="card-body p-4">
        <div class="mb-3 p-3 bg-light rounded">
            <strong>สินค้า:</strong> {{ $transaction->product->name ?? '-' }} ({{ $transaction->product->product_code ?? '-' }})<br>
            <strong>ล็อต:</strong> {{ $transaction->batch->batch_number ?? '-' }}
        </div>

        <form action="{{ route('stock_transactions.receive.update', $transaction->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">จำนวน <span class="text-danger">*</span></label>
                    <input type="number" name="quantity" class="form-control rounded-pill @error('quantity') is-invalid @enderror"
                           value="{{ old('quantity', $transaction->quantity) }}" min="1" required>
                    @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">วันที่ทำรายการ <span class="text-danger">*</span></label>
                    <input type="date" name="transaction_date" class="form-control rounded-pill @error('transaction_date') is-invalid @enderror"
                           value="{{ old('transaction_date', \Carbon\Carbon::parse($transaction->transaction_date)->format('Y-m-d')) }}" required>
                    @error('transaction_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">แผนก</label>
                <select name="department_id" class="form-select rounded-pill @error('department_id') is-invalid @enderror">
                    <option value="">-- ไม่ระบุแผนก --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id', $transaction->department_id) == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">หมายเหตุ</label>
                <textarea name="notes" class="form-control rounded-3 @error('notes') is-invalid @enderror" rows="3"
                          placeholder="หมายเหตุเพิ่มเติม">{{ old('notes', $transaction->notes) }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success btn-lg rounded-pill px-5">
                <i class="fas fa-save me-2"></i> บันทึก
            </button>
        </form>
    </div>
</div>
@endsection