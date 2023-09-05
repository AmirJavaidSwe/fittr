<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembershipCharge extends Model
{
    use SoftDeletes;

    protected $table = 'membership_charges';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_paid' => 'boolean',
        'amount_due' => 'integer',
        'amount_paid' => 'integer',
        'amount_remaining' => 'integer',
        'discount' => 'integer',
        'subtotal' => 'integer',
        'total' => 'integer',
        'application_fee_amount' => 'integer',
        'period_start' => 'integer',
        'period_end' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    // protected $appends = [
        
    // ];

    // Local scopes


    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    // Accessors
}