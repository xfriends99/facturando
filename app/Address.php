<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
        
        protected $connection = 'mysql2';

        protected $table = 'ps_address';
	
        public $timestamps = false;
	

	public function state()
	{
		return $this->belongsTo('app\State','id_state','id_state');
	}

	
}