@extends('layouts.app')

@section('title', 'ปรับปรุงสต็อก')

@section('content')
    <div class="container py-4"> {{-- เพิ่ม .container เพื่อให้ได้ max-width ที่กำหนดใน app.blade.php --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-sliders-h me-2 text-primary"></i> ปรับปรุงสต็อก</h1>
            <a href="{{ route('stock_transactions.index') }}" class="btn btn-secondary shadow-sm rounded-pill px-4 py-2">
                <i class="fas fa-arrow-alt-circle-left me-2"></i> กลับหน้ารายการสต็อก
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
                <form action="{{ route('stock_transactions.adjust.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="product_id" class="form-label fw-bold mb-0">
                                        สินค้า <span class="text-danger">*</span>
                                    </label>
                                    <button type="button" id="scannerToggleBtn" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                        <i class="fas fa-qrcode me-1"></i> สแกนบาร์โค้ด
                                    </button>
                                </div>
                                <label class="form-text text-muted mb-2">กดปุ่มเพื่อสแกนบาร์โค้ดสินค้าและเลือกสินค้านั้นโดยอัตโนมัติ</label>
                                <select class="form-select form-select-lg rounded-pill @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                                    <option value="">-- เลือกสินค้า --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" data-unit="{{ $product->unit }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }} ({{ $product->product_code }})</option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="batch_id" class="form-label fw-bold">
                                    เลือกล็อตสินค้า <span class="text-danger">*</span>
                                    <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="เลือกล็อตสิ้นค้าที่ต้องการปรับปรุง, (จำเป็น)"></i>
                                </label>
                                <select class="form-select form-select-lg rounded-pill @error('batch_id') is-invalid @enderror" id="batch_id" name="batch_id" required>
                                    <option value="">-- เลือกล็อตสินค้า --</option>
                                    {{-- Options จะถูกโหลดด้วย AJAX --}}
                                </select>
                                @error('batch_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted" id="batch_quantity_display">
                                    {{-- แสดงจำนวนคงเหลือของล็อตที่เลือก --}}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div id="scanner_widget" class="mb-4" style="display:none;">
                        <div class="card border-info rounded-4 shadow-sm p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-1">สแกนบาร์โค้ด</h6>
                                    <p class="text-muted mb-0">สแกนรหัสสินค้าเพื่อนำข้อมูลไปเติมในฟอร์มอัตโนมัติ</p>
                                </div>
                                <span id="scannerStatus" class="badge bg-secondary">พร้อมสแกน</span>
                            </div>
                            <div id="scannerHolder" style="min-height:260px; width:100%;"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quantity" class="form-label fw-bold">
                                    จำนวนที่ปรับปรุง <span class="text-danger">*</span>
                                    <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="ระบุจำนวนที่ต้องการปรับปรุง, (จำเป็น)"></i>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control form-control-lg rounded-start-pill @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', 0) }}" required placeholder="จำนวน (บวกเพื่อเพิ่ม, ลบเพื่อลด)">
                                    <span class="input-group-text rounded-end-pill" id="unit_display">หน่วย</span>
                                </div>
                                <small class="form-text text-muted">ใส่ค่าบวกเพื่อเพิ่มสต็อก, ใส่ค่าลบเพื่อลดสต็อก</small>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_date" class="form-label fw-bold">
                                    วันที่ทำรายการ <span class="text-danger">*</span>
                                    <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="ระบุวันที่ทำรายการ/วันที่ปรับปรุงสต็อก, (จำเป็น)"></i>
                                </label>
                                <input type="date" class="form-control form-control-lg rounded-pill @error('transaction_date') is-invalid @enderror" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                                @error('transaction_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="department_id" class="form-label fw-bold">
                            แผนกที่เกี่ยวข้อง (ถ้ามี)
                            <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="เลือกแผนก, (ใส่ก็ได้หรือไม่ใส่ก็ได้)"></i>
                        </label>
                        <select class="form-select form-select-lg rounded-pill @error('department_id') is-invalid @enderror" id="department_id" name="department_id">
                            <option value="">-- เลือกแผนก --</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label fw-bold">
                            หมายเหตุ <span class="text-danger">*</span>
                            <i class="fas fa-info-circle custom-tooltip-icon ms-1"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="ระบุเหตุผลในการปรับปรุงสต็อก (เช่น สต็อกขาด, สต็อกเกิน, ชำรุด), (จำเป็น)"></i>
                        </label>
                        <textarea class="form-control rounded-3 @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" required placeholder="เหตุผลในการปรับปรุงสต็อก (เช่น สต็อกขาด, สต็อกเกิน, ชำรุด)"></textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" id="submitButton" class="btn btn-primary btn-lg shadow-sm rounded-pill px-5 py-2 w-100">
                        <span id="buttonText"><i class="fas fa-save me-2"></i>บันทึกการปรับปรุง</span>
                        <span id="loadingSpinner" class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true" style="display: none;"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/html5-qrcode@2.3.12/minified/html5-qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productIdSelect = document.getElementById('product_id');
            const batchIdSelect = document.getElementById('batch_id');
            const unitDisplay = document.getElementById('unit_display');
            const batchQuantityDisplay = document.getElementById('batch_quantity_display');
            let availableBatches = []; // To store batch data for quantity display

            // Function to update unit display
            function updateUnitDisplay() {
                const selectedProductOption = productIdSelect.options[productIdSelect.selectedIndex];
                const unit = selectedProductOption.dataset.unit || 'หน่วย';
                unitDisplay.textContent = unit;
            }

            // Function to load batches via AJAX
            function loadBatchesForProduct(productId) {
                batchIdSelect.innerHTML = '<option value="">กำลังโหลด...</option>'; // Loading indicator
                batchQuantityDisplay.textContent = ''; // Clear batch quantity display
                if (productId) {
                    fetch(`/api/products/${productId}/batches`)
                        .then(response => response.json())
                        .then(data => {
                            availableBatches = data; // Store fetched batches
                            batchIdSelect.innerHTML = '<option value="">-- เลือกล็อตสินค้า --</option>';
                            data.forEach(batch => {
                                const option = document.createElement('option');
                                option.value = batch.id;
                                option.textContent = `${batch.batch_number} (คงเหลือ: ${batch.quantity})`;
                                // Retain old selected value after validation error
                                if ("{{ old('batch_id') }}" === batch.id.toString()) {
                                    option.selected = true;
                                }
                                batchIdSelect.appendChild(option);
                            });
                            // Trigger change to update quantity display if an old batch was selected
                            if ("{{ old('batch_id') }}") {
                                updateBatchQuantityDisplay();
                            }
                        })
                        .catch(error => {
                            console.error('Error loading batches:', error);
                            batchIdSelect.innerHTML = '<option value="">ไม่สามารถโหลดล็อตสินค้าได้</option>';
                            batchQuantityDisplay.textContent = '';
                        });
                } else {
                    batchIdSelect.innerHTML = '<option value="">-- เลือกล็อตสินค้า --</option>';
                    batchQuantityDisplay.textContent = '';
                }
            }

            // Function to update batch quantity display
            function updateBatchQuantityDisplay() {
                const selectedBatchId = batchIdSelect.value;
                const selectedBatch = availableBatches.find(batch => batch.id == selectedBatchId);
                const unit = productIdSelect.options[productIdSelect.selectedIndex].dataset.unit || 'หน่วย';

                if (selectedBatch) {
                    batchQuantityDisplay.textContent = `จำนวนคงเหลือในล็อต: ${selectedBatch.quantity} ${unit}`;
                    // For adjust, we don't set max directly on quantity as it can be negative
                } else {
                    batchQuantityDisplay.textContent = '';
                }
            }

            // Initial setup on page load
            const oldProductId = "{{ old('product_id') }}";
            const queryProductId = "{{ request()->query('product_id', '') }}";
            const queryBatchId = "{{ request()->query('batch_id', '') }}";

            if (queryProductId && !oldProductId) {
                productIdSelect.value = queryProductId;
                updateUnitDisplay();
                loadBatchesForProduct(queryProductId, queryBatchId || null);
            } else if (oldProductId) {
                loadBatchesForProduct(oldProductId, queryBatchId || null);
            }

            // Event Listeners
            productIdSelect.addEventListener('change', function() {
                updateUnitDisplay();
                loadBatchesForProduct(this.value);
            });

            batchIdSelect.addEventListener('change', updateBatchQuantityDisplay);

            const scannerToggleBtn = document.getElementById('scannerToggleBtn');
            const scannerWidget = document.getElementById('scanner_widget');
            const scannerHolder = document.getElementById('scannerHolder');
            const scannerStatus = document.getElementById('scannerStatus');
            let html5QrCode = null;
            let scanning = false;

            function setScannerStatus(message, isError = false) {
                scannerStatus.textContent = message;
                scannerStatus.classList.toggle('bg-danger', isError);
                scannerStatus.classList.toggle('bg-secondary', !isError);
                scannerStatus.classList.toggle('bg-warning', !isError && message === 'กำลังสแกน...');
                scannerStatus.classList.toggle('bg-success', !isError && message !== 'กำลังสแกน...');
            }

            function showScanner() {
                scannerWidget.style.display = 'block';
                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode('scannerHolder');
                }
                html5QrCode.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: { width: 300, height: 200 } },
                    decodedText => {
                        if (scanning) {
                            scanning = false;
                            html5QrCode.stop().then(() => {
                                scannerToggleBtn.textContent = 'สแกนบาร์โค้ด';
                                handleBarcode(decodedText);
                            }).catch(() => {
                                scannerToggleBtn.textContent = 'สแกนบาร์โค้ด';
                            });
                        }
                    },
                    errorMessage => {
                        setScannerStatus('กำลังสแกน...');
                    }
                ).then(() => {
                    scanning = true;
                    scannerToggleBtn.textContent = 'หยุดสแกน';
                    setScannerStatus('กำลังสแกน...');
                }).catch(error => {
                    setScannerStatus('ไม่สามารถเปิดกล้องได้', true);
                    console.error(error);
                });
            }

            function hideScanner() {
                if (html5QrCode && scanning) {
                    html5QrCode.stop().then(() => {
                        scanning = false;
                        scannerToggleBtn.textContent = 'สแกนบาร์โค้ด';
                        setScannerStatus('หยุดสแกนแล้ว');
                    }).catch(() => {
                        setScannerStatus('ไม่สามารถหยุดกล้องได้', true);
                    });
                }
            }

            function handleBarcode(decodedText) {
                setScannerStatus('พบบาร์โค้ด: ' + decodedText);
                fetch('{{ route('scanner.scan') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ barcode: decodedText })
                })
                .then(response => response.json().then(body => ({ status: response.status, body })))
                .then(result => {
                    if (result.status === 200) {
                        const data = result.body;
                        const productId = data.product_id || data.id;
                        productIdSelect.value = productId;
                        updateUnitDisplay();
                        loadBatchesForProduct(productId, data.batch_id ? data.batch_id.toString() : null);
                    } else {
                        setScannerStatus(result.body.message || 'ไม่พบบาร์โค้ดนี้ในระบบ', true);
                    }
                })
                .catch(() => {
                    setScannerStatus('เกิดข้อผิดพลาดในการสแกน', true);
                });
            }

            scannerToggleBtn.addEventListener('click', function () {
                if (scanning) {
                    hideScanner();
                } else {
                    showScanner();
                }
            });
        });
    </script>
@endsection
