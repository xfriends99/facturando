<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{

    protected $connection = 'mysql2';

    protected $table = 'ps_orders';
       
    public $timestamps = false;

    public $primaryKey = 'id_order';

 
    public function customer()
	{
		return $this->belongsTo('app\Customer','id_customer','id_customer');
	}

    public function direccion_entrega()
	{
		return $this->belongsTo('app\Address','id_address_delivery','id_address');
	}

     public function direccion_factura()
	{
		return $this->belongsTo('app\Address','id_address_invoice','id_address');
	}

     public function lineas()
	{
		return $this->hasMany('app\Linea','id_order','id_order');
	}



}