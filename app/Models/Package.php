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
        //mysql returns decimals as strings
        'tx_percent' => 'float',
        'tx_fixed_fee' => 'float',
        'fee_annually' => 'float',
        'fee_monthly' => 'float',
        'monthly_fee_sites' => 'float',
        'admin_users' => 'integer',
        'max_sites' => 'integer',
    ];
}
