<?php

namespace App\Models\Partner;

use App\Models\Country;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static count()
 */
class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'locations';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    public function amenities() {
        return $this->belongsToMany(Amenity::class, 'location_amenities', 'location_id', 'amenity_id');
    }

    public function class_type_studios() {
        return $this->hasMany(ClassTypeStudio::class);
    }

    public function manager() {
        return $this->belongsTo(User::class, 'manager_id', 'id');
    }

    public function country() {
        return $this->belongsTo(Country::class);
    }

    public function images() {
        return $this->morphMany(Image::class, 'imageable');
    }
}
