<?php

namespace App\Models\Partner;

use App\Enums\PartnerUserRole;
use Illuminate\Support\Str;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Services\Shared\CacheMasterService;
use App\Enums\SettingKey;

class MemberFamily extends Model
{
    use HasProfilePhoto;

    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_partner';
    protected $table = 'member_family';

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
