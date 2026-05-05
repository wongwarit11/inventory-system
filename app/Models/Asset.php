<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Asset
 * @package App\Models
 * @property string $name
 * @property string $serial_number
 * @property string $description
 * @property int $department_id
 * @property string $status
 */
class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'serial_number',
        'description',
        'department_id',
        'status',
    ];

    /**
     * Define the relationship with the Department model.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Define the relationship with the Borrows model.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }
}