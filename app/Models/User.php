<?php

namespace App\Models;

use App\Enums\AppUserSource;
use App\Models\Partner\Export;
use App\Traits\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Traits\Jetstream\HasProfilePhoto;
use App\Traits\MustVerifyEmail as MustVerifyEmailTrait;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Database\Eloquent\Builder;


// use DateTimeInterface;

/**
 * @method static partner()
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;
    use MustVerifyEmailTrait;
    use CanResetPasswordTrait;

    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql'; //must be set for cross DB relationships
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'email_verified_at',
        'is_super',
        'source',
        'business_id'
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
        'is_super' => 'boolean',
        'email_verified_at' => 'datetime',
        'deleted_at' => 'datetime',
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
        'active_subscription',
        'is_partner',
        'user_roles',
        'role_permissions'
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
        return $this->source == AppUserSource::get('partner');
    }

    public function getIsAdminAttribute()
    {
        return $this->source == AppUserSource::get('admin');
    }

    public function getDashboardRouteAttribute()
    {
        return $this->is_admin ? 'admin.dashboard' : 'partner.dashboard';
    }

    public function getActiveSubscriptionAttribute()
    {
        return $this->subscriptions()->where('status', 1)->whereNull('cancelled_at')->first();
    }

    protected function getUserRolesAttribute()
    {
        return $this->roles()->pluck('title', 'slug')->toArray();
    }

    public function getInitialsAttribute()
    {
        return Str::of($this->first_name)->upper()->substr(0, 1).Str::of($this->last_name)->upper()->substr(0, 1);
    }

    public function getFullNameAttribute()
    {
        return Str::of($this->first_name)->ucfirst()->append(' ').Str::of($this->last_name)->ucfirst();
    }

    //Local scopes
    public function scopeAdmin($query)
    {
        $query->where('source', AppUserSource::get('admin'));
    }

    public function scopePartner($query)
    {
        $query->where('source', AppUserSource::get('partner'));
    }

    // public function scopeDatabaseless($query)
    // {
    //     $query->whereNull('db_name');
    // }

    //Relationships
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'partner_id');
    }

    // public function instance()
    // {
    //     return $this->hasOne(Instance::class, 'partner_id');
    // }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function exports()
    {
        return $this->hasMany(Export::class, 'created_by');
    }

    public function roles(){
        return $this->belongsToMany(Role::class);
    }

    public function getRolePermissionsAttribute()
    {
        $for = $this->source;
        $systemModules = SystemModule::where('is_for', $for)->get();

        $permissionsArray = [];

        foreach ($systemModules as $sysModule) {
            $rolesWithPermissions = [];

            foreach ($this->roles as $role) {
                $permissions = $role->permissions->where('system_module_id', $sysModule->id)->pluck('slug')->toArray();
                $rolesWithPermissions[$role->slug] = $permissions;
            }
            $permissionsArray[$sysModule->slug] = $rolesWithPermissions;
        }
        return $permissionsArray;
    }
}
