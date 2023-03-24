<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Instance extends Model
{
    use SoftDeletes;

    protected $table = 'instances';
    protected $guarded = [
        'id',
    ];

    //Relationships
    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }
}
