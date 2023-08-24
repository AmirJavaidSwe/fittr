<?php

namespace App\Models\Partner;

use App\Enums\StripeCurrency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [

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
    
    // Accessors
   
}
