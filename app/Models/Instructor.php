<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * @method static orderBy(string $string, string $order)
 * @method static create($all)
 */
class Instructor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'users';

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::saving(function ($instructor) {
            $instructor->role = UserRole::INSTRUCTOR->value;
        });
    }

    protected $hidden = [
        'role',
        'password',
        'remember_token',
    ];

    protected function newBaseQueryBuilder(): Builder
    {
        return parent::newBaseQueryBuilder()->where('role', UserRole::INSTRUCTOR->value);
    }
}
