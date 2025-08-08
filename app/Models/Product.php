<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'manufacturer_id', 
        'product_type_id',
        'name',
        'product_code',
        'unit',
        'cost_price',
        'minimum_stock_level',
        'status',
        'image_path', // เพิ่ม image_path ที่นี่
    ];

    // Relationship: สินค้าเป็นของหมวดหมู่หนึ่ง (Many-to-One)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship: สินค้ามาจากผู้จำหน่ายหนึ่งราย (Many-to-One)
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // ความสัมพันธ์กับ Manufacturer
    public function manufacturer() // <-- เพิ่มเมธอดนี้
    {
        return $this->belongsTo(Manufacturer::class);
    }

    // ความสัมพันธ์กับ ProductType
    public function productType() // <-- เพิ่มเมธอดนี้
    {
        return $this->belongsTo(ProductType::class);
    }

    // Relationship: สินค้าหนึ่งชนิดมีได้หลายล็อต (One-to-Many)
    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    // Relationship: สินค้าหนึ่งชนิดมีการทำรายการสต็อกได้หลายรายการ (One-to-Many)
    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    // Relationship: สินค้าหนึ่งชนิดสามารถถูกขอเบิกได้หลายรายการ (One-to-Many)
    public function requisitionItems()
    {
        return $this->hasMany(RequisitionItem::class);
    }
}