<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notification_templates';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

     /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'unsubscribe' => 'boolean',
        'bypass' => 'boolean',
        'status' => 'boolean',
        'placeholders' => 'array',
        'deleted_at' => 'datetime',
    ];

    //Local scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('status', true);
    }
}
