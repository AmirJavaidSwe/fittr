<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StripeEvent extends Model
{
    protected $table = 'stripe_events';
    protected $guarded = [
        'id',
    ];
    protected $casts = [
        'data' => 'object',
        'livemode' => 'boolean',
        'is_processed' => 'boolean',
    ];

    //Relationships
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'connected_account', 'stripe_account_id');
    }
    
}
