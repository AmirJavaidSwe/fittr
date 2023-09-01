<?php

namespace App\Models\Partner;

use App\Enums\StripeCurrency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use SoftDeletes;

    protected $table = 'order_items';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_processed' => 'boolean',
        'quantity' => 'integer',
        'amount_discount' => 'integer',
        'amount_subtotal' => 'integer',
        'amount_total' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'currency_symbol',
        'unit_amount_formatted',
        'amount_discount_formatted',
        'amount_subtotal_formatted',
        'amount_total_formatted',
    ];

    // Local scopes
    
    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function price_stripe(): BelongsTo
    {
        return $this->belongsTo(PackPrice::class, 'stripe_price_id', 'stripe_price_id')->withTrashed();
    }

    public function pack_price(): BelongsTo
    {
        return $this->belongsTo(PackPrice::class, 'pack_price_id')->withTrashed();
    }

    public function membership(): HasOne
    {
        return $this->hasOne(Membership::class);
    }

    // Accessors
    public function getCurrencySymbolAttribute(): ?string
    {
        return StripeCurrency::from($this->currency)->symbol();
    }

    public function getUnitAmountFormattedAttribute(): ?string
    {
        return $this->currency_symbol.number_format($this->unit_amount/100, 2);
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
