<?php

namespace App\Models\Partner;

use App\Enums\StripeCurrency;
use App\Models\StripeEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $table = 'orders';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount_subtotal' => 'integer',
        'amount_total' => 'integer',
        'line_items_pulled' => 'boolean',
        'line_items' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'currency_symbol',
        'amount_discount_formatted',
        'amount_subtotal_formatted',
        'amount_total_formatted',
    ];

    // Local scopes
    
    // Relationships
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stripe_event(): BelongsTo
    {
        return $this->belongsTo(StripeEvent::class);
    }
    
    // Accessors
    public function getCurrencySymbolAttribute(): ?string
    {
        return StripeCurrency::from($this->currency)->symbol();
    }

    public function getAmountDiscountFormattedAttribute(): ?string
    {
        return $this->currency_symbol.number_format($this->amount_discount/100, 2);
    }

    public function getAmountSubtotalFormattedAttribute(): ?string
    {
        return $this->currency_symbol.number_format($this->amount_subtotal/100, 2);
    }

    public function getAmountTotalFormattedAttribute(): ?string
    {
        return $this->currency_symbol.number_format($this->amount_total/100, 2);
    }
}
