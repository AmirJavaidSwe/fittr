<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classtype extends Model
{
    use HasFactory;

    protected $table = 'classtypes';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];

}
