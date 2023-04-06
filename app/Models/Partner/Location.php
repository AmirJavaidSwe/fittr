<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static count()
 */
class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';
    protected $connection = 'mysql_partner';
    protected $guarded = ['id'];
}
