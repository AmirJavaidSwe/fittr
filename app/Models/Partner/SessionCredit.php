<?php

namespace App\Models\Partner;

use App\Enums\StateType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SessionCredit extends Model
{
    use SoftDeletes;

    protected $table = 'session_credits';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'restrictions' => 'array',
        'price_value' => 'float',
        'expiry_at' => 'datetime',
        'used_at' => 'datetime',
        'refunded_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_active' => 'boolean', //appended
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_active',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
    public function order_item(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    // Accessors
    public function getIsActiveAttribute(): bool
    {
        if($this->status != StateType::get('active')){
            return false;
        }

        if(!empty($this->used_at)){
            return false;
        }

        if(!empty($this->expiry_at) && $this->expiry_at->isPast()){
            return false;
        }

        return true;
    }

    // Local scopes

}
