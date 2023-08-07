<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

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
        'is_unlimited' => 'boolean',
        'is_fap' => 'boolean',
        'is_renewable' => 'boolean',
        'is_intro' => 'boolean',
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
    protected $appends = [
        'taxonomy_sessions',
        'interval_human',
        'price_floor',
        'price_floor_formatted',
        'price_decimals',
        'price_formatted',
        'price_formatted_full',
    ];

    // Relationships
    public function priceable(): MorphTo
    {
        // will return either a App\Models\Partner\Pack or other morphable model as listed under App\Providers\AppServiceProvider
        return $this->morphTo();
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class);
    }

    // Accessors
    public function getTaxonomySessionsAttribute(): ?string
    {
        //TODO add Taxonomy (terms) settings for partner to set: (Hardwired to class)
        return Str::plural(__('class'), $this->sessions);
    }

    public function getIntervalHumanAttribute(): ?string
    {
        if(empty($this->interval_count)){
            return null;
        }
        $string = $this->interval_count == 1 ? __('per') : __('every');
        $string .= ($this->interval_count == 1 ? '' : ' '.$this->interval_count).' '. __(Str::plural($this->interval, $this->interval_count));
        return $string;
    }

    public function getPriceFloorAttribute(): int
    {
        return floor($this->price);
    }

    public function getPriceFloorFormattedAttribute(): ?string
    {
        return number_format($this->price_floor);
    }

    public function getPriceDecimalsAttribute(): ?int
    {
        return round(fmod($this->price, 1), 2) * 100;
    }

    public function getPriceFormattedAttribute(): ?string
    {
        return $this->currency_symbol.number_format($this->price, 2);
    }

    public function getPriceFormattedFullAttribute(): ?string
    {
        return $this->price_formatted.' '.strtoupper($this->currency);
    }

    //Local scopes

}
