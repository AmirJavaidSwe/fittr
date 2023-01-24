<?php

namespace App\Models;

use App\Enums\Subscription as Enum;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use SoftDeletes;

    protected $table = 'subscriptions';
    protected $guarded = [
        'id',
    ];
    protected $casts = [
        'package_id' => 'integer',
        'partner_id' => 'integer',
        //mysql returns decimals as strings
        'tx_percent' => 'float',
        'tx_fixed_fee' => 'float',
        'fee' => 'float',
        'monthly_fee_sites' => 'float',
        'admin_users' => 'integer',
        'max_sites' => 'integer',
        'cancelled_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_free',
    ];

    //Relationships
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    //Local scopes
    public function scopeActive($query)
    {
        $query->where('status', Enum::ACTIVE->value);
    }

    // Accessors
    public function getIsFreeAttribute()
    {
        return $this->fee == 0;
    }
}
