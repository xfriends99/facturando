<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{


    protected $table = 'pagos';

	protected $fillable = array('pago','otro','medios_pagos_id','cta_ctes_id');

	public function medio_pago()
	{
		return $this->belongsTo('app\MedioPago','medios_pagos_id');
	}

	public function cta_cte()
	{
		return $this->belongsTo('app\CtaCte','cta_ctes_id');
	}

	public function users()
	{
		return $this->belongsTo('app\User','users_id');
	}

}
