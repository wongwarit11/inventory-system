<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
@font-face { font-family: 'THSarabunNew'; font-style: normal; font-weight: normal; src: url('{{ storage_path("fonts/THSarabunNew.ttf") }}') format('truetype'); }
@font-face { font-family: 'THSarabunNew'; font-style: normal; font-weight: bold; src: url('{{ storage_path("fonts/THSarabunNew Bold.ttf") }}') format('truetype'); }
* { font-family: 'THSarabunNew', sans-serif; box-sizing: border-box; }
body { font-size: 14px; color: #2C2C2A; padding: 28px 32px; margin: 0; }

.header-table { width: 100%; margin-bottom: 0; }
.divider { border: none; border-top: 2.5px solid #185FA5; margin: 10px 0 14px 0; }
.divider-thin { border: none; border-top: 0.5px solid #D3D1C7; margin: 12px 0; }

.org-name { font-size: 17px; font-weight: bold; color: #0C447C; margin: 0 0 2px 0; }
.org-sub { font-size: 11px; color: #5F5E5A; margin: 1px 0; }

.doc-title { font-size: 22px; font-weight: bold; color: #185FA5; margin: 0; }
.doc-subtitle { font-size: 11px; color: #888780; margin: 1px 0 6px 0; }
.doc-number-badge { display: inline-block; background: #E6F1FB; padding: 4px 14px; border-radius: 6px; font-size: 12px; font-weight: bold; color: #0C447C; border: 1px solid #B5D4F4; }
.doc-date { font-size: 11px; color: #5F5E5A; margin-top: 4px; }

.info-box { border-radius: 6px; overflow: hidden; border: 1px solid #B5D4F4; width: 100%; }
.info-box-header { background: #185FA5; padding: 7px 14px; }
.info-box-header-text { font-size: 12px; color: white; font-weight: bold; letter-spacing: 0.02em; }
.info-box-body { background: #F7F5EF; padding: 10px 14px; }
.info-key { color: #888780; font-size: 12px; width: 72px; vertical-align: top; padding: 3px 0; }
.info-val { font-size: 12px; font-weight: bold; color: #2C2C2A; padding: 3px 0; }

.section-title { font-size: 13px; font-weight: bold; color: #185FA5; border-bottom: 1.5px solid #B5D4F4; padding-bottom: 5px; margin-bottom: 10px; }

.items-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.items-table thead tr { background: #185FA5; color: white; }
.items-table thead th { padding: 8px 10px; font-weight: bold; }
.items-table tbody tr.odd { background: #FFFFFF; }
.items-table tbody tr.even { background: #F7F5EF; }
.items-table tbody td { padding: 8px 10px; border-bottom: 0.5px solid #E0DDD5; }
.items-table tfoot td { padding: 8px 10px; border-top: 1.5px solid #185FA5; }

.note-box { margin-top: 10px; padding: 8px 12px; background: #EEF5FC; border-left: 3px solid #185FA5; border-radius: 0 4px 4px 0; font-size: 11px; color: #444441; }

.sig-section { width: 100%; margin-top: 28px; }
.sig-cell { width: 33%; text-align: center; vertical-align: top; padding: 0 10px; }
.sig-space { height: 48px; }
.sig-line { border-top: 1px solid #888780; padding-top: 7px; }
.sig-name { font-size: 12px; font-weight: bold; color: #2C2C2A; }
.sig-sub { font-size: 11px; color: #888780; margin-top: 2px; }
.sig-date { font-size: 11px; color: #B4B2A9; margin-top: 3px; }

.footer-table { width: 100%; margin-top: 14px; padding-top: 7px; border-top: 0.5px solid #D3D1C7; }
.footer-text { font-size: 10px; color: #B4B2A9; }
</style>
</head>
<body>

{{-- Header --}}
<table class="header-table">
  <tr>
    <td style="width: 64px; vertical-align: middle;">
      <img src="{{ public_path('images/hospital_logo.png') }}" style="width: 58px; height: 58px; border-radius: 8px;">
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

{{-- Supplier Info --}}
<table style="width: 50%; border-collapse: collapse; margin-bottom: 14px;">
  <tr>
    <td>
      <div class="info-box">
        <div class="info-box-header"><span class="info-box-header-text">ข้อมูลผู้จัดจำหน่าย</span></div>
        <div class="info-box-body">
          <table style="width: 100%; border-collapse: collapse;">
            <tr><td class="info-key">บริษัท</td><td class="info-val">{{ $supplier->name ?? '-' }}</td></tr>
            <tr><td class="info-key">ที่อยู่</td><td class="info-val">{{ $supplier->address ?? '-' }}</td></tr>
            <tr><td class="info-key">โทรศัพท์</td><td class="info-val">{{ $supplier->phone ?? '-' }}</td></tr>
            <tr><td class="info-key">อีเมล</td><td class="info-val">{{ $supplier->email ?? '-' }}</td></tr>
          </table>
        </div>
      </div>
    </td>
  </tr>
</table>

{{-- Product Table --}}
<div class="section-title">รายการสินค้าที่สั่งซื้อ</div>
<table class="items-table">
  <thead>
    <tr>
      <th style="width: 36px; text-align: center;">#</th>
      <th style="text-align: left;">ชื่อสินค้า</th>
      <th style="width: 90px; text-align: center;">จำนวนที่สั่ง</th>
      <th style="width: 70px; text-align: center;">หน่วย</th>
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
      <td colspan="2" style="text-align: right; color: #5F5E5A; font-size: 11px;">รวมทั้งหมด {{ $products->count() }} รายการ</td>
      <td style="text-align: center; font-weight: bold; color: #185FA5; font-size: 13px;">{{ $products->sum('order_quantity') }}</td>
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

{{-- Footer --}}
<table class="footer-table">
  <tr>
    <td class="footer-text" style="text-align: left;">พิมพ์เมื่อ: {{ \Carbon\Carbon::now()->timezone('Asia/Bangkok')->format('d/m/Y H:i') }} น.</td>
    <td class="footer-text" style="text-align: center;">ระบบจัดการคลังพัสดุ — มูลนิธิพระอาจารย์พบโชคเพื่อสังคม</td>
    <td class="footer-text" style="text-align: right;">หน้า 1/1</td>
  </tr>
</table>

</body>
</html>