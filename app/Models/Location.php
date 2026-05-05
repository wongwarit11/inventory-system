<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'zone',
        'shelf',
        'slot',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the batches stored at this location.
     */
    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    /**
     * Get the full location string representation.
     */
    public function getFullLocationAttribute()
    {
        return "{$this->zone}-{$this->shelf}-{$this->slot}";
    }
}
