<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $table = 'business_settings';
    protected $guarded = [
        'id',
    ];
    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    //Relationships
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    //Local scopes
    public function scopeOfBusiness($query, $business_id)
    {
        $query->where('business_id', $business_id);
    }

    public function scopeOfGroup($query, $group)
    {
        return $query->where('group_name', $group->name);
    }
}
