<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gred extends Model
{
    use HasFactory;

    protected $primaryKey = 'gred_id';

    protected $table='gred';

    public $timestamps = false;

    protected $keyType = 'string';

}
