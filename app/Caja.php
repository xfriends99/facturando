<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
        protected $table = 'caja';


	public function concepto()
	{
		return $this->belongsTo('app\ConceptosCaja','conceptos_caja_id');
	}
         public function users()
	{
		return $this->belongsTo('app\User');
	}

	
}