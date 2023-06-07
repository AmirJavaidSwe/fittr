<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PackPrice extends Model
{
    use SoftDeletes;

    protected $table = 'pack_prices';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'float',//mysql returns decimals as strings
        'is_active' => 'boolean',
        'is_expiring' => 'boolean',
        'is_ongoing' => 'boolean',
        'sessions' => 'integer',
        'expiration' => 'integer',
        'unit_amount' => 'integer',
        'interval_count' => 'integer',
        'fixed_count' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    // protected $appends = [];

    // Relationships
    public function priceable(): MorphTo
    {
        return $this->morphTo();
    }

    // Accessors


    //Local scopes

}
