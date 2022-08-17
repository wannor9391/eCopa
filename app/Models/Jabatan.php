<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $primaryKey = 'ptj_code';

    protected $table='jabatan';

    public $timestamps = false;

    protected $keyType = 'string';


}
