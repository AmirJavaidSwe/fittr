<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pack extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'packs';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'restrictions' => 'array',
        'is_active' => 'boolean',
        'is_restricted' => 'boolean',
        'is_private' => 'boolean',
        'active_from' => 'datetime',
        'active_to' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'not_yet_active',
        'became_inactive',
        'will_become_inactive',
    ];

    // Relationships
    public function pack_prices(): MorphMany
    {
        return $this->morphMany(PackPrice::class, 'priceable');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    // Accessors
    public function getNotYetActiveAttribute(): ?bool
    {
        return !empty($this->active_from) && $this->active_from->isFuture() ? true : null;
    }

    public function getBecameInactiveAttribute(): ?bool
    {
        return !empty($this->active_to) && $this->active_to->isPast() ? true : null;
    }

    public function getWillBecomeInactiveAttribute(): ?bool
    {
        return !empty($this->active_to) && $this->active_to->isFuture() ? true : null;
    }

    // Local scopes
    public function scopeActive(Builder $query): void
    {
        $query
            ->where('is_active', true)
            ->where(function (Builder $query) {
                $query->whereNull('active_from')
                      ->orWhereDate('active_from', '<=', today());
            })
            ->where(function (Builder $query) {
                $query->whereNull('active_to')
                      ->orWhereDate('active_to', '>=', today());
            });
    }

    public function scopePrivate(Builder $query): void
    {
        $query->where('is_private', true)->whereNotNull('private_url');
    }

    public function scopePublic(Builder $query): void
    {
        $query->where('is_private', false);
    }
}
