<?php

namespace App\Models;

use App\Models\Partner\Location;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'images';
    protected $guarded = [
        'id',
    ];

    protected $appends = ['url'];

    protected function url(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return Storage::disk($attributes['disk'])->url($attributes['path'].'/'.$attributes['filename']);
            },
        );
    }

    public function imageable()
    {
        // will return either a App\Models\Partner\Pack or other morphable model as listed under App\Providers\AppServiceProvider
        return $this->morphTo();
    }
}
