<?php

namespace App\Models\Partner;

use App\Enums\PartnerUserRole;
use App\Traits\Jetstream\HasProfilePhoto;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * @method static orderBy(string $string, string $order)
 * @method static create($all)
 */
class Instructor extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use SoftDeletes;
    use Notifiable;
    use HasProfilePhoto;

    protected $table = 'users';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::saving(function ($instructor) {
            $instructor->role = PartnerUserRole::INSTRUCTOR->value;
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'stripe_id',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'two_factor_confirmed_at',
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
    ];

    protected function newBaseQueryBuilder(): Builder
    {
        return parent::newBaseQueryBuilder()->where('role', PartnerUserRole::INSTRUCTOR->value);
    }

     // Accessors
     public function getInitialsAttribute()
     {
         return Str::of($this->first_name)->upper()->substr(0, 1).Str::of($this->last_name)->upper()->substr(0, 1);
     }
 
     public function getFullNameAttribute()
     {
         return Str::of($this->first_name)->ucfirst()->append(' ').Str::of($this->last_name)->ucfirst();
     }

    public function profile(): HasOne
    {
        return $this->hasOne(InstructorProfile::class, 'user_id');
    }
}
