<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kampus extends Model
{
    use HasFactory;

    protected $primaryKey = 'kampus_id';

    protected $table='kampus';

    public $timestamps = false;
}
