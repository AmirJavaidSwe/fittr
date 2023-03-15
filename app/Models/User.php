<?php

namespace App\Models;

use App\Enums\User as Enum;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
// use DateTimeInterface;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;

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
        'stripe_customer_id',
        'stripe_account_id',
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
        'active_subscription',
        'is_partner',
    ];

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    // protected function serializeDate(DateTimeInterface $date)
    // {
    //     return $date->format('Y-m-d H:i:s');
    // }

    // Accessors
    public function getIsPartnerAttribute()
    {
        return $this->role == Enum::PARTNER->value;
    }

    public function getIsAdminAttribute()
    {
        return $this->role == Enum::ADMIN->value;
    }

    public function getDashboardRouteAttribute()
    {
        return $this->is_admin ? 'admin.dashboard' : 'partner.dashboard';
    }

    public function getActiveSubscriptionAttribute()
    {
        return $this->subscriptions()->where('status', 1)->whereNull('cancelled_at')->first();
    }

    public function getHasStripeCustomerAttribute()
    {
        return !empty($this->stripe_customer_id);
    }

    public function getHasStripeAccountAttribute()
    {
        return !empty($this->stripe_account_id);
    }

    //Local scopes
    public function scopeAdmin($query)
    {
        $query->where('role', Enum::ADMIN->value);
    }

    public function scopePartner($query)
    {
        $query->where('role', Enum::PARTNER->value);
    }

    //Relationships
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'partner_id');
    }

    public function instance()
    {
        return $this->hasOne(Instance::class, 'partner_id');
    }
}
