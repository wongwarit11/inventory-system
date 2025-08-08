@extends('layouts.app')

@section('title', 'จัดการสินค้า')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-box me-2 text-primary"></i> จัดการสินค้า</h1>
        <a href="{{ route('products.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 py-2">
            <i class="fas fa-plus-circle me-2"></i> เพิ่มสินค้าใหม่
        </a>
    </div>
    <div class="card shadow-lg rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col">#</th>
                            <th>รูปภาพ</th> {{-- เพิ่มคอลัมน์รูปภาพ --}}
                            <th scope="col">รหัสสินค้า</th>
                            <th scope="col">ชื่อสินค้า</th>
                            <th scope="col">หมวดหมู่</th>
                            <th scope="col">ประเภทสินค้า</th> {{-- เพิ่มคอลัมน์ประเภทสินค้า --}}
                            <th scope="col">ผู้จำหน่าย</th>
                            <th scope="col">ผู้ผลิต</th> {{-- เพิ่มคอลัมน์ผู้ผลิต --}}
                            <th scope="col">หน่วยนับ</th>
                            <th scope="col">สต็อกต่ำสุด</th>
                            <th scope="col">ราคาต้นทุน</th>
                            <th scope="col">สถานะ</th>
                            <th scope="col" class="text-center" width="180px">การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</td>
                                <td>
                                    @if ($product->image_path)
                                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 0.25rem;">
                                    @else
                                        <i class="fas fa-image text-muted" style="font-size: 2.5rem;"></i> {{-- Icon placeholder --}}
                                    @endif
                                </td> {{-- แสดงรูปภาพ --}}
                                <td>{{ $product->product_code }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name ?? '-' }}</td>
                                <td>{{ $product->productType->name ?? '-' }}</td> {{-- แสดงชื่อประเภทสินค้า --}}
                                <td>{{ $product->supplier->name ?? '-' }}</td>
                                <td>{{ $product->manufacturer->name ?? '-' }}</td> {{-- แสดงชื่อผู้ผลิต --}}
                                <td>{{ $product->unit }}</td>
                                <td>{{ number_format($product->minimum_stock_level) }}</td>
                                <td>{{ number_format($product->cost_price, 2) }}</td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2 {{ $product->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $product->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-sm me-1 rounded-pill" title="แก้ไข">
                                        <i class="fas fa-edit"></i> แก้ไข
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบสินค้านี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้!');">
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
                                <td colspan="12" class="text-center py-4">ไม่พบข้อมูลสินค้า</td> {{-- อัปเดต colspan เป็น 12 --}}
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
