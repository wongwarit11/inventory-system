<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'requisition_number',
        'user_id',
        'department_id',
        'requisition_date',
        'status',
        'notes',
    ];

    // ความสัมพันธ์กับ User (ผู้ขอเบิก)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ความสัมพันธ์กับ Department (แผนกที่ขอเบิก)
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // ความสัมพันธ์กับ RequisitionItem (ใบขอเบิก 1 ใบ มีได้หลายรายการสินค้า)
    public function items()
    {
        return $this->hasMany(RequisitionItem::class);
    }
}
