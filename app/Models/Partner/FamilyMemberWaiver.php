<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FamilyMemberWaiver extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'family_member_waivers';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'deleted_at' => 'datetime',
        'user_waiver_accepted_data' => 'array',
    ];
}
