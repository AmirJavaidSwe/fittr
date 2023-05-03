<?php

namespace App\Models\Partner;

use App\Enums\ClassStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ClassLesson extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'classes';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    protected $casts = [
        'is_offpeak' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'deleted_at' => 'datetime',
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

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }

    public function classType(): BelongsTo
    {
        return $this->belongsTo(ClassType::class);
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
