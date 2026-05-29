<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
@font-face { font-family: 'THSarabunNew'; font-style: normal; font-weight: normal; src: url('{{ storage_path("fonts/THSarabunNew.ttf") }}') format('truetype'); }
@font-face { font-family: 'THSarabunNew'; font-style: normal; font-weight: bold; src: url('{{ storage_path("fonts/THSarabunNew Bold.ttf") }}') format('truetype'); }
* { font-family: 'THSarabunNew', sans-serif; box-sizing: border-box; }

@page {
    margin-top: 115px;
    margin-bottom: 60px;
    margin-left: 32px;
    margin-right: 32px;
}

body {
    font-size: 16px;
    color: #2C2C2A;
    margin: 0;
    padding: 0;
}

/* ===== FIXED HEADER — repeats on every page ===== */
#pdf-header {
    position: fixed;
    top: -115px;
    left: 0;
    right: 0;
    height: 115px;
}
.header-table { width: 100%; border-collapse: collapse; }
.divider { border: none; border-top: 2.5px solid #185FA5; margin: 8px 0 0 0; }
.org-name { font-size: 20px; font-weight: bold; color: #0C447C; margin: 0 0 2px 0; }
.org-sub { font-size: 13px; color: #5F5E5A; margin: 1px 0; }
.doc-title { font-size: 26px; font-weight: bold; color: #185FA5; margin: 0; }
.doc-subtitle { font-size: 13px; color: #888780; margin: 1px 0 5px 0; }
.doc-number-badge { display: inline-block; background: #E6F1FB; padding: 3px 12px; border-radius: 6px; font-size: 13px; font-weight: bold; color: #0C447C; border: 1px solid #B5D4F4; }
.doc-date { font-size: 13px; color: #5F5E5A; margin-top: 3px; }

/* ===== FIXED FOOTER — repeats on every page ===== */
#pdf-footer {
    position: fixed;
    bottom: -60px;
    left: 0;
    right: 0;
    height: 60px;
    border-top: 0.5px solid #D3D1C7;
    padding-top: 8px;
}
.footer-table { width: 100%; border-collapse: collapse; }
.footer-text { font-size: 12px; color: #B4B2A9; }
.page-number:before { content: counter(page); }
.page-count:before  { content: counter(pages); }

/* ===== MAIN CONTENT ===== */
.supplier-box {
    background: #E6F1FB;
    border: 0.5px solid #B5D4F4;
    border-radius: 6px;
    padding: 10px 14px;
    width: 55%;
    margin-bottom: 14px;
}
.supplier-label { font-size: 12px; font-weight: bold; color: #0C447C; margin: 0 0 5px 0; }
.supplier-name  { font-size: 14px; font-weight: bold; color: #2C2C2A; margin: 0 0 3px 0; }
.supplier-detail { font-size: 12px; color: #5F5E5A; margin: 0 0 2px 0; }

.section-title { font-size: 16px; font-weight: bold; color: #185FA5; border-bottom: 1.5px solid #B5D4F4; padding-bottom: 5px; margin-bottom: 10px; }

.items-table { width: 100%; border-collapse: collapse; font-size: 15px; }
.items-table thead tr { background: #185FA5; color: white; }
.items-table thead th { padding: 9px 10px; font-weight: bold; }
.items-table tbody tr.odd  { background: #FFFFFF; }
.items-table tbody tr.even { background: #F7F5EF; }
.items-table tbody td { padding: 7px 10px; border-bottom: 0.5px solid #E0DDD5; }
.items-table tfoot td { padding: 8px 10px; border-top: 1.5px solid #185FA5; font-size: 14px; }
.items-table tbody tr { page-break-inside: avoid; }

.note-box { margin-top: 10px; padding: 8px 12px; background: #EEF5FC; border-left: 3px solid #185FA5; border-radius: 0 4px 4px 0; font-size: 14px; color: #444441; }

.sig-section { width: 100%; border-collapse: collapse; margin-top: 32px; page-break-inside: avoid; }
.sig-cell { width: 33.3%; text-align: center; vertical-align: top; padding: 0 10px; }
.sig-space { height: 48px; }
.sig-line { border-top: 1px solid #888780; padding-top: 7px; }
.sig-name { font-size: 16px; font-weight: bold; color: #2C2C2A; margin: 0; }
.sig-sub  { font-size: 14px; color: #888780; margin: 2px 0 0 0; }
.sig-date { font-size: 14px; color: #B4B2A9; margin: 3px 0 0 0; }
</style>
</head>
<body>

{{-- ===== FIXED HEADER (repeats every page) ===== --}}
<div id="pdf-header">
    <table class="header-table">
        <tr>
            <td style="width: 60px; vertical-align: middle;">
                <img src="{{ public_path('images/hospital_logo.png') }}" style="width: 54px; height: 54px; border-radius: 8px;">
            </td>
            <td style="vertical-align: middle; padding-left: 12px;">
                <p class="org-name">มูลนิธิพระอาจารย์พบโชคเพื่อสังคม</p>
                <p class="org-sub">553 หมู่ 14 ต.บ้านดู่ อ.เมืองเชียงราย จ.เชียงราย 57100</p>
                <p class="org-sub">เลขประจำตัวผู้เสียภาษี: 099-3-00048408-8 &nbsp;|&nbsp; โทร: 052 029 888</p>
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <p class="doc-title">ใบสั่งซื้อพัสดุ</p>
                <p class="doc-subtitle">Purchase Order</p>
                <span class="doc-number-badge">{{ $poNumber }}</span>
                <p class="doc-date">วันที่ {{ \Carbon\Carbon::now()->timezone('Asia/Bangkok')->format('d/m/Y') }}</p>
            </td>
        </tr>
    </table>
    <hr class="divider">
</div>

{{-- ===== FIXED FOOTER (repeats every page) ===== --}}
<div id="pdf-footer">
    <table class="footer-table">
        <tr>
            <td class="footer-text" style="text-align: left; width: 40%;">
                พิมพ์เมื่อ: {{ \Carbon\Carbon::now()->timezone('Asia/Bangkok')->format('d/m/Y H:i') }} น.
            </td>
            <td class="footer-text" style="text-align: center; width: 35%;">
                ระบบจัดการคลังพัสดุ — มูลนิธิพระอาจารย์พบโชคเพื่อสังคม
            </td>
            <td class="footer-text" style="text-align: right; width: 25%;">
                หน้า <span class="page-number"></span>/<span class="page-count"></span>
            </td>
        </tr>
    </table>
</div>

{{-- ===== MAIN CONTENT ===== --}}

{{-- Supplier Info --}}
<div class="supplier-box">
    <p class="supplier-label">ผู้จัดจำหน่าย</p>
    <p class="supplier-name">{{ $supplier->name ?? '-' }}</p>
    @if(!empty($supplier->address))
    <p class="supplier-detail">{{ $supplier->address }}</p>
    @endif
    @if(!empty($supplier->phone))
    <p class="supplier-detail">โทร: {{ $supplier->phone }}</p>
    @endif
    @if(!empty($supplier->email))
    <p class="supplier-detail">อีเมล: {{ $supplier->email }}</p>
    @endif
</div>

{{-- Product Table --}}
<div class="section-title">รายการสินค้าที่สั่งซื้อ</div>
<table class="items-table">
    <thead>
        <tr>
            <th style="width: 36px; text-align: center;">#</th>
            <th style="text-align: left;">ชื่อสินค้า</th>
            <th style="width: 80px; text-align: center;">จำนวนที่สั่ง</th>
            <th style="width: 100px; text-align: center;">หน่วย</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $index => $product)
        <tr class="{{ $index % 2 == 0 ? 'odd' : 'even' }}">
            <td style="text-align: center; color: #888780;">{{ $index + 1 }}</td>
            <td>{{ $product->name }}</td>
            <td style="text-align: center; font-weight: bold; color: #185FA5;">{{ $product->order_quantity }}</td>
            <td style="text-align: center; color: #5F5E5A;">{{ $product->unit }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" style="text-align: right; color: #5F5E5A;">รวมทั้งหมด {{ $products->count() }} รายการ</td>
            <td style="text-align: center; font-weight: bold; color: #185FA5;">{{ $products->sum('order_quantity') }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>

<div class="note-box">
    หมายเหตุ: กรุณาส่งใบเสนอราคาภายในวันที่กำหนด หากมีข้อสงสัยกรุณาติดต่อ 052 029 888
</div>

{{-- Signatures --}}
<table class="sig-section">
    <tr>
        <td class="sig-cell">
            <div class="sig-space"></div>
            <div class="sig-line">
                <p class="sig-name">ผู้สั่งซื้อ</p>
                <p class="sig-sub">(................................)</p>
                <p class="sig-date">วันที่ ......./......./.......</p>
            </div>
        </td>
        <td class="sig-cell">
            <div class="sig-space"></div>
            <div class="sig-line">
                <p class="sig-name">ผู้อนุมัติ</p>
                <p class="sig-sub">(................................)</p>
                <p class="sig-date">วันที่ ......./......./.......</p>
            </div>
        </td>
        <td class="sig-cell">
            <div class="sig-space"></div>
            <div class="sig-line">
                <p class="sig-name">ผู้จัดจำหน่าย</p>
                <p class="sig-sub">(................................)</p>
                <p class="sig-date">วันที่ ......./......./.......</p>
            </div>
        </td>
    </tr>
</table>

</body>
</html>
