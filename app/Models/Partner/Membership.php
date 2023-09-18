<?php

namespace App\Models\Partner;

use App\Enums\PackType;
use App\Enums\StripeCurrency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Membership extends Model
{
    use SoftDeletes;

    protected $table = 'memberships';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'restrictions' => 'array',
        'is_restricted' => 'boolean',
        'price' => 'float',//mysql returns decimals as strings
        'is_expiring' => 'boolean',
        'is_ongoing' => 'boolean',
        'is_unlimited' => 'boolean',
        'is_fap' => 'boolean',
        'is_renewable' => 'boolean',
        'is_intro' => 'boolean',
        'sessions' => 'integer',
        'expiration' => 'integer',
        'interval_count' => 'integer',
        'fixed_count' => 'integer',
        'deleted_at' => 'datetime',
        'expiration_date' => 'datetime', //appended
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'taxonomy_sessions',
        'expiration_date',
        'interval_human',
        'price_floor',
        'price_floor_formatted',
        'price_decimals',
        'price_formatted',
        'price_formatted_full',
    ];

    // Local scopes
    
    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
    public function order_item(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class);
    }

    public function pack_price(): BelongsTo
    {
        return $this->belongsTo(PackPrice::class);
    }

    public function membership_charges(): HasMany
    {
        return $this->hasMany(MembershipCharge::class);
    }

    public function session_credits(): HasMany
    {
        return $this->hasMany(SessionCredit::class);
    }

    // Accessors
    public function getExpirationDateAttribute(): ?Carbon
    {
        if(!$this->is_expiring){
            return null;
        }

        //expiration date is for one_time, recurring must look for membership_charges

        $expiration_date = $this->created_at->copy();
        switch ($this->expiration_period) {
            case 'day':
                $expiration_date->addDays($this->expiration);
                break;
            case 'week':
                $expiration_date->addWeeks($this->expiration);
                break;
            case 'month':
                $expiration_date->addMonths($this->expiration);
                break;
            case 'year':
                $expiration_date->addYears($this->expiration);
                break;
        }

        return $expiration_date;
    }

    public function getTaxonomySessionsAttribute(): ?string
    {
        $type_label = PackType::label($this->type);
        return Str::plural($type_label, $this->sessions);
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
}
