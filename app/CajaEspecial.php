<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class CajaEspecial extends Model
{
        protected $table = 'caja_especial';


	public function concepto()
	{
		return $this->belongsTo('app\ConceptosCajaEspecial','conceptos_caja_id');
	}
         public function users()
	{
		return $this->belongsTo('app\User');
	}

	
}