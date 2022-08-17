<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KampusUsers extends Model
{
    use HasFactory;

    protected $table='kampus_users';

    public $timestamps = false;
}
