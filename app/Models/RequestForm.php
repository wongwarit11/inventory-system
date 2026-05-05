<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestForm extends Model
{
    protected $fillable = ['item_product', 'requester', 'quantity'];
}