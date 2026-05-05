<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LowStockExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filtered;

    public function __construct($filtered)
    {
        $this->filtered = $filtered;
    }

    public function collection()
    {
        return $this->filtered;
    }

    public function headings(): array
    {
        return [
            'รหัสสินค้า',
            'ชื่อสินค้า',
            'ผู้จัดจำหน่าย',
            'หน่วยนับ',
            'ราคา',
        ];
    }

    public function map($product): array
    {
        return [
            $product->product_code,
            $product->name,
            optional($product->supplier)->name,
            $product->unit,
            number_format($product->cost_price,2),  
        ];
    }

}

