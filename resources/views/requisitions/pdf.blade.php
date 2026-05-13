<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'THSarabunNew', sans-serif; font-size: 14px; color: #2C2C2A; padding: 30px; }
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
.status-badge { background: #FAEEDA; color: #633806; padding: 3px 12px; border-radius: 6px; font-size: 12px; font-weight: bold; }
.section-title { font-size: 13px; font-weight: bold; color: #185FA5; border-bottom: 1px solid #B5D4F4; padding-bottom: 6px; margin-bottom: 10px; }
.items-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.items-table thead tr { background: #185FA5; color: white; }
.items-table thead th { padding: 8px 10px; text-align: center; }
.items-table tbody tr.odd { background: white; }
.items-table tbody tr.even { background: #F1EFE8; }
.items-table tbody td { padding: 8px 10px; text-align: center; border-bottom: 1px solid #D3D1C7; }
.sig-line { border-top: 1px solid #888780; padding-top: 8px; margin-top: 50px; text-align: center; }
.sig-name { font-size: 12px; font-weight: bold; }
.sig-sub { font-size: 11px; color: #5F5E5A; margin-top: 2px; }
.sig-date { font-size: 11px; color: #888780; margin-top: 4px; }
</style>
</head>
<body>

<table class="header-table" style="width: 100%; margin-bottom: 14px; border-bottom: 2px solid #185FA5; padding-bottom: 12px;">
  <tr>
    <td style="width: 60px; vertical-align: middle;">
      <img src="{{ public_path('images/hospital_logo.png') }}" style="width: 56px; height: 56px;">
    </td>
    <td style="vertical-align: middle; padding-left: 10px;">
      <div class="hospital-name">โรงพยาบาลวัดห้วยหลากั้งเพื่อสังคม</div>
      <div class="hospital-sub">Wat Huay Pakang Hospital for Society</div>
    </td>
    <td style="text-align: right; vertical-align: middle;">
      <div class="doc-title">ใบขอเบิกพัสดุ</div>
      <div style="font-size: 11px; color: #5F5E5A;">Material Requisition Form</div>
      <div style="margin-top: 4px;"><span class="doc-number">{{ $requisition->requisition_number }}</span></div>
    </td>
  </tr>
</table>

<table style="width: 100%; margin-bottom: 14px; border-collapse: separate; border-spacing: 10px 0;">
  <tr>
    <td style="width: 50%; vertical-align: top;">
      <div class="info-box">
        <div class="info-box-header">
          <span class="info-box-header-text">ข้อมูลผู้ขอเบิก</span>
        </div>
        <div class="info-box-body">
          <table style="width: 100%;">
            <tr>
              <td class="info-key">ผู้ขอเบิก</td>
              <td class="info-val">{{ $requisition->user->fullname ?? '-' }}</td>
            </tr>
            <tr>
              <td class="info-key">แผนก</td>
              <td class="info-val">{{ $requisition->department->name ?? '-' }}</td>
            </tr>
            <tr>
              <td class="info-key">วันที่ขอ</td>
              <td class="info-val">{{ \Carbon\Carbon::parse($requisition->requisition_date)->locale('th')->translatedFormat('j F Y') }}</td>
            </tr>
          </table>
        </div>
      </div>
    </td>
    <td style="width: 50%; vertical-align: top;">
      <div class="info-box">
        <div class="info-box-header">
          <span class="info-box-header-text">สถานะเอกสาร</span>
        </div>
        <div class="info-box-body">
          <table style="width: 100%;">
            <tr>
              <td class="info-key">สถานะ</td>
              <td>
                <span class="status-badge">
                  @if($requisition->status == 'pending') รอดำเนินการ (Pending)
                  @elseif($requisition->status == 'approved') อนุมัติแล้ว (Approved)
                  @elseif($requisition->status == 'issued') จ่ายแล้ว (Issued)
                  @else ยกเลิก (Cancelled) @endif
                </span>
              </td>
            </tr>
            <tr>
              <td class="info-key">หมายเหตุ</td>
              <td class="info-val">{{ $requisition->notes ?? '-' }}</td>
            </tr>
          </table>
        </div>
      </div>
    </td>
  </tr>
</table>

<div class="section-title" style="margin-bottom: 10px;">รายการสินค้าที่ขอเบิก</div>
<table class="items-table">
  <thead>
    <tr>
      <th style="width: 36px;">#</th>
      <th style="width: 80px;">รหัสสินค้า</th>
      <th style="text-align: left;">ชื่อสินค้า</th>
      <th style="width: 80px;">Lot/Batch</th>
      <th style="width: 70px;">จำนวนขอ</th>
      <th style="width: 70px;">จำนวนจ่าย</th>
      <th style="width: 60px;">หน่วย</th>
    </tr>
  </thead>
  <tbody>
    @foreach($requisition->items as $index => $item)
    <tr class="{{ $index % 2 == 0 ? 'odd' : 'even' }}">
      <td>{{ $index + 1 }}</td>
      <td style="color: #185FA5; font-weight: bold;">{{ $item->product->product_code ?? '-' }}</td>
      <td style="text-align: left;">{{ $item->product->name ?? '-' }}</td>
      <td>{{ $item->batch->batch_number ?? '-' }}</td>
      <td style="font-weight: bold;">{{ $item->requested_quantity }}</td>
      <td>{{ $item->issued_quantity ?? '___' }}</td>
      <td>{{ $item->product->unit ?? '-' }}</td>
    </tr>
    @endforeach
    <tr>
      <td colspan="4" style="text-align: right; color: #5F5E5A; font-size: 11px; border-bottom: none;">รวมทั้งหมด</td>
      <td style="font-weight: bold; color: #185FA5; border-bottom: none;">{{ $requisition->items->sum('requested_quantity') }}</td>
      <td colspan="2" style="border-bottom: none;"></td>
    </tr>
  </tbody>
</table>

<table style="width: 100%; margin-top: 20px;">
  <tr>
    <td style="width: 33%; text-align: center; vertical-align: top; padding: 0 10px;">
      <div class="sig-line">
        <div class="sig-name">ผู้ขอเบิก</div>
        <div class="sig-sub">({{ $requisition->user->fullname ?? '................................' }})</div>
        <div class="sig-date">วันที่ ......./......./.......</div>
      </div>
    </td>
    <td style="width: 33%; text-align: center; vertical-align: top; padding: 0 10px;">
      <div class="sig-line">
        <div class="sig-name">ผู้อนุมัติ</div>
        <div class="sig-sub">(................................)</div>
        <div class="sig-date">วันที่ ......./......./.......</div>
      </div>
    </td>
    <td style="width: 33%; text-align: center; vertical-align: top; padding: 0 10px;">
      <div class="sig-line">
        <div class="sig-name">ผู้จ่ายพัสดุ</div>
        <div class="sig-sub">(................................)</div>
        <div class="sig-date">วันที่ ......./......./.......</div>
      </div>
    </td>
  </tr>
</table>

<table style="width: 100%; margin-top: 20px; border-top: 1px solid #D3D1C7; padding-top: 8px;">
  <tr>
    <td style="font-size: 10px; color: #888780; text-align: left;">
      พิมพ์เมื่อ: {{ \Carbon\Carbon::now()->timezone('Asia/Bangkok')->format('d/m/Y H:i') }} น.
    </td>
    <td style="font-size: 10px; color: #888780; text-align: center;">
      ระบบจัดการคลังพัสดุ — โรงพยาบาลวัดห้วยหลากั้งเพื่อสังคม
    </td>
    <td style="font-size: 10px; color: #888780; text-align: right;">
      หน้า 1/1
    </td>
  </tr>
</table>

</body>
</html>