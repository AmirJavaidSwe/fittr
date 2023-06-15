<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassTypeStudio extends Model
{
    use HasFactory;

    protected $table = 'class_type_studios';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];
}
