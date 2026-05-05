@extends('layouts.app')

@section('title', 'Barcode Label')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4">ป้ายบาร์โค้ด</h1>
            <p class="text-muted mb-0">{{ $type === 'product' ? 'รหัสสินค้า' : 'ล็อตสินค้า' }}</p>
        </div>
        <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print me-2"></i>พิมพ์บาร์โค้ด</button>
    </div>

    <div class="card mx-auto" style="max-width: 420px;">
        <div class="card-body text-center">
            <div class="barcode-label p-4 border rounded">
                @if ($type === 'product')
                    <h2 class="h5 mb-2">{{ $product->name }}</h2>
                    <p class="mb-1">รหัสสินค้า: <strong>{{ $product->product_code }}</strong></p>
                    @if ($product->manufacturer)
                        <p class="mb-1">ผู้ผลิต: {{ $product->manufacturer->name }}</p>
                    @endif
                @else
                    <h2 class="h5 mb-2">{{ $batch->product->name }}</h2>
                    <p class="mb-1">รหัสสินค้า: <strong>{{ $batch->product->product_code }}</strong></p>
                    <p class="mb-1">ล็อต: <strong>{{ $batch->batch_number }}</strong></p>
                    <p class="mb-1">วันหมดอายุ: <strong>{{ $batch->expiration_date ? \Carbon\Carbon::parse($batch->expiration_date)->format('Y-m-d') : '-' }}</strong></p>
                    @if ($batch->location)
                        <p class="mb-1">ตำแหน่ง: <strong>{{ $batch->location->full_location }}</strong></p>
                    @endif
                @endif

                <div class="my-3">
                    <img src="{{ $barcode }}" alt="Barcode" class="img-fluid" />
                </div>

                <p class="mb-0 text-muted">{{ $type === 'product' ? $product->product_code : $batch->batch_number }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .card, .card * {
            visibility: visible;
        }
        .card {
            width: 100%;
            box-shadow: none;
        }
        .barcode-label {
            border: none;
        }
    }
</style>
@endpush
