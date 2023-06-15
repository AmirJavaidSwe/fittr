<?php

namespace App\Models\Partner;

use App\Models\Country;
use App\Models\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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

     /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'location_amenities', 'location_id', 'amenity_id');
    }

    public function studios(): HasMany
    {
        return $this->hasMany(Studio::class);
    }

    public function class_type_studios(): HasMany
    {
        return $this->hasMany(ClassTypeStudio::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id', 'id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

     //Local scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('status', true);
    }
}
