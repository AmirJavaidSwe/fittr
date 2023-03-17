<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerSetting extends Model
{
    protected $table = 'partner_settings';
    protected $guarded = [
        'id',
    ];
    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    //Relationships
    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    //Local scopes
    public function scopeLoggedIn($query)
    {
        $query->where('partner_id', auth()->user()->id);
    }

    public function scopeOfGroup($query, $group)
    {
        return $query->where('group_name', $group->name);
    }
}
