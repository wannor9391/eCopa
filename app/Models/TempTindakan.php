<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempTindakan extends Model
{
    use HasFactory;

    protected $table='temp_tindakan';

    public $timestamps = false;
}
