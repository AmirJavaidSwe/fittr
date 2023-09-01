<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemModule extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'system_modules';
    protected $fillable = [
        'name', 'slug', 'is_for'
    ];

    public function permissions() {
        return $this->hasMany(Permission::class, 'system_module_id');
    }
}
