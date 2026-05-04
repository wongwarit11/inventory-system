@extends('layouts.app')

@section('title', 'สแกนบาร์โค้ด')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="fas fa-qrcode me-2 text-primary"></i> สแกนบาร์โค้ด</h1>
            <p class="text-muted mb-0">ใช้กล้องเว็บแคมสแกนบาร์โค้ดสินค้า หรือรหัสล็อต เพื่อดูข้อมูลและดำเนินการต่อทันที</p>
        </div>
        <a href="{{ route('stock_transactions.index') }}" class="btn btn-secondary rounded-pill px-4 py-2">
            <i class="fas fa-arrow-alt-circle-left me-2"></i> กลับหน้าสต็อก
        </a>
    </div>

    <div class="row gy-4">
        <div class="col-lg-6">
            <div class="card shadow-sm rounded-4 p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">กล้องสแกน</h5>
                    <button id="toggleScannerBtn" class="btn btn-primary btn-sm rounded-pill px-4">เริ่มสแกน</button>
                </div>
                <div id="scanner-reader" class="border rounded-4 overflow-hidden" style="min-height:320px; display:none;"></div>
                <div id="scanner-status" class="mt-3 text-muted">กดปุ่มเริ่มสแกนเพื่อเปิดกล้อง</div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm rounded-4 p-4 h-100">
                <h5 class="mb-3">ข้อมูลรายการที่สแกน</h5>
                <div id="scannedInfo" class="mb-4">
                    <p class="text-muted">ยังไม่มีข้อมูลสินค้า</p>
                </div>

                <div class="d-grid gap-2">
                    <a id="receiveAction" href="#" class="btn btn-success btn-lg rounded-pill disabled" role="button"><i class="fas fa-arrow-alt-circle-down me-2"></i> รับเข้า</a>
                    <a id="issueAction" href="#" class="btn btn-warning btn-lg rounded-pill disabled" role="button"><i class="fas fa-arrow-alt-circle-up me-2"></i> จ่ายออก</a>
                    <a id="adjustAction" href="#" class="btn btn-info btn-lg rounded-pill disabled" role="button"><i class="fas fa-sliders-h me-2"></i> ปรับสต็อก</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.12/minified/html5-qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleScannerBtn = document.getElementById('toggleScannerBtn');
        const scannerReader = document.getElementById('scanner-reader');
        const scannerStatus = document.getElementById('scanner-status');
        const scannedInfo = document.getElementById('scannedInfo');
        const receiveAction = document.getElementById('receiveAction');
        const issueAction = document.getElementById('issueAction');
        const adjustAction = document.getElementById('adjustAction');

        let html5QrCode = null;
        let scanning = false;

        function setActionUrls(info) {
            const productId = info.product_id || info.id;
            const batchParam = info.batch_id ? `&batch_id=${info.batch_id}` : '';
            receiveAction.href = `{{ route('stock_transactions.receive.create') }}?product_id=${productId}${batchParam}`;
            issueAction.href = `{{ route('stock_transactions.issue.create') }}?product_id=${productId}${batchParam}`;
            adjustAction.href = `{{ route('stock_transactions.adjust.create') }}?product_id=${productId}${batchParam}`;
            receiveAction.classList.remove('disabled');
            issueAction.classList.remove('disabled');
            adjustAction.classList.remove('disabled');
        }

        function clearScannedInfo() {
            scannedInfo.innerHTML = '<p class="text-muted">ยังไม่มีข้อมูลสินค้า</p>';
            receiveAction.classList.add('disabled');
            issueAction.classList.add('disabled');
            adjustAction.classList.add('disabled');
        }

        function showScannedInfo(info) {
            const typeLabel = info.type === 'batch' ? 'ล็อต' : 'สินค้า';
            scannedInfo.innerHTML = `
                <div class="mb-3">
                    <span class="badge bg-secondary">${typeLabel}</span>
                </div>
                <p class="mb-2"><strong>ชื่อ:</strong> ${info.name || '-'}<br>
                <strong>รหัส:</strong> ${info.code || '-'}${info.product_code ? `<br><strong>รหัสสินค้า:</strong> ${info.product_code}` : ''}</p>
                <p class="mb-2"><strong>สต็อก:</strong> ${info.stock ?? '-'} ${info.unit ? info.unit : ''}</p>
                <p class="mb-0"><strong>ตำแหน่ง:</strong> ${info.location || '-'}</p>
            `;
            setActionUrls(info);
        }

        function setStatus(message, isError = false) {
            scannerStatus.textContent = message;
            scannerStatus.classList.toggle('text-danger', isError);
            scannerStatus.classList.toggle('text-muted', !isError);
        }

        function startScanner() {
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode('scanner-reader');
            }
            scannerReader.style.display = 'block';
            html5QrCode.start(
                { facingMode: 'environment' },
                {
                    fps: 10,
                    qrbox: { width: 320, height: 240 }
                },
                decodedText => {
                    html5QrCode.stop().then(() => {
                        scanning = false;
                        toggleScannerBtn.textContent = 'เริ่มสแกน';
                        setStatus(`พบบาร์โค้ด: ${decodedText}`);
                        fetch('{{ route('scanner.scan') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ barcode: decodedText })
                        })
                        .then(response => response.json().then(data => ({ status: response.status, body: data })))
                        .then(result => {
                            if (result.status === 200) {
                                showScannedInfo(result.body);
                            } else {
                                clearScannedInfo();
                                setStatus(result.body.message || 'ไม่พบบาร์โค้ดนี้', true);
                            }
                        })
                        .catch(() => {
                            clearScannedInfo();
                            setStatus('เกิดข้อผิดพลาดในการสแกน', true);
                        });
                    });
                },
                errorMessage => {
                    setStatus('กำลังสแกน...');
                }
            ).catch(error => {
                setStatus('ไม่สามารถใช้งานกล้องได้: ' + error, true);
            });
            scanning = true;
            toggleScannerBtn.textContent = 'หยุดสแกน';
            setStatus('กำลังสแกน...');
        }

        function stopScanner() {
            if (html5QrCode && scanning) {
                html5QrCode.stop().then(() => {
                    scanning = false;
                    scannerReader.style.display = 'none';
                    toggleScannerBtn.textContent = 'เริ่มสแกน';
                    setStatus('สแกนหยุดแล้ว');
                }).catch(() => {
                    setStatus('ไม่สามารถหยุดกล้องได้', true);
                });
            }
        }

        toggleScannerBtn.addEventListener('click', function () {
            if (scanning) {
                stopScanner();
            } else {
                startScanner();
            }
        });

        clearScannedInfo();
    });
</script>
@endpush
