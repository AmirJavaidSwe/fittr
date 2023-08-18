<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Business extends Model
{
    use SoftDeletes;

    protected $table = 'businesses';
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'stripe_customer_id',
        'stripe_account_id',
        'db_host',
        'db_port',
        'db_name',
        'db_user',
        'db_password',
    ];

    //Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'business_id');
    }

    public function settings(): HasMany
    {
        return $this->hasMany(BusinessSetting::class, 'business_id');
    }

    public function name(): HasOne
    {
        return $this->hasOne(BusinessSetting::class, 'business_id')->where('key', 'business_name');
    }

    // Accessors
    public function getHasStripeAccountAttribute()
    {
        return !empty($this->stripe_account_id);
    }

    public function getHasStripeCustomerAttribute()
    {
        return !empty($this->stripe_customer_id);
    }
}
