<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class MedioPago extends Model
{


    protected $table = 'medios_pagos';

	protected $fillable = array('tipo');

	public $timestamps = false;

	public function pagos()
	{
		return $this->hasMany('app\Pago','medios_pagos_id');
	}

}
