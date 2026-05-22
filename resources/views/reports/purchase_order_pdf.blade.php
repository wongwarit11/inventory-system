<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
@font-face { font-family: 'THSarabunNew'; font-style: normal; font-weight: normal; src: url('{{ storage_path("fonts/THSarabunNew.ttf") }}') format('truetype'); }
@font-face { font-family: 'THSarabunNew'; font-style: normal; font-weight: bold; src: url('{{ storage_path("fonts/THSarabunNew Bold.ttf") }}') format('truetype'); }
* { font-family: 'THSarabunNew', sans-serif; }
body { font-size: 14px; color: #2C2C2A; padding: 30px; }
.header-table { width: 100%; border-bottom: 2px solid #185FA5; padding-bottom: 12px; margin-bottom: 14px; }
.hospital-name { font-size: 16px; font-weight: bold; color: #0C447C; }
.hospital-sub { font-size: 12px; color: #5F5E5A; }
.doc-title { font-size: 20px; font-weight: bold; color: #185FA5; }
.doc-number { background: #E6F1FB; padding: 4px 12px; border-radius: 6px; font-size: 13px; color: #0C447C; font-weight: bold; }
.info-box { border-radius: 6px; overflow: hidden; border: 1px solid #B5D4F4; }
.info-box-header { background: #185FA5; padding: 7px 14px; }
.info-box-header-text { font-size: 12px; color: white; font-weight: bold; }
.info-box-body { background: #F1EFE8; padding: 10px 14px; }
.info-key { color: #5F5E5A; font-size: 12px; width: 70px; }
.info-val { font-weight: bold; color: #2C2C2A; font-size: 12px; }
.section-title { font-size: 13px; font-weight: bold; color: #185FA5; border-bottom: 1px solid #B5D4F4; padding-bottom: 6px; margin-bottom: 10px; }
.items-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.items-table thead tr { background: #185FA5; color: white; }
.items-table thead th { padding: 8px 10px; text-align: center; }
.items-table tbody tr.odd { background: white; }
.items-table tbody tr.even { background: #F1EFE8; }
.items-table tbody td { padding: 8px 10px; border-bottom: 1px solid #D3D1C7; }
.sig-line { border-top: 1px solid #888780; padding-top: 8px; margin-top: 50px; text-align: center; }
.sig-name { font-size: 12px; font-weight: bold; }
.sig-sub { font-size: 11px; color: #5F5E5A; margin-top: 2px; }
.sig-date { font-size: 11px; color: #888780; margin-top: 4px; }
</style>
</head>
<body>

<table class="header-table">
  <tr>
    <td style="width: 60px; vertical-align: middle;">
      <img src="{{ public_path('images/hospital_logo.png') }}" style="width: 56px; height: 56px;">
    </td>
    <td style="vertical-align: middle; padding-left: 10px;">
      <div class="hospital-name">โรงพยาบาลวัดห้วยปลากั้งเพื่อสังคม</div>
      <div class="hospital-sub">Wat Huay Pakang Hospital for Society</div>
    </td>
    <td style="text-align: right; vertical-align: middle;">
      <div class="doc-title">ใบสั่งซื้อพัสดุ</div>
      <div style="font-size: 11px; color: #5F5E5A;">Purchase Order</div>
      <div style="margin-top: 4px;"><span class="doc-number">{{ $poNumber }}</span></div>
      <div style="font-size: 11px; color: #5F5E5A; margin-top: 3px;">วันที่ {{ \Carbon\Carbon::now()->timezone('Asia/Bangkok')->format('d/m/Y') }}</div>
    </td>
  </tr>
</table>

<table style="width: 100%; border-collapse: separate; border-spacing: 10px 0; margin-bottom: 14px;">
  <tr>
    <td style="width: 50%; vertical-align: top;">
      <div class="info-box">
        <div class="info-box-header"><span class="info-box-header-text">ข้อมูลผู้สั่งซื้อ</span></div>
        <div class="info-box-body">
          <table style="width: 100%;">
            <tr><td class="info-key">ชื่อ</td><td class="info-val">โรงพยาบาลวัดห้วยปลากั้งเพื่อสังคม</td></tr>
            <tr><td class="info-key" style="vertical-align: top;">ที่อยู่</td><td class="info-val">553/11 ม.14 ต.บ้านดู่ อ.เมืองเชียงราย จ.เชียงราย 57100</td></tr>
            <tr><td class="info-key">เบอร์โทร</td><td class="info-val">052 029 888</td></tr>
          </table>
        </div>
      </div>
    </td>
    <td style="width: 50%; vertical-align: top;">
      <div class="info-box">
        <div class="info-box-header"><span class="info-box-header-text">ข้อมูลผู้จัดจำหน่าย</span></div>
        <div class="info-box-body">
          <table style="width: 100%;">
            <tr><td class="info-key">บริษัท</td><td class="info-val">{{ $supplier->name ?? '-' }}</td></tr>
            <tr><td class="info-key" style="vertical-align: top;">ที่อยู่</td><td class="info-val">{{ $supplier->address ?? '-' }}</td></tr>
            <tr><td class="info-key">โทรศัพท์</td><td class="info-val">{{ $supplier->phone ?? '-' }}</td></tr>
            <tr><td class="info-key">อีเมล</td><td class="info-val">{{ $supplier->email ?? '-' }}</td></tr>
          </table>
        </div>
      </div>
    </td>
  </tr>
</table>

<div class="section-title">รายการสินค้าที่สั่งซื้อ</div>
<table class="items-table">
  <thead>
    <tr>
      <th style="width: 36px;">#</th>
      <th style="text-align: left;">ชื่อสินค้า</th>
      <th style="width: 80px;">จำนวนที่สั่ง</th>
      <th style="width: 70px;">หน่วย</th>
    </tr>
  </thead>
  <tbody>
    @foreach($products as $index => $product)
    <tr class="{{ $index % 2 == 0 ? 'odd' : 'even' }}">
      <td style="text-align: center; color: #5F5E5A;">{{ $index + 1 }}</td>
      <td>{{ $product->name }}</td>
      <td style="text-align: center; font-weight: bold;">{{ $product->order_quantity }}</td>
      <td style="text-align: center; color: #5F5E5A;">{{ $product->unit }}</td>
    </tr>
    @endforeach
    <tr>
      <td colspan="2" style="text-align: right; color: #5F5E5A; font-size: 11px; border-bottom: none;">รวมทั้งหมด</td>
      <td style="text-align: center; font-weight: bold; color: #185FA5; border-bottom: none;">{{ $products->sum('order_quantity') }}</td>
      <td style="text-align: center; font-size: 11px; color: #5F5E5A; border-bottom: none;">{{ $products->count() }} รายการ</td>
    </tr>
  </tbody>
</table>

<div style="margin-top: 8px; padding: 8px 10px; background: #E6F1FB; border-radius: 6px; font-size: 11px; color: #5F5E5A;">
  หมายเหตุ: กรุณาส่งใบเสนอราคาภายในวันที่กำหนด หากมีข้อสงสัยกรุณาติดต่อ 052 029 888
</div>

<table style="width: 100%; margin-top: 24px;">
  <tr>
    <td style="width: 33%; text-align: center; vertical-align: top; padding: 0 8px;">
      <div class="sig-line">
        <div class="sig-name">ผู้สั่งซื้อ</div>
        <div class="sig-sub">(................................)</div>
        <div class="sig-date">วันที่ ......./......./.......</div>
      </div>
    </td>
    <td style="width: 33%; text-align: center; vertical-align: top; padding: 0 8px;">
      <div class="sig-line">
        <div class="sig-name">ผู้อนุมัติ</div>
        <div class="sig-sub">(................................)</div>
        <div class="sig-date">วันที่ ......./......./.......</div>
      </div>
    </td>
    <td style="width: 33%; text-align: center; vertical-align: top; padding: 0 8px;">
      <div class="sig-line">
        <div class="sig-name">ผู้จัดจำหน่าย</div>
        <div class="sig-sub">(................................)</div>
        <div class="sig-date">วันที่ ......./......./.......</div>
      </div>
    </td>
  </tr>
</table>

<table style="width: 100%; margin-top: 16px; border-top: 1px solid #D3D1C7; padding-top: 8px;">
  <tr>
    <td style="font-size: 10px; color: #888780; text-align: left;">พิมพ์เมื่อ: {{ \Carbon\Carbon::now()->timezone('Asia/Bangkok')->format('d/m/Y H:i') }} น.</td>
    <td style="font-size: 10px; color: #888780; text-align: center;">ระบบจัดการคลังพัสดุ — โรงพยาบาลวัดห้วยปลากั้งเพื่อสังคม</td>
    <td style="font-size: 10px; color: #888780; text-align: right;">หน้า 1/1</td>
  </tr>
</table>

</body>
</html>