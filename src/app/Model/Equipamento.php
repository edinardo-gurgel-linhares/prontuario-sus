<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Equipamento extends Model
{
    public $timestamps = false;

    protected $fillable = [ 'nome' ];
}
