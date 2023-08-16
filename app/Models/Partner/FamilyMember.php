<?php

namespace App\Models\Partner;

use App\Enums\PartnerUserRole;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Jetstream\HasProfilePhoto;

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
        'name',
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
        'profile_photo_url',
        'dashboard_route',
    ];

    /**
     * Interact with the user's first name.
     */
    protected function dateOfBirth(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => date('Y-m-d', strtotime($value)),
            set: fn (string $value) => date('Y-m-d', strtotime($value)),
        );
    }

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
        return Str::of($this->name)->upper()->explode(' ')->reduce(function (?string $carry, string $item) {
            return $carry .''. mb_substr($item, 0, 1);
        });
    }

    //Local scopes
    public function scopeMember($query)
    {
        $query->where('role', PartnerUserRole::MEMBER->value);
    }
}
