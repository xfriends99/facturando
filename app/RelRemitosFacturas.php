<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class RelRemitosFacturas extends Model
{
    protected $table = 'rel_remitos_facturas';
	protected $fillable = array('remito_id','factura_id');
	 public $timestamps = false;
	
	public function remitos()
	{
		return $this->belongsTo('app\InvoiceHead','remito_id');
	}

	public function facturas()
	{
		return $this->belongsTo('app\InvoiceHead','factura_id');
	}
	
}