<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Studio extends Model
{
    use HasFactory;

    protected $table = 'studios';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    //Relationships
    public function amenities(): HasMany
    {
        return $this->hasMany(Amenity::class, 'studio_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function class_type_studios(): HasMany
    {
        return $this->hasMany(ClassTypeStudio::class);
    }
}
