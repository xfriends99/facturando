<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Produccion extends Model
{


        protected $table = 'produccion';

public function users()
	{
		return $this->belongsTo('app\User');
	}

}
