<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassType extends Model
{
    use HasFactory;

    protected $table = 'class_types';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucwords($value),
            set: fn (string $value) => strtolower($value),
        );
    }
}
