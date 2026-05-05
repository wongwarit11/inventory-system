<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบขอเบิกพัสดุ</title>
    <style>
        @font-face {
            font-family: 'THSarabunNew';
            src: url('{{ storage_path("fonts/THSarabunNew.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'THSarabunNew';
            src: url('{{ storage_path("fonts/THSarabunNew Bold.ttf") }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        @font-face {
            font-family: 'THSarabunNew';
            src: url('{{ storage_path("fonts/THSarabunNew Italic.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: italic;
        }
        @font-face {
            font-family: 'THSarabunNew';
            src: url('{{ storage_path("fonts/THSarabunNew BoldItalic.ttf") }}') format('truetype');
            font-weight: bold;
            font-style: italic;
        }

        * {
            font-family: 'THSarabunNew', sans-serif !important;
        }

        body {
            font-family: 'THSarabunNew', sans-serif;
            font-size: 16px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            width: 80px;
            height: auto;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        .info {
            margin-bottom: 20px;
        }
        .info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
        }
        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            width: 30%;
            text-align: center;
        }
        .signature p {
            margin: 5px 0;
        }
        .line {
            border-top: 1px solid #000;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/hpk_logo.png') }}" alt="Logo" class="logo">
        <div class="title">ใบขอเบิกพัสดุ</div>
        <p>โรงพยาบาลวัดห้วยปลากั้งเพื่อสังคม</p>
    </div>

    <div class="info">
        <p><strong>เลขที่ใบขอเบิก:</strong> REQ-{{ $requisition->requisition_number }}</p>
        <p><strong>วันที่:</strong> {{ \Carbon\Carbon::parse($requisition->requisition_date)->format('d/m/Y') }}</p>
        <p><strong>แผนกที่ขอเบิก:</strong> {{ $requisition->department->name ?? '-' }}</p>
        <p><strong>ผู้ขอเบิก:</strong> {{ $requisition->user->fullname ?? $requisition->user->username ?? '-' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ลำดับ</th>
                <th>รหัสสินค้า</th>
                <th>ชื่อสินค้า</th>
                <th>Lot/Batch</th>
                <th>จำนวนที่ขอ</th>
                <th>หน่วยนับ</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requisition->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product->product_code ?? '-' }}</td>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td>-</td>
                    <td>{{ $item->requested_quantity }}</td>
                    <td>{{ $item->product->unit ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signatures">
        <div class="signature">
            <p>ผู้ขอเบิก</p>
            <div class="line"></div>
            <p>({{ $requisition->user->fullname ?? $requisition->user->username ?? '-' }})</p>
            <p>วันที่: ____ / ____ / ______</p>
        </div>
        <div class="signature">
            <p>ผู้อนุมัติ</p>
            <div class="line"></div>
            <p>(..............................................)</p>
            <p>วันที่: ____ / ____ / ______</p>
        </div>
        <div class="signature">
            <p>ผู้จ่ายพัสดุ</p>
            <div class="line"></div>
            <p>(..............................................)</p>
            <p>วันที่: ____ / ____ / ______</p>
        </div>
    </div>
</body>
</html>