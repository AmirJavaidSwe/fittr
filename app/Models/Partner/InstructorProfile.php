<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class InstructorProfile extends Model
{
    use HasFactory;

    protected $table = 'instructor_profiles';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    //Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('id', 'desc');
    }
}
