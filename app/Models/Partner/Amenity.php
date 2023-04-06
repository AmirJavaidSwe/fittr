<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static count()
 */
class Amenity extends Model
{
    use HasFactory;

    protected $table = 'amenities';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    //Relationships
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Studio::class, 'studio_id');
    }
}
