<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use SoftDeletes;

    protected $table = 'packages';
    protected $guarded = [
        'id',
    ];
    protected $casts = [
        'status' => 'boolean',
        'is_private' => 'boolean',
        //mysql returns decimals as strings
        'tx_percent' => 'float',
        'tx_fixed_fee' => 'float',
        'fee_annually' => 'float',
        'fee_monthly' => 'float',
        'monthly_fee_sites' => 'float',
        'admin_users' => 'integer',
        'max_sites' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_free',
        'annual_price_year',
        'monthly_price_year',
        'annual_savings',
    ];

    //Local scopes
    public function scopePrivate($query)
    {
        $query->where('is_private', true);
    }

    public function scopePublic($query)
    {
        $query->where('is_private', false);
    }

    // Accessors
    public function getIsFreeAttribute()
    {
        return $this->fee_monthly == 0 && $this->fee_annually == 0;
    }

    // Full subscription price for one full year, with *annual* billing cycle
    public function getAnnualPriceYearAttribute()
    {
        return $this->fee_annually * 12;
    }

    // Full subscription price for one full year, with *monthly* billing cycle
    public function getMonthlyPriceYearAttribute()
    {
        return $this->fee_monthly * 12;
    }

    // Difference/savings when paid for a year, compared to month-to-month
    public function getAnnualSavingsAttribute()
    {
        return $this->monthly_price_year - $this->annual_price_year;
    }
}
