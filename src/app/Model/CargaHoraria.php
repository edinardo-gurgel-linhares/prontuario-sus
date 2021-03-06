<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CargaHoraria extends Model
{
    public $timestamps = false;

    protected $fillable = [
    	'medico_id',
    	'intervalo',
    	'inicio',
    	'fim',
    ];

    public function medico()
    {
    	return $this->belongsTo(Usuario::class, 'medico_id', 'id');
    }
}
