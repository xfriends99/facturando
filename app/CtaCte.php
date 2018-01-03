<?php namespace app;

use Illuminate\Database\Eloquent\Model;

class CtaCte extends Model
{


    protected $table = 'cta_ctes';

	protected $fillable = array('invoice_head_id','saldo');

	public $timestamps = false;

	public function pagos()
	{
		return $this->hasMany('app\Pago','cta_ctes_id');
	}

	public function facturas()
	{
		return $this->belongsTo('app\InvoiceHead','invoice_head_id');
	}

    	public function saldos()
	{
		return $this->hasMany('app\Saldo','cta_ctes_id');
	}


}
