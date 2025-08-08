@extends('layouts.app')

@section('title', 'รายละเอียดใบขอเบิก #' . $requisition->requisition_number)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-file-invoice me-2"></i> รายละเอียดใบขอเบิก #{{ $requisition->requisition_number }}</h1>
        <a href="{{ route('requisitions.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-alt-circle-left me-2"></i> กลับ</a>
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

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            ข้อมูลใบขอเบิก
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>เลขที่ใบขอเบิก:</strong> REQ-{{ $requisition->requisition_number }}</p>
                    <p><strong>วันที่ขอเบิก:</strong> {{ \Carbon\Carbon::parse($requisition->requisition_date)->format('d/m/Y') }}</p>
                    <p><strong>แผนกที่ขอเบิก:</strong> {{ $requisition->department->name ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>ผู้ขอเบิก:</strong> {{ $requisition->user->fullname ?? $requisition->user->username ?? '-' }}</p>
                    <p><strong>สถานะ:</strong>
                        @php
                            $statusClass = '';
                            switch($requisition->status) {
                                case 'pending': $statusClass = 'bg-warning text-dark'; break;
                                case 'approved': $statusClass = 'bg-primary'; break;
                                case 'rejected': $statusClass = 'bg-danger'; break;
                                case 'completed': $statusClass = 'bg-success'; break;
                                default: $statusClass = 'bg-secondary'; break;
                            }
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst($requisition->status) }}</span>
                    </p>
                    <p><strong>หมายเหตุ:</strong> {{ $requisition->notes ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            รายการสินค้าที่ขอเบิก
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>สินค้า</th>
                            <th>จำนวนที่ขอเบิก</th>
                            <th>จำนวนที่เบิกให้จริง</th>
                            <th>หมายเหตุรายการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requisition->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->product->name ?? '-' }} ({{ $item->product->product_code ?? '-' }})</td>
                                <td>{{ $item->requested_quantity }} {{ $item->product->unit ?? '' }}</td>
                                <td>{{ $item->issued_quantity }} {{ $item->product->unit ?? '' }}</td>
                                <td>{{ $item->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">ไม่พบรายการสินค้าในใบขอเบิกนี้</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ส่วนสำหรับดำเนินการเบิกสินค้า จะแสดงเฉพาะเมื่อสถานะเป็น pending หรือ approved --}}
    @if (in_array($requisition->status, ['pending', 'approved']))
        <div class="card shadow-sm" id="process-section">
            <div class="card-header bg-success text-white">
                ดำเนินการเบิกสินค้า
            </div>
            <div class="card-body">
                <form action="{{ route('requisitions.process', $requisition->id) }}" method="POST">
                    @csrf
                    <h4>รายการที่จะดำเนินการเบิก</h4>
                    <div id="process-items-container">
                        @foreach ($requisition->items as $index => $item)
                            @php
                                $product = $item->product;
                                // ดึงล็อตสินค้าที่มีสต็อกมากกว่า 0 สำหรับสินค้านี้
                                $batches = $product ? $product->batches()->where('quantity', '>', 0)->orderBy('expiration_date')->get() : collect();
                            @endphp
                            <div class="row process-item-row mb-3 border p-3 rounded">
                                <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->id }}">
                                <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">สินค้า:</label>
                                    <p class="form-control-plaintext"><strong>{{ $product->name ?? '-' }} ({{ $product->product_code ?? '-' }})</strong></p>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">ขอเบิก:</label>
                                    <p class="form-control-plaintext">{{ $item->requested_quantity }} {{ $product->unit ?? '' }}</p>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="issued_quantity_{{ $index }}" class="form-label">จำนวนที่เบิกให้จริง <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="issued_quantity_{{ $index }}" name="items[{{ $index }}][issued_quantity]" value="{{ old('items.' . $index . '.issued_quantity', $item->issued_quantity ?? 0) }}" min="0" max="{{ $item->requested_quantity }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="batch_id_{{ $index }}" class="form-label">เลือกล็อตสินค้า</label>
                                    <select class="form-select" id="batch_id_{{ $index }}" name="items[{{ $index }}][batch_id]">
                                        <option value="">-- ไม่เลือกล็อต --</option>
                                        @foreach ($batches as $batch)
                                            <option value="{{ $batch->id }}" {{ old('items.' . $index . '.batch_id') == $batch->id ? 'selected' : '' }}>
                                                {{ $batch->batch_number }} (คงเหลือ: {{ $batch->quantity }} {{ $product->unit ?? '' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">สต็อกที่มี: {{ $batches->sum('quantity') }} {{ $product->unit ?? '' }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check-circle me-2"></i> ดำเนินการเบิก</button>
                </form>
            </div>
        </div>
    @endif
@endsection
