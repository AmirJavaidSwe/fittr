<?php

namespace App\Models\Partner;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    protected $appends = ['status_text'];

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassLesson::class, 'class_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->member();
    }

    // Local Scopes
    public function scopeActive($query)
    {
        $query->where('status', BookingStatus::get('active'));
    }

    public function scopeInactive($query)
    {
        $query->where('status', BookingStatus::get('inactive'));
    }

    public function scopeCancelled($query)
    {
        $query->where('status', BookingStatus::get('cancelled'));
    }

    // Accessors & Mutators
    protected function statusText(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => Str::ucfirst(__($attributes['status'])),
        );
    }
}
