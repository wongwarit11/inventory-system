<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'batch_id', // อาจเป็น null ได้ถ้าไม่ระบุล็อต
        'user_id',
        'department_id', // อาจเป็น null ได้
        'transaction_type', // 'in', 'out', 'adjustment'
        'quantity',
        'transaction_date',
        'notes',
    ];

    // Relationship: รายการสต็อกเป็นของสินค้า (Many-to-One)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship: รายการสต็อกอาจผูกกับล็อตสินค้า (Many-to-One, Nullable)
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    // Relationship: ผู้ทำรายการ (Many-to-One)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship: แผนกที่เกี่ยวข้อง (Many-to-One, Nullable)
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}