<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'batch_number',
        'quantity',
        'production_date',
        'expiration_date',
        'status',
    ];

    // ความสัมพันธ์กับ Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ความสัมพันธ์กับ StockTransaction (ล็อตสินค้า 1 ล็อตมีได้หลายรายการสต็อก)
    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    // ความสัมพันธ์กับ RequisitionItem (ล็อตสินค้า 1 ล็อตอาจถูกเบิกในหลายรายการ)
    public function requisitionItems()
    {
        return $this->hasMany(RequisitionItem::class);
    }
}
