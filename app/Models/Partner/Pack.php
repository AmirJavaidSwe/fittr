<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Pack extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'packs';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'restrictions' => 'array',
        'is_active' => 'boolean',
        'is_restricted' => 'boolean',
        'is_unlimited' => 'boolean',
        'is_fap' => 'boolean',
        'is_renewable' => 'boolean',
        'is_intro' => 'boolean',
        'is_private' => 'boolean',
        'active_from' => 'datetime',
        'active_to' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
 
    ];

    // Relationships
    public function prices(): MorphMany
    {
        return $this->morphMany(PackPrice::class, 'priceable');
    }

    // Accessors

    // Local scopes
}
