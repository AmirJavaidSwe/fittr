<?php

namespace App\Models\Partner;

use App\Enums\PartnerUserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

/**
 * @method static instructor()
 * @method static member()
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;

    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_partner';
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'dashboard_route',
    ];

    // Accessors
    public function getIsMemberAttribute(): bool
    {
        return $this->role == PartnerUserRole::MEMBER->value;
    }

    public function getIsInstructorAttribute(): bool
    {
        return $this->role == PartnerUserRole::INSTRUCTOR->value;
    }

    public function getDashboardRouteAttribute(): string
    {
        return match($this->role) {
            PartnerUserRole::MEMBER->value => 'member.dashboard',
            PartnerUserRole::INSTRUCTOR->value => 'instructor.dashboard'
        };
    }

    //Local scopes
    public function scopeMember($query)
    {
        $query->where('role', PartnerUserRole::MEMBER->value);
    }

    public function scopeInstructor($query)
    {
        $query->where('role', PartnerUserRole::INSTRUCTOR->value);
    }
}
