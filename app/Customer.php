<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

    protected $connection = 'mysql2';

    protected $table = 'ps_customer';
    
    protected $primaryKey = "id_customer";

    public $timestamps = false;

    public function pedidos()
	{
		return $this->hasMany('app\Pedido','id_customer','id_customer');
	}



  
	
}