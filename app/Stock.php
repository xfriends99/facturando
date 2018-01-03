<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{

    protected $connection = 'mysql2';

    protected $table = 'ps_stock_available';
   
    protected $primaryKey = 'id_product';
       
    public $timestamps = false;

    public function producto()
	{
		return $this->belongsTo('app\Product','id_product','id_product');
	}


  
	
}