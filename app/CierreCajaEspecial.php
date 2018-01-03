<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class CierreCajaEspecial extends Model
{
        
        protected $table = 'cierre_caja_especial';
        public function users()
	{
		return $this->belongsTo('app\User','users_id');
	}

	
}