<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: "TH Sarabun New", sans-serif; font-size: 18px; }
        .header { text-align: center; margin-bottom: 20px; }
        .logo { width: 80px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; }
        .signature { margin-top: 40px; display: flex; justify-content: space-between; }
        .signature div { width: 45%; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('images/hospital_logo.png') }}" class="logo">
        <h2>ใบขอเบิกเวชภัณฑ์ / สินค้า</h2>
        <p>เลขที่ใบขอเบิก: <strong>{{ $requestForm->code }}</strong></p>
        <p>วันที่ขอเบิก: {{ \Carbon\Carbon::parse($requestForm->requested_at)->format('d/m/Y') }}</p>
    </div>

    <p><strong>ผู้ขอเบิก:</strong> {{ $requestForm->requester->name }}</p>
    <p><strong>หน่วยงาน:</strong> {{ $requestForm->department->name }}</p>
    <p><strong>สถานะใบขอเบิก:</strong> {{ ucfirst($requestForm->status) }}</p>

    <table>
        <thead>
            <tr>
                <th>ลำดับ</th>
                <th>รหัสสินค้า</th>
                <th>ชื่อสินค้า</th>
                <th>หน่วยนับ</th>
                <th>จำนวนที่ขอเบิก</th>
                <th>หมายเหตุ</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requestForm->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product->code }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->product->unit }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->note ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <div>
            <p>..............................................</p>
            <p>ลายเซ็นผู้ขอเบิก</p>
            <p>วันที่: ____ / ____ / ______</p>
        </div>
        <div>
            <p>..............................................</p>
            <p>ลายเซ็นผู้อนุมัติ</p>
            <p>วันที่: ____ / ____ / ______</p>
        </div>
    </div>

</body>
</html>