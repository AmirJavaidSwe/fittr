<?php

namespace App\Models\Partner;

use App\Enums\PartnerUserRole;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\Jetstream\HasProfilePhoto;

class FamilyMember extends Model
{
    use HasProfilePhoto;
    use SoftDeletes;

    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_partner';
    protected $table = 'family_members';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date_of_birth' => 'date'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'initials',
        'full_name',
        'profile_photo_url',
        'dashboard_route',
        'waivers'
    ];


    public function getDashboardRouteAttribute(): string
    {
        return match($this->role) {
            PartnerUserRole::get('member') => 'ss.member.dashboard',
            PartnerUserRole::get('instructor') => 'ss.instructor.dashboard',
            default => 'ss.home',
        };
    }

    public function getInitialsAttribute()
    {
        return Str::of($this->first_name)->upper()->substr(0, 1).Str::of($this->last_name)->upper()->substr(0, 1);
    }

    public function getFullNameAttribute()
    {
        return Str::of($this->first_name)->ucfirst()->append(' ').Str::of($this->last_name)->ucfirst();
    }

    public function getWaiversAttribute()
    {
        return Waiver::where('show_at', 'family-add')->where('is_active', 1)->get();
    }

    //Local scopes
    public function scopeMember($query)
    {
        $query->where('role', PartnerUserRole::MEMBER->value);
    }

    // Relation With Waivers
    public function userWaivers()
    {
        return $this->hasMany(UserWaiver::class, 'family_member_id');
    }
}
