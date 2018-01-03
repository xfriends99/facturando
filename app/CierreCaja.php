<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class CierreCaja extends Model
{
        
        protected $table = 'cierre_caja';
        public function users()
	{
		return $this->belongsTo('app\User','users_id');
	}

	
}