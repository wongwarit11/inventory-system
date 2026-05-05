<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $batches;

    public function __construct($batches)
    {
        $this->batches = $batches;
    }

    public function collection()
    {
        return $this->batches;
    }

    public function headings(): array
    {
        return [
            'รหัสสินค้า',
            'ชื่อสินค้า',
            'ผู้จัดจำหน่าย',
            'รหัสล็อต',
            'วันหมดอายุ',
            'ราคาต้นทุน (ต่อหน่วย)',
            'จุดต่ำสุดที่ต้องสั่งซื้อ',
            'จำนวนคงเหลือ',
            'หน่วยนับ',
        ];
    }

    public function map($batch): array
    {
        return [
            $batch->product->product_code ?? '-',
            $batch->product->name ?? '-',
            optional($batch->product->supplier)->name ?: '-',
            $batch->batch_number,
            $batch->expiration_date ? date('d/m/Y', strtotime($batch->expiration_date)) : '-',
            number_format($batch->product->cost_price ?? 0, 2),
            $batch->product->minimum_stock_level ?? '-',
            $batch->quantity,
            $batch->product->unit ?? '-',
        ];
    }
}
