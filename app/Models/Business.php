<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    use SoftDeletes;

    protected $table = 'businesses';
    protected $guarded = [
        'id',
    ];

    //Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'business_id');
    }
}
