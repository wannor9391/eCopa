<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembetulan extends Model
{
    use HasFactory;

    protected $table='pembetulan';

    protected $primaryKey = 'id_pembetulan';

    public $timestamps = false;
}
