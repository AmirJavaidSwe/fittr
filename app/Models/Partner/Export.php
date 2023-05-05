<?php

namespace App\Models\Partner;

use App\Enums\ExportStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * @property mixed $type
 * @property mixed $filters
 * @property mixed $created_by
 * @property mixed $id
 * @property string $status
 * @property mixed $file_name
 * @property mixed $file_type
 */
class Export extends Model
{
    use SoftDeletes;

    protected $table = 'exports';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    protected $casts = [
        'filters' => 'array',
        'completed_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::deleted(function ($menu_model) {
            // TODO: delete file
        });
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function setStatusPending(): void
    {
        $this->status = ExportStatus::pending->name;
        $this->saveQuietly();
    }

    public function setStatusProcessing(): void
    {
        $this->status = ExportStatus::processing->name;
        $this->saveQuietly();
    }

    public function setStatusCompleted(): void
    {
        $this->status = ExportStatus::completed->name;
        $this->completed_at = Carbon::now();
        $this->saveQuietly();
    }

    public function generateToken(): string
    {
        $token = base64_encode($this->id.':'.Str::random(32).':'.$this->type);

        return Cache::remember($token, 30, function () use ($token) {
            return $token;
        });
    }
}
