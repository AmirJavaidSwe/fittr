<?php

namespace App\Models\Partner;

use App\Enums\BookingStatus;
use App\Enums\ClassStatus;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ClassLesson extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'classes';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    protected $casts = [
        'is_off_peak' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'deleted_at' => 'datetime',
        'original_instructors' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'status_label',
        'duration',
        'default_spaces',
        'spaces_booked',
        'spaces_left',
        'is_booked',
    ];

    //Local scopes
    public function scopeActive($query)
    {
        $query->where('status', ClassStatus::ACTIVE->value);
    }

    //Relationships
    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    public function instructor(): BelongsToMany
    {
        return $this->belongsToMany(Instructor::class, 'class_instructor', 'class_id', 'instructor_id');
    }

    public function classType(): BelongsTo
    {
        return $this->belongsTo(ClassType::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'class_id', 'id');
    }

    public function waitlists(): HasMany
    {
        return $this->hasMany(Booking::class, 'class_id', 'id')->waitlisted();
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        $value = match($this->status) {
            ClassStatus::ACTIVE->value => __(ClassStatus::ACTIVE->value),
            ClassStatus::INACTIVE->value =>  __(ClassStatus::INACTIVE->value),
            ClassStatus::CANCELLED->value =>  __(ClassStatus::CANCELLED->value),
        };

        return Str::ucfirst($value);
    }

    public function getDurationAttribute(): ?int
    {
        // 2nd param false, returns negative value when end_date on is greater than the compared start_date
        return $this->start_date->diffInMinutes($this->end_date, false);
    }

    public function getDefaultSpacesAttribute(): ?int
    {
        return $this->studio?->class_type_studios?->where('class_type_id', $this->class_type_id)?->first()?->spaces;
    }

    public function getSpacesBookedAttribute(): int
    {
        if(!$this->relationLoaded('bookings')) return 0;
            // $this->load(['bookings' => function (Builder $query) {
            //     $query->active();
            // }]);
        return $this->bookings->count();
    }

    public function getSpacesLeftAttribute(): int
    {
        $defaultSpaces = $this->getAttribute('default_spaces') ?? 0;
        return ($this->spaces ?? $defaultSpaces) - $this->getAttribute('spaces_booked');
    }

    public function getIsBookedAttribute(): bool
    {
        if(!$this->relationLoaded('bookings')) return false;
            // $this->load(['bookings' => function (Builder $query) {
            //     $query->active();
            // }]);
        return $this->bookings->contains('user_id', auth()->user()?->id);
    }
}
