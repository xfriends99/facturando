<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

        protected $connection = 'mysql2';

        protected $table = 'ps_product';

        protected $primaryKey = 'id_product';

        public $timestamps = false;

  public function costo()
	{
		return $this->belongsTo('app\Costo','id_product','id_product');
	}	

  public function nombre()
	{
		return $this->belongsTo('app\Lengua','id_product','id_product');
	}	

	
}