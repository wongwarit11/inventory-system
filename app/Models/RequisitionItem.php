<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'requisition_id',
        'product_id',
        'requested_quantity',
        'issued_quantity', // จำนวนที่เบิกให้จริง (อาจเป็น null หรือ 0 ในตอนแรก)
        'notes', // เพิ่ม notes สำหรับรายการขอเบิกแต่ละรายการ
    ];

    // Relationship: รายการขอเบิกเป็นของใบขอเบิก (Many-to-One)
    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    // Relationship: รายการขอเบิกเป็นของสินค้า (Many-to-One)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}