<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
        protected $table = 'saldos';


	public function medioPago()
	{
		return $this->belongsTo('app\MedioPago','medios_pagos_id');
	}
	
         public function user()
	{
		return $this->belongsTo('app\User');
	}

	
}