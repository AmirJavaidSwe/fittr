<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
