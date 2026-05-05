<div class="card shadow-lg rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">ชื่อประเภทสินค้า</th>
                        <th scope="col">คำอธิบาย</th>
                        <th scope="col">สถานะ</th>
                        <th scope="col" class="text-center" width="180px">การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productTypes as $productType)
                        <tr>
                            <td>{{ $loop->iteration + ($productTypes->currentPage() - 1) * $productTypes->perPage() }}</td>
                            <td>{{ $productType->name }}</td>
                            <td>{{ $productType->description ?? '-' }}</td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2 {{ $productType->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $productType->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('product-types.edit', $productType->id) }}" class="btn btn-warning btn-sm me-1 rounded-pill" title="แก้ไข">
                                    <i class="fas fa-edit"></i> แก้ไข
                                </a>
                                <form action="{{ route('product-types.destroy', $productType->id) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบประเภทสินค้านี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้!');">
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
                            <td colspan="5" class="text-center py-4">ไม่พบข้อมูลประเภทสินค้า</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Links --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $productTypes->appends(request()->input())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
