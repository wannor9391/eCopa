<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pencegahan extends Model
{
    use HasFactory;

    protected $table='pencegahan';

    protected $primaryKey = 'id_pencegahan';

    public $timestamps = false;

}
