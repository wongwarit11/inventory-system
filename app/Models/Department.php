<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // Relationships
    public function requisitions()
    {
        return $this->hasMany(Requisition::class, 'department_id');
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class, 'department_id');
    }
}
