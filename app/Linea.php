<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Linea extends Model
{

    protected $connection = 'mysql2';
    protected $table = 'ps_order_detail';
    public $timestamps = false;

    public function producto()
	{
		return $this->belongsTo('app\Product','product_id','id_product');
	}

   

}