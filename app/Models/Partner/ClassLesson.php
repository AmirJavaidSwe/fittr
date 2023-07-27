<?php

namespace App\Models\Partner;

use App\Enums\ClassStatus;
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
}
