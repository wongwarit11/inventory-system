<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'fullname',
        'email',
        'phone',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class, 'user_id');
    }

    public function requisitions()
    {
        return $this->hasMany(Requisition::class, 'requested_by');
    }

    public function approvedRequisitions()
    {
        return $this->hasMany(Requisition::class, 'approved_by');
    }

    public function issuedRequisitions()
    {
        return $this->hasMany(Requisition::class, 'issued_by');
    }
}
