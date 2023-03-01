<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripeEvent extends Model
{
    protected $table = 'stripe_events';
    protected $guarded = [
        'id',
    ];
    protected $casts = [
        'data' => 'object',
        'livemode' => 'boolean',
    ];
}
