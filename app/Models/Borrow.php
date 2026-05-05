<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Borrow
 * @package App\Models
 * @property int $asset_id
 * @property int $user_id
 * @property string $borrowed_at
 * @property string $returned_at
 */
class Borrow extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'user_id',
        'borrowed_at',
        'returned_at',
    ];

    /**
     * Define the relationship with the Asset model.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Define the relationship with the User model.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
