<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creditpack extends Model
{
    use HasFactory;

    protected $table = 'creditpacks';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

}
