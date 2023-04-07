<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * @method static count()
 */
class Amenity extends Model
{
    use HasFactory;

    protected $table = 'amenities';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'image_url',
    ];

    public function getImageUrlAttribute(): string
    {
        if(empty($this->icon)){
            return null;
        }

        return Storage::disk('public')->url('/images/amenity/'.$this->icon);

        // OR
        // return asset('/storage/images/amenity/'.$this->icon);
    }

    //Relationships
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Studio::class, 'studio_id');
    }
}
