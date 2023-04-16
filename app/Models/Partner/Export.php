<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Export extends Model
{
    use SoftDeletes;

    protected $table = 'exports';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    protected $casts = [
        'filters' => 'array',
        'completed_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
