<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory;

    // กำหนดคอลัมน์ที่สามารถ assign ค่าได้ (Mass Assignment)
    protected $fillable = [
        'name',
        'description',
        'status',
    ];
}
