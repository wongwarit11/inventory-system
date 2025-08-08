<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', // ชื่อหมวดหมู่
        'description', // คำอธิบายหมวดหมู่
        'status',
    ];

    // หากมี Relationships ในอนาคตกับ Products (1 Category มีได้หลาย Products)
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
