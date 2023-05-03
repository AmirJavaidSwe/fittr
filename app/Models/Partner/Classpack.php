<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classpack extends Model
{
    use HasFactory;

    protected $table = 'classpacks';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [        
        'price' => 'float',//mysql returns decimals as strings
        'restrictions' => 'array',
        'is_active' => 'boolean',
        'is_expiring' => 'boolean',
        'is_renewable' => 'boolean',
        'is_intro' => 'boolean',
        'is_private' => 'boolean',
        'is_restricted' => 'boolean',
        'active_from' => 'datetime',
        'active_to' => 'datetime',
        'deleted_at' => 'datetime',
    ];

}
