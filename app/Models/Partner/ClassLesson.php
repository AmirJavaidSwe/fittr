<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static create(array $array)
 * @method static orderBy(string $string, string $string1)
 * @method static select(string[] $array)
 * @method static count()
 * @method static distinct(string $string)
 */
class ClassLesson extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'classes';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    protected $casts = [
        'week_days' => 'array',
        'deleted_at' => 'datetime',
    ];

    const WEEK_DAYS = [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
    ];

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }
}
