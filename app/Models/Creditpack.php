<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creditpack extends Model
{
    use HasFactory;

    protected $table = 'creditpacks';
    protected $guarded = ['id'];

}
